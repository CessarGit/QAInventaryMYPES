#!/usr/bin/env bash
# Restaura un respaldo de scripts/respaldo.sh en una base del staging.
#
# Uso: bash scripts/restaurar.sh <archivo.sql.gz> <base_destino> [--forzar]
#
# Procedimientos que lo usan (Tabla 16 de recuperación):
#   RB-01 (falla del servidor): paso 3, restaurar la última copia de la base.
#   RB-04 (borrado indebido): paso 2, restaurar la copia anterior al error en una base
#         temporal (por ejemplo stock_restore), sin tocar stock.
#   RB-06 (actualización fallida): paso 3, restaurar la base si se modificó.
# Prueba periódica (Tabla 20): restauración mensual en stock_restore y comparación con
#   scripts/verificar-conteos.sh.
# Copias de origen: las de la Tabla 11 de recuperación, guardadas en backups/.
#
# Protecciones:
#   - Verifica la suma sha256 antes de restaurar; si no coincide, se detiene.
#   - Restaurar sobre "stock" exige --forzar.
#   - La contraseña de root se toma de MYSQL_ROOT_PASSWORD dentro del servicio db y se
#     pasa como MYSQL_PWD; no aparece en la línea de comandos ni en la salida.
#   - El volcado reemplaza las tablas que contiene (DROP TABLE IF EXISTS); las tablas de la
#     base destino que no estén en el volcado no se tocan.
set -euo pipefail

cd "$(dirname "$0")/.."

uso() { echo "Uso: bash scripts/restaurar.sh <archivo.sql.gz> <base_destino> [--forzar]" >&2; exit 1; }

[ $# -ge 2 ] && [ $# -le 3 ] || uso
archivo="$1"
base="$2"
forzar="${3:-}"
[ -z "$forzar" ] || [ "$forzar" = "--forzar" ] || uso

if [[ ! "$base" =~ ^[A-Za-z0-9_]+$ ]]; then
    echo "Nombre de base no válido: solo letras, números y guion bajo." >&2
    exit 1
fi
if [ "$base" = "stock" ] && [ "$forzar" != "--forzar" ]; then
    echo "Restaurar sobre 'stock' reemplaza los datos de producción del staging: usa --forzar." >&2
    exit 1
fi
if [ ! -f "$archivo" ] || [ ! -f "$archivo.sha256" ]; then
    echo "Falta el archivo o su suma: $archivo y $archivo.sha256" >&2
    exit 1
fi
if ! docker compose ps --status running --services 2>/dev/null | grep -qx db; then
    echo "El servicio db no está en ejecución (docker compose up -d)." >&2
    exit 1
fi

# Sobre stock, la aplicación en marcha podría registrar ventas durante la restauración y
# mezclarlas con los datos restaurados (RV-10, H-RV10-04).
if [ "$base" = "stock" ] && docker compose ps --status running --services 2>/dev/null | grep -qx app; then
    echo "Advertencia: el servicio app está en marcha. Para evitar escrituras durante la" >&2
    echo "restauración, detenlo antes con 'docker compose stop app' y vuelve a iniciarlo después." >&2
fi

echo "Verificando la suma sha256..."
( cd "$(dirname "$archivo")" && sha256sum -c --quiet "$(basename "$archivo").sha256" )
echo "Suma correcta."

echo "Restauración en '$base' · inicio: $(date '+%Y-%m-%d %H:%M:%S %Z')"

docker compose exec -T db sh -c \
    'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysql -uroot -e "CREATE DATABASE IF NOT EXISTS \`$1\` DEFAULT CHARACTER SET utf8"' \
    sh "$base"

gunzip -c "$archivo" | docker compose exec -T db sh -c \
    'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysql -uroot --default-character-set=utf8 "$1"' \
    sh "$base"

echo "Restauración en '$base' · fin:    $(date '+%Y-%m-%d %H:%M:%S %Z')"
echo "Siguiente paso: bash scripts/verificar-conteos.sh <base_origen> $base"

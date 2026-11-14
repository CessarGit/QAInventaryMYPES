#!/usr/bin/env bash
# Respaldo completo de una base de datos del staging.
#
# Uso: bash scripts/respaldo.sh [base]        (base por defecto: stock)
#
# Qué se respalda (Tabla 11 de recuperación, "Base de datos completa"): todas las tablas,
#   datos, rutinas y disparadores de la base indicada, en un volcado consistente
#   (--single-transaction, tablas InnoDB) sin sentencias CREATE DATABASE, para poder
#   restaurarlo en otra base (por ejemplo stock_restore).
# Cada cuánto: la Tabla 11 propone copias cada hora (retención 48 h), diarias (30 días) y
#   mensuales (12 meses). En staging el script se ejecuta a mano: no hay programación
#   automática (desviación registrada en RV-10).
# Dónde: backups/<base>_AAAAMMDD_HHMMSS.sql.gz y su .sha256, en el disco local. La copia en
#   otra región y la copia desconectada de la Tabla 11 no se implementan en staging.
#   backups/ está fuera de git (regla 15) y Apache no lo sirve (backups/.htaccess).
# Uso en la recuperación: es la copia que restauran RB-01, RB-04 y RB-06
#   (Tabla 16 de recuperación) con scripts/restaurar.sh.
#
# Seguridad: la contraseña de root no aparece en la línea de comandos del host ni del
#   contenedor: se toma de MYSQL_ROOT_PASSWORD dentro del servicio db (docker-compose.yml)
#   y se pasa como MYSQL_PWD. El script no imprime contraseñas ni contenido de la base.
#   Los archivos se crean legibles solo por el usuario que ejecuta el script (umask 077):
#   contienen hashes de contraseñas y datos de clientes y ventas (RV-10, H-RV10-01).
set -euo pipefail
umask 077

cd "$(dirname "$0")/.."

base="${1:-stock}"
if [[ ! "$base" =~ ^[A-Za-z0-9_]+$ ]]; then
    echo "Nombre de base no válido: solo letras, números y guion bajo." >&2
    exit 1
fi

if ! docker compose ps --status running --services 2>/dev/null | grep -qx db; then
    echo "El servicio db no está en ejecución (docker compose up -d)." >&2
    exit 1
fi

existe=$(docker compose exec -T db sh -c \
    'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysql -uroot -N -B -e "SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '"'"'$1'"'"'"' \
    sh "$base")
if [ "$existe" != "1" ]; then
    echo "La base '$base' no existe." >&2
    exit 1
fi

mkdir -p backups
marca=$(date +%Y%m%d_%H%M%S)
archivo="backups/${base}_${marca}.sql.gz"
parcial="${archivo}.partial"
trap 'rm -f "$parcial"' EXIT

echo "Respaldo de '$base' · inicio: $(date '+%Y-%m-%d %H:%M:%S %Z')"

docker compose exec -T db sh -c \
    'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysqldump -uroot --single-transaction --routines --triggers --default-character-set=utf8 "$1"' \
    sh "$base" | gzip > "$parcial"

mv "$parcial" "$archivo"
( cd backups && sha256sum "$(basename "$archivo")" > "$(basename "$archivo").sha256" )

echo "Respaldo de '$base' · fin:    $(date '+%Y-%m-%d %H:%M:%S %Z')"
echo "Archivo: $archivo ($(du -h "$archivo" | cut -f1))"
echo "Suma:    $archivo.sha256"

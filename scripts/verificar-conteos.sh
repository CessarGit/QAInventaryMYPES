#!/usr/bin/env bash
# Compara el número de registros de las 12 tablas del sistema entre dos bases.
#
# Uso: bash scripts/verificar-conteos.sh <base_a> <base_b>
#
# Criterio de éxito de la restauración (Tabla 20 de recuperación): base restaurada con el
#   mismo número de registros y sin errores. Verificación de RB-01 y RB-04 (Tabla 16 de
#   recuperación): contar productos y ventas, y comprobar que los registros coinciden.
# Tablas: las 12 de stock.sql (Tabla 2).
#
# Código de salida: 0 si todas coinciden; 1 si alguna difiere; 2 si una base no existe o no
# se pudo obtener algún conteo.
# Solo imprime conteos, nunca contenido de la base ni contraseñas.
set -euo pipefail

cd "$(dirname "$0")/.."

[ $# -eq 2 ] || { echo "Uso: bash scripts/verificar-conteos.sh <base_a> <base_b>" >&2; exit 1; }
base_a="$1"
base_b="$2"
for b in "$base_a" "$base_b"; do
    if [[ ! "$b" =~ ^[A-Za-z0-9_]+$ ]]; then
        echo "Nombre de base no válido: $b" >&2
        exit 1
    fi
done

if ! docker compose ps --status running --services 2>/dev/null | grep -qx db; then
    echo "El servicio db no está en ejecución (docker compose up -d)." >&2
    exit 1
fi

tablas=(attributes attribute_value brands categories company groups orders orders_item
        products stores users user_group)

consulta_sql() {
    docker compose exec -T db sh -c \
        'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysql -uroot -N -B -e "$1"' sh "$1"
}

existe() {
    [ "$(consulta_sql "SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '$1'")" = "1" ]
}

for b in "$base_a" "$base_b"; do
    existe "$b" || { echo "La base '$b' no existe." >&2; exit 2; }
done

# Una sola consulta por base: tabla<TAB>conteo.
conteos() {
    local base="$1" sql="" t
    for t in "${tablas[@]}"; do
        [ -z "$sql" ] || sql+=" UNION ALL "
        sql+="SELECT '$t', COUNT(*) FROM \`$base\`.\`$t\`"
    done
    consulta_sql "$sql"
}

# Los conteos se guardan primero en variables: así set -e detiene el script si la consulta
# falla (en una sustitución de procesos < <(...) el fallo pasaría inadvertido; RV-10,
# H-RV10-02).
salida_a=$(conteos "$base_a") || { echo "Falló la consulta de conteos en '$base_a'." >&2; exit 2; }
salida_b=$(conteos "$base_b") || { echo "Falló la consulta de conteos en '$base_b'." >&2; exit 2; }

declare -A cuenta_a cuenta_b
while IFS=$'\t' read -r t n; do cuenta_a[$t]=$n; done <<< "$salida_a"
while IFS=$'\t' read -r t n; do cuenta_b[$t]=$n; done <<< "$salida_b"

# Un conteo ausente es un error, nunca una coincidencia.
for t in "${tablas[@]}"; do
    if [ -z "${cuenta_a[$t]:-}" ] || [ -z "${cuenta_b[$t]:-}" ]; then
        echo "No se obtuvo el conteo de la tabla '$t' en alguna de las bases." >&2
        exit 2
    fi
done

difieren=0
printf '%-16s %12s %12s  %s\n' "Tabla" "$base_a" "$base_b" "¿Igual?"
printf '%-16s %12s %12s  %s\n' "----------------" "------------" "------------" "-------"
for t in "${tablas[@]}"; do
    a="${cuenta_a[$t]:-?}"
    b="${cuenta_b[$t]:-?}"
    if [ "$a" = "$b" ]; then igual="sí"; else igual="NO"; difieren=$((difieren + 1)); fi
    printf '%-16s %12s %12s  %s\n' "$t" "$a" "$b" "$igual"
done

if [ "$difieren" -eq 0 ]; then
    echo "Resultado: las 12 tablas coinciden."
    exit 0
fi
echo "Resultado: $difieren tabla(s) difieren."
exit 1

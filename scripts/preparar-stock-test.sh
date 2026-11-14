#!/usr/bin/env bash
# Recrea la base de pruebas stock_test con stock.sql y los datos PC-0 (subpaso 4.4).
#
# Uso: bash scripts/preparar-stock-test.sh [stock_test]
#
# Regla 14: las pruebas usan solo stock_test; este script se niega a operar sobre otra base.
# Regla 16: los datos salen de application/tests/_datos/pc0.sql (mapeo de RV-01).
# Contraseña de los usuarios de prueba: TEST_USER_PASS del .env (solo se lee esa línea). El
# hash se genera dentro del contenedor app con password_hash(..., PASSWORD_DEFAULT), la
# función de application/controllers/Users.php:91. No se imprime ni se guarda en el repo.
set -euo pipefail

cd "$(dirname "$0")/.."

base="${1:-stock_test}"
if [ "$base" != "stock_test" ]; then
    echo "Solo opera sobre stock_test (regla 14); se recibió '$base'." >&2
    exit 1
fi
for s in db app; do
    docker compose ps --status running --services 2>/dev/null | grep -qx "$s" \
        || { echo "El servicio $s no está en ejecución (docker compose up -d)." >&2; exit 1; }
done

TEST_USER_PASS=$(grep -E '^TEST_USER_PASS=' .env 2>/dev/null | head -1 | cut -d= -f2- || true)
if [ -z "$TEST_USER_PASS" ]; then
    echo "Falta TEST_USER_PASS en .env (ver .env.example)." >&2
    exit 1
fi
export TEST_USER_PASS
hash=$(docker compose exec -T -e TEST_USER_PASS app \
    php -r 'echo password_hash(getenv("TEST_USER_PASS"), PASSWORD_DEFAULT);')
unset TEST_USER_PASS
[[ "$hash" =~ ^\$2y\$ ]] || { echo "No se pudo generar el hash." >&2; exit 1; }

sql() { docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysql -uroot "$@"' sh "$@"; }

echo "Recreando $base..."
sql -e "DROP DATABASE IF EXISTS \`$base\`; CREATE DATABASE \`$base\` DEFAULT CHARACTER SET utf8;"
docker compose exec -T db sh -c \
    'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysql -uroot -e "GRANT ALL ON \`$1\`.* TO '"'"'$MYSQL_USER'"'"'@'"'"'%'"'"'"' \
    sh "$base"

echo "Cargando stock.sql y pc0.sql..."
sql --default-character-set=utf8 "$base" < stock.sql
{ printf "SET @pc0_hash = '%s';\n" "$hash"; cat application/tests/_datos/pc0.sql; } \
    | sql --default-character-set=utf8 "$base"

echo "Conteos en $base:"
sql -t "$base" -e "SELECT (SELECT COUNT(*) FROM \`groups\`) AS grupos,
    (SELECT COUNT(*) FROM users) AS usuarios, (SELECT COUNT(*) FROM user_group) AS user_group,
    (SELECT COUNT(*) FROM products) AS productos, (SELECT qty FROM products WHERE id = 10) AS stock_pc0;"

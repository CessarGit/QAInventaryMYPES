#!/usr/bin/env bash
# Genera .env a partir de .env.example con contraseñas aleatorias.
# No sobrescribe un .env existente y no imprime los valores generados.
# Tabla 13 de seguridad: "Contenedores de staging" y "Secretos en el repositorio".
set -euo pipefail

cd "$(dirname "$0")/.."

if [ -e .env ]; then
    echo "Ya existe .env; no se modifica. Bórralo a mano si quieres regenerarlo." >&2
    exit 1
fi

command -v openssl >/dev/null || { echo "Falta openssl." >&2; exit 1; }

# Alfanumérica: evita caracteres que rompan YAML, la shell o el DSN.
generar() { openssl rand -base64 48 | tr -dc 'A-Za-z0-9' | head -c 32; }

umask 077
sed \
    -e "s/^DB_PASS=.*/DB_PASS=$(generar)/" \
    -e "s/^DB_ROOT_PASS=.*/DB_ROOT_PASS=$(generar)/" \
    -e "s/^HOST_UID=.*/HOST_UID=$(id -u)/" \
    -e "s/^HOST_GID=.*/HOST_GID=$(id -g)/" \
    .env.example > .env

echo ".env creado (permisos 600). Copia su contenido a tu gestor de contraseñas."

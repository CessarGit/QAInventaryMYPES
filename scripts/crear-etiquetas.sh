#!/usr/bin/env bash
# Crea o actualiza las etiquetas de categoría y severidad en GitHub.
# Se puede repetir: --force actualiza la etiqueta si ya existe.
#
# Respaldo: Figura 1 de mantenimiento (clasificación por categoría y severidad),
# Tabla 11 de mantenimiento (categorías), Tabla 12 de mantenimiento (issues con
# etiquetas de categoría y severidad), Tabla 13 de mantenimiento (métricas que
# se calculan a partir de las etiquetas) y Tabla 17, punto 7.
#
# Uso: bash scripts/crear-etiquetas.sh [propietario/repositorio]
# Sin argumento usa el repositorio del directorio actual.
set -euo pipefail

command -v gh >/dev/null || { echo "Falta GitHub CLI (gh)." >&2; exit 1; }
gh auth status >/dev/null 2>&1 || { echo "gh no está autenticado: ejecuta 'gh auth login'." >&2; exit 1; }

repo_args=()
if [ $# -ge 1 ]; then
    repo_args=(--repo "$1")
fi

crear() {
    gh label create "$1" --color "$2" --description "$3" --force ${repo_args[@]+"${repo_args[@]}"}
}

# Categorías (Tabla 11 de mantenimiento)
crear "tipo:correctivo" "d73a4a" "Corrige un defecto detectado (Tabla 11 de mantenimiento)"
crear "tipo:adaptativo" "0e8a16" "Adapta al entorno: framework, PHP, MySQL (Tabla 11 de mantenimiento)"
crear "tipo:perfectivo" "1d76db" "Mejora o amplía; fuera del alcance de la iteración 1 (Tabla 11 de mantenimiento)"
crear "tipo:preventivo" "fbca04" "Reduce riesgo futuro o deuda técnica (Tabla 11 de mantenimiento)"

# Severidades (Tablas 9 de pruebas, 18 y 19)
crear "sev:critica" "b60205" "Bloquea la integración; corregir en máximo una iteración (Tablas 18 y 19)"
crear "sev:alta"    "e99695" "Detiene la liberación (Tabla 9 de pruebas)"
crear "sev:media"   "f9d0c4" "Se registra con un plan de corrección (Tabla 9 de pruebas)"
crear "sev:baja"    "fef2c0" "Se registra con un plan de corrección (Tabla 9 de pruebas)"

echo "Etiquetas creadas o actualizadas."

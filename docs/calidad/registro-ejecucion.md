# Registro de ejecución de pruebas, iteración 1

Registro de la ejecución de los casos de `trazabilidad.md`. Equivale al registro de pruebas (*test log*) de ISO/IEC/IEEE 29119-3. Cada fila es una ejecución: si un caso se repite (por ejemplo, después de una corrección o de la actualización de CodeIgniter), se agrega una fila nueva y no se sobrescribe la anterior.

Resultado: **Pasa** (coincide con el esperado), **Falla** (no coincide; genera un defecto) o **Bloqueado** (no se pudo ejecutar; se indica por qué).

| Fecha y hora | Caso | Commit | CodeIgniter | Entorno | Resultado | Resultado obtenido | Evidencia | Defecto |
|---|---|---|---|---|---|---|---|---|
| | | | 3.1.6 | staging Docker | | | | |

## Resumen por ronda

| Ronda | Fecha | Commit | Ejecutados / diseñados | Pasan | Fallan | Bloqueados | Cobertura Orders | Cobertura Products |
|---|---|---|---|---|---|---|---|---|
| 1 · línea base 3.1.6 | | | / 20 | | | | % | % |
| 2 · tras correcciones | | | / 20 | | | | % | % |
| 3 · CodeIgniter 3.1.13 | | | / 20 | | | | % | % |

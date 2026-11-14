---
description: Prepara la auditoría de un subpaso sin modificar nada
argument-hint: [subpaso, por ejemplo 1.2]
---
Prepara la auditoría del subpaso $ARGUMENTS. **No modifiques ningún archivo.**

1. Ejecuta `git status` y `git diff --stat` y lista cada archivo cambiado.
2. Para cada archivo, indica qué debe revisar el autor línea por línea y por qué (riesgo).
3. Da los comandos exactos para verificar que el cambio funciona, con el resultado esperado de cada uno.
4. Indica qué tabla del informe (`docs/calidad/fuente-informe.md`) se cumple con este cambio y cuál no o solo en parte.
5. Señala cualquier cosa que contradiga las reglas de `CLAUDE.md` (secretos, funcionalidad nueva, cambios en `system/`).
6. Termina con una lista de verificación con casillas para que el autor la marque.

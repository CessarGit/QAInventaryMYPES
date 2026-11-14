---
description: Registra la ejecución de un caso de prueba en registro-ejecucion.md
argument-hint: CP-xx <Pasa|Falla|Bloqueado> [nota]
---
Registra la ejecución de $ARGUMENTS en `docs/calidad/registro-ejecucion.md`.

- Agrega una fila nueva a la tabla de ejecuciones; nunca sobrescribas una anterior.
- Columnas: fecha y hora (`date '+%Y-%m-%d %H:%M %Z'`), caso, commit (`git rev-parse --short HEAD`), CodeIgniter (`grep CI_VERSION system/core/CodeIgniter.php`), entorno (staging Docker), resultado, resultado obtenido, evidencia y defecto.
- El resultado obtenido se describe con hechos; el esperado está en la ficha de `trazabilidad.md` y no se reescribe (regla 6).
- Si el resultado es **Falla**: recuerda la regla 12 y pide al autor la severidad y el número de issue (plantilla Defecto). Deja la columna Defecto vacía hasta tenerlo.
- Si es **Bloqueado**: indica la causa en la nota.
- No modifiques nada más.

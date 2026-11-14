<!--
Respaldo: Figura 1 de mantenimiento (rama → pruebas de regresión → revisión →
firma SQA → despliegue), Tabla 12 de mantenimiento (changelog y commits
convencionales), Tabla 13 de mantenimiento (regresiones detectadas),
Tabla 18 (semáforo), lista L1 y Tabla 17, puntos 4 a 8.
-->

## Resumen

<!-- Qué cambia y por qué. -->

Cierra #<!-- número de la issue -->

- **Categoría:** <!-- correctivo | adaptativo | perfectivo | preventivo (Tabla 11 de mantenimiento) -->
- **Tabla del informe que lo respalda:** <!-- p. ej., Tabla 13 de seguridad -->
- **Caso(s) de prueba y requisito(s):** <!-- CP-xx → RQ-xx -->

## Lista de verificación

- [ ] Caso de prueba enlazado (CP-xx y su requisito) — Tabla 17, punto 6; L1, pregunta 10
- [ ] Lista L1 revisada (10 preguntas) — autorrevisión que sustituye la revisión por pares (desviación de la Figura 1)
- [ ] Pruebas de regresión ejecutadas, sin casos que pasaban y ahora fallan — Figura 1 y Tabla 13 de mantenimiento
- [ ] CHANGELOG.md actualizado — Tabla 17, punto 8; Tabla 12 de mantenimiento
- [ ] Pipeline en verde (o ámbar con el riesgo registrado y aprobado) — Tabla 18
- [ ] Sin secretos en el código ni en la evidencia — Tabla 13 de seguridad
- [ ] Sin funcionalidad nueva — exclusión del alcance del informe
- [ ] Commits en formato Conventional Commits, en español — Tabla 17, punto 5

## Semáforo (Tabla 18)

<!-- verde | ámbar (riesgo y aprobación) | rojo (no se integra) -->

## Firma SQA

<!-- Tabla 17, punto 4. Quien firma deja un comentario de aprobación en el PR. -->

- [ ] Revisado y aprobado por SQA: <!-- nombre y fecha -->

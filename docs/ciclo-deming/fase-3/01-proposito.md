# Fase 3 · Verificar: propósito de la fase

## Qué resuelve

La Fase 2 dejó evidencia: dos rondas de pruebas, cuatro revisiones, una recuperación medida y seis defectos corregidos. También dejó brechas declaradas. Sin una verificación, esa evidencia solo diría lo que se hizo, no si alcanza las metas del informe ni si el proceso se siguió como se diseñó.

La fase Verificar compara los resultados con cada umbral del informe y comprueba el proceso con una autoverificación. Según la Tabla 23 de PHVA, aquí se ejecutan los casos, se actualizan las métricas y se produce un informe de resultados. La fase tiene dos pasos:
1. **Paso 8 · Medir:** calcula todas las métricas que define el informe y las compara con su umbral, sin redondear. Son las de las Tablas 13 y 19 de PHVA, la Tabla 19 de revisiones, la Tabla 13 de mantenimiento, el DPMO por subsistema (Tablas 9 y 10 de Top-Down), la Tabla 9 de recuperación, y los niveles, técnicas y controles (Tablas 16 y 17 de PHVA y Tabla 13 de seguridad).
2. **Paso 9 · Autoverificación:** aplica la lista de verificación de la Tabla 17 siguiendo el plan de auditoría (Tabla 16). Actualiza las observaciones O-01 a O-06 (Tabla 18), hace la auditoría de cumplimiento (Tabla 20 de PHVA) y prepara la prueba independiente y el paquete para el auditor externo. Sustituye a la auditoría interna cruzada de la Tabla 14 de auditoría, que no es posible con un solo autor.

Además, la fase corrige a la baja tres evaluaciones de la Fase 2: técnicas y aceptación (Tablas 16 y 17 de PHVA), grupos de la matriz en `stock`, y defectos que escaparon a las revisiones. Todo lo que aquí resulta, consolidado en las brechas B-01 a B-17, es la entrada de la Fase 4 (Actuar).

## Qué define esta fase para la siguiente

| Producto de la Fase 3 | Dónde está | Para qué sirve en la Fase 4 |
|---|---|---|
| Entradas, brechas declaradas y correcciones a la Fase 2 | `02-punto-de-partida.md` | Base de la retrospectiva |
| Métricas con valor, umbral y estado | `03-paso-8-medir.md` | Diagnóstico de cada acción de mejora (Tabla 21 de PHVA) |
| Lista de verificación, observaciones y auditoría de cumplimiento | `04-paso-9-autoverificacion.md` | No conformidades que deben corregirse |
| Prueba independiente y paquete para el auditor externo | `04-paso-9-autoverificacion.md` (9.5 y 9.6) | Independencia de la verificación antes de cerrar la iteración |
| Brechas consolidadas B-01 a B-17 | `05-objetivos-y-criterio-de-salida.md` | Una acción de mejora por brecha |

## Datos de la fase

| Dato | Valor |
|---|---|
| Fecha de diseño | No fijada en el plan |
| Fecha de desarrollo | Por registrar |
| Estado verificado | `main` al cierre de la Fase 2 (PR #53), CodeIgniter 3.1.6 |
| Responsable | César Roque (autor y firma SQA), con Claude Code como apoyo |
| Independencia | Autoverificación del autor; la independencia la aporta la auditoría externa del docente |
| Fuentes | `docs/calidad/fuente-informe.md`, `docs/calidad/fuente-informe-2.md` y las secciones del informe que no están en los extractos (Tablas 16 y 17 de PHVA) |

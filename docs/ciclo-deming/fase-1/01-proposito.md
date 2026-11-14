# Fase 1 · Planificar: propósito de la fase

## Qué resuelve

El sistema de inventarios y punto de venta para MYPES ya estaba desarrollado, pero no tenía pruebas, trazabilidad ni un entorno controlado. La línea base del informe (Tabla 7) lo resume así:

- 0 pruebas manuales o automáticas ejecutadas;
- 0 casos de prueba documentados y 0 pruebas unitarias;
- ningún registro de tareas;
- ningún repositorio central de trabajo;
- CodeIgniter 3.1.6, cuando la última versión de la rama 3.x es la 3.1.13.

En esas condiciones no se podía saber si un cambio mejoraba o empeoraba la calidad, porque no había un estado inicial registrado, ni casos contra los que comparar, ni un lugar donde probar sin afectar la operación.

La fase Planificar convierte el diseño del informe en un punto de partida medible. Para eso registra el estado inicial del sistema y lo confirma contra el código real. Después prepara un entorno de trabajo controlado y comprueba en el código los hallazgos del análisis Top-Down. Por último, une los requisitos con sus casos de prueba, fija el plan de pruebas de la iteración y adopta las metas que se medirán al final.

Todo lo que se hace en las fases siguientes sale de lo que aquí se define: las revisiones parten de los hallazgos confirmados, las pruebas ejecutan los casos de la trazabilidad, el entorno y la integración continua son los de esta fase, y las mediciones se comparan con las metas de la Tabla 13 de PHVA.

## Qué define esta fase para las siguientes

| Producto de la Fase 1 | Dónde está | Qué fase lo usa y para qué |
|---|---|---|
| Línea base confirmada y etiqueta `linea-base` | `02-linea-base.md`; `docs/ejecucion/evidencias/1.0-inventario.md` | Verificar: es el «antes» de la comparación final |
| Staging, secretos fuera del código, registro de solicitudes e integración continua | `03-paso-1-entorno.md` | Hacer: todos los cambios se prueban en staging y entran por PR con el semáforo |
| Hallazgos Top-Down confirmados (H-01 a H-06) e issues #4 a #7 | `04-paso-2-top-down-y-plan.md`; `docs/calidad/hallazgos-topdown.md` | Hacer: punto de partida de las revisiones y origen de casos de prueba |
| Trazabilidad unificada RQ-01 a RQ-06 con CP-01 a CP-14 | `docs/calidad/trazabilidad.md` | Hacer: casos que se ejecutan |
| Plan de pruebas de la iteración | `docs/calidad/plan-pruebas-iteracion-1.md` | Hacer: orden de ejecución, criterios y regla ante retrasos |
| Metas de la Tabla 13 de PHVA y criterio de salida | `05-metas-y-criterio-de-salida.md` | Verificar: se miden al final de la iteración |

## Datos de la fase

| Dato | Valor |
|---|---|
| Fechas de diseño (planificadas) | Ejecución el 11/10/2026; evidencias y firmas el 10/11/2026 |
| Fechas de desarrollo (reales, bitácora) | Ejecución el 13/11/2026; evidencias y firmas el 14/11/2026 |
| Rama de trabajo | `chore/fase1-planificar` y ramas por tema, fusionadas en `main` mediante PR |
| Responsable | César Roque (autor y firma SQA), con Claude Code como apoyo |
| Registro | `docs/ejecucion/bitacora.md`, subpasos 1.0 a 2.3, firmados (PR #24) |
| Fuentes de diseño | `docs/calidad/fuente-informe.md` y `docs/calidad/fuente-informe-2.md` |

## Fechas de diseño frente a desarrollo

El diseño fijó las fechas de la iteración. El desarrollo empezó más tarde: estas son las fechas reales de la bitácora y de los commits. La comparación sirve para estimar qué habría pasado si el trabajo se hubiera iniciado en la fecha de diseño.

| Hito | Diseño | Desarrollo | Desfase |
|---|---|---|---|
| Ejecución de la Fase 1 (subpasos 1.0 a 2.3) | 11/10/2026 | 13/11/2026 | +33 días |
| Evidencias y firmas de la Fase 1 | 10/11/2026 | 14/11/2026 | +4 días |
| Inicio de las pruebas (preparar `stock_test` y PC-0) | 15/10/2026 | 14/11/2026 | +30 días |
| Fin de las pruebas (registro de resultados y defectos) | 27/10/2026 | 14/11/2026 | +18 días |

| Duración | Diseño | Desarrollo |
|---|---|---|
| Fase 1 | 1 día | 1 día (13/11/2026) |
| Pruebas de la iteración | 13 días (15 al 27/10/2026) | 1 día (14/11/2026) |

**Estimado.** El desarrollo de la Fase 1 duró lo mismo que su diseño: 1 día. Si se hubiera iniciado el 11/10/2026, con esa misma duración, la Fase 1 habría cerrado ese mismo día. La holgura de 4 días hasta el inicio planificado de las pruebas (15/10/2026) se habría mantenido.

El desfase de 33 días se debe al inicio tardío, no a una duración mayor de la planificada.

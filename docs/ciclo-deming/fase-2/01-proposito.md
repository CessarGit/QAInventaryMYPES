# Fase 2 · Hacer: propósito de la fase

## Qué resuelve

Al cerrar la Fase 1, el sistema tenía un plan, pero ninguna evidencia sobre su calidad. Los hallazgos del Top-Down estaban confirmados en el código, aunque nadie había revisado a fondo los productos de mayor riesgo. Los 14 casos de prueba estaban diseñados, pero sin ejecutar. No había respaldos ni una recuperación probada, y no se conocía ningún defecto.

La fase Hacer ejecuta lo planificado y produce esa evidencia en tres pasos:
- **Paso 3:** revisa los productos de riesgo alto antes de probar.
- **Paso 4:** demuestra que la información se puede recuperar.
- **Paso 5:** ejecuta las pruebas, verifica la seguridad, registra cada defecto con su severidad y corrige los críticos y altos sin cambiar ninguna prueba.

El resultado es lo que la Fase 3 (Verificar) va a medir: un registro de ejecución con dos rondas, defectos clasificados, correcciones verificadas y una línea base de regresión.

El diseño de la iteración incluía en esta fase dos pasos más. El paso 6, actualizar CodeIgniter a 3.1.13, quedó diferido a la próxima iteración por decisión del autor (issue #52). El paso 7, reversión RB-06, no aplica porque no hubo actualización.

## Qué define esta fase para las siguientes

| Producto de la Fase 2 | Dónde está | Qué fase lo usa y para qué |
|---|---|---|
| Revisiones RV-01, RV-03, RV-04 y RV-10, con sus indicadores | `docs/calidad/revisiones/` | Verificar: indicadores de la Tabla 19 de revisiones |
| Scripts de respaldo, restauración y verificación; RTO y RPO medidos | `scripts/`; `docs/ejecucion/evidencias/4.2-*` y `4.3-*` | Actuar: prueba periódica del plan de recuperación (Tabla 20) |
| Base `stock_test` con PC-0 y suite PHPUnit en la integración continua | `application/tests/`; `.github/workflows/calidad.yml` | Todas: las pruebas se repiten en cada cambio |
| Registro de ejecución con las rondas 1 y 2 | `docs/calidad/registro-ejecucion.md` | Verificar: casos ejecutados, cobertura y defectos |
| Defectos #34 a #44, con 6 corregidos | Issues de GitHub; PR #46 a #50 | Verificar y Actuar |
| Línea base de regresión `pruebas-base-3.1.6` | Etiqueta; `5.7-suite-base-3.1.6.txt` | Actuar: referencia para la actualización diferida (#52) |

## Datos de la fase

| Dato | Valor |
|---|---|
| Fechas de diseño (planificadas) | El plan solo fijó fechas para las pruebas: del 15/10/2026 al 27/10/2026. Los pasos 3 y 4 no tenían fecha de diseño |
| Fechas de desarrollo (reales) | Pasadas del autor en las revisiones: 01/11/2026 y 03/11/2026. Resto de la fase: del 13/11/2026 (pasada de Claude en RV-01) al 14/11/2026 |
| Ramas | `docs/...`, `chore/...`, `test/...` y `fix/...`, fusionadas en `main` mediante PR (PR #13 a #53) |
| Responsable | César Roque (autor, revisor y firma SQA), con Claude Code como apoyo |
| Registro | Bitácora (pasos 3.1 a 3.4, firmados); registro de ejecución; evidencias en `docs/ejecucion/evidencias/` |
| Guía de ejecución | `PROMPTS-FASE2-ADAPTADO.md` |

## Fechas de diseño frente a desarrollo

| Hito | Diseño | Desarrollo | Desfase |
|---|---|---|---|
| Inicio de las pruebas (preparar `stock_test` y PC-0) | 15/10/2026 | 14/11/2026 | +30 días |
| Fin de las pruebas (registro de resultados y defectos) | 27/10/2026 | 14/11/2026 | +18 días |
| Duración de las pruebas | 13 días | 1 día | −12 días |

**Estimado.** Las pruebas, sus correcciones y la ronda 2 cupieron en 1 día de desarrollo. Si se hubieran iniciado en la fecha de diseño, habrían terminado el 15/10/2026, 12 días antes del fin planificado. El desfase se debe al inicio tardío, no a la duración del trabajo.

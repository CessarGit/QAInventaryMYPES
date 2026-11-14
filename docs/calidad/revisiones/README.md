# Revisiones de la iteración 1: resumen e indicadores

Cierre del paso 3 (subpaso 3.4), ampliado con RV-10 en el subpaso 4.1. Resume las revisiones RV-01, RV-03, RV-04 y RV-10 y calcula los indicadores de la Tabla 19 de revisiones (`docs/calidad/fuente-informe-2.md`) que ya se pueden calcular. Los datos salen de la sección 4 (métricas) de cada revisión.

## 1. Revisiones realizadas

| Revisión | Producto | Riesgo | Lista | Decisión (Tabla 18) | Firma |
|---|---|---|---|---|---|
| [RV-01](RV-01-matriz.md) | Matriz de roles (Tabla 5) contra los permisos del código | Alto | L2 | Aceptar con correcciones | César Roque, 14/11/2026 |
| [RV-03](RV-03-orders.md) | Módulo Orders (controlador, modelo y vistas) | Alto | L1 | Aceptar con correcciones | César Roque, 14/11/2026 |
| [RV-04](RV-04-usuarios-permisos.md) | Users, Groups y autenticación | Alto | L1 | Aceptar con correcciones | César Roque, 14/11/2026 |
| RV-05 | Products | Alto | L1 | **Pendiente**: no se realizó en esta iteración (decisión del autor) | — |
| [RV-10](RV-10-scripts.md) | Scripts de respaldo, restauración y verificación (subpaso 4.1) | Medio | L5 | Aceptar con correcciones | César Roque, 14/11/2026 |

Desviación común: la Tabla 7 de revisiones pide inspección formal con tres personas; se trabaja solo, con verificación de escritorio del autor y una segunda pasada de Claude Code como apoyo (no como revisor humano independiente).

## 2. Métricas por revisión

| Revisión | Ep (h) | Ea (h) | Er (h) | WPS | Mayores / menores | Densidad de error | Solo autor / solo Claude / ambos |
|---|---|---|---|---|---|---|---|
| RV-01 | 1,0 | 1,15 | 0,2 (corrección de la matriz, PR #13) | 60 celdas | 12 / 2 | 14 / 60 × 1000 = 233,3 por mil celdas | 1 / 0 / 13 |
| RV-03 | 1,0 | 0,4 | N/A (se mide en el paso 5.6) | 1516 líneas | 10 / 3 | 13 / 1516 × 1000 = 8,6 por mil líneas | 0 / 0 / 13 |
| RV-04 | 0,3 | 0,4 | N/A (se mide en el paso 5.6) | 1499 líneas | 13 / 5 | 18 / 1499 × 1000 = 12,0 por mil líneas | 0 / 0 / 18 |
| RV-10 | 0,4 | 1,0 | 0,02 (correcciones de H-RV10-01, -02 y -04) | 209 líneas | 2 / 3 | 5 / 209 × 1000 = 23,9 por mil líneas | 0 / 5 / 0 |
| **Total** | 2,7 | 2,95 | — | — | **37 / 13** | — | **1 / 5 / 44** |

Comparación autor / Claude: de los 50 hallazgos, 44 los encontraron ambos, 1 solo el autor (H-RV01-14) y 5 solo Claude (los de RV-10). En H-RV01-04, común a ambos, Claude amplió el alcance (creación, edición y alta de valores de atributo, además de la eliminación que identificó el autor). En RV-10 la segunda pasada la hizo el mismo productor de los scripts (Claude Code), por lo que no es independiente.

## 3. Indicadores de la Tabla 19 de revisiones

| Indicador | Fórmula | Valor | Meta | Estado |
|---|---|---|---|---|
| Cobertura de revisión | Revisiones de riesgo alto realizadas / planificadas | 3 / 4 = **75 %** (falta RV-05, Products) | 100 % antes de ejecutar las pruebas | **No alcanzada** (regla 17) |
| Cierre de hallazgos mayores | Mayores cerrados y verificados / mayores registrados | 5 / 37 = **13,5 %** (H-RV01-01, corregido en el PR #13; H-RV10-01 y H-RV10-02, corregidos y verificados en el 4.1 y el 4.2; H-RV01-02 y H-RV04-01, corregidos en #34 y verificados con CP-15) | 100 % antes de aceptar el producto | **Abierto** hasta el paso 5 |
| Eficacia de eliminación de defectos | E / (E + D) | No calculable: D (defectos después de liberar) aún no existe | Al menos 85 % | Se calcula en el paso 8 |
| Esfuerzo por revisión formal | Ep + Ea | RV-01 **2,15 h** · RV-03 **1,4 h** · RV-04 **0,7 h** · RV-10 **1,4 h** | No más de 10 horas-persona | Alcanzada en las cuatro |
| Densidad de error | Errtotal / WPS por tipo de producto | Matriz: 233,3 por mil celdas · Código: 8,6 (Orders) y 12,0 (Users, Groups y autenticación) por mil líneas · Scripts: 23,9 por mil líneas | Línea base con las tres primeras revisiones | **Línea base establecida** (RV-10 se compara con ella, no la modifica) |

Las densidades se agrupan por tipo de producto, como pide la fórmula: la de la matriz (celdas) no se compara con la del código (líneas).

## 4. Destino de los hallazgos mayores

| Destino | Hallazgos | Cantidad |
|---|---|---|
| Corregido | H-RV01-01 → PR #13 (Settings del Administrador pasa de CRUD a RU propia, opción A de H-03) | 1 |
| Corregido antes de integrar y verificado en el 4.2 | H-RV10-01 (permisos de los respaldos), H-RV10-02 (falso éxito de la verificación de conteos; la ruta del fallo de consulta, por simulación) | 2 |
| Issue existente #4 (H-01) | H-RV01-03, H-RV01-04, H-RV01-05, H-RV01-06, H-RV03-01 | 5 |
| Issue existente #6 (H-04) | H-RV03-05 | 1 |
| Issue existente #8 (A-04) | H-RV01-08, H-RV04-09 | 2 |
| Defecto confirmado en el paso 5 (regla 12) | H-RV01-02 y H-RV04-01 → #34; H-RV03-02 → #35 y #36; H-RV04-02 → #40; H-RV04-03 → #42; H-RV04-07 → #44; H-RV04-04 parcial (sin Secure) → #15 | 7 |
| Issue #14: decisiones pendientes en la matriz | H-RV01-07, H-RV01-11, H-RV01-12 | 3 |
| Issue #15: requisitos de seguridad sin definir | H-RV01-14, H-RV04-06 | 2 |
| Issue #16: integridad del stock al editar o eliminar una venta y con ventas simultáneas | H-RV03-06, H-RV03-07, H-RV03-08, H-RV01-10 | 4 |
| Issue #17: importes aceptados del navegador | H-RV03-03 | 1 |
| Issue #18: salidas sin escapar (XSS) | H-RV03-04, H-RV04-14 | 2 |
| Issue #19: datos de la venta | H-RV03-12, H-RV03-13 | 2 |
| Issue #20: gestión de usuarios y grupos | H-RV04-10, H-RV04-11, H-RV04-12, H-RV04-13, H-RV04-16 | 5 |
| **Total** | | **37** |

Los issues #14 a #20 se crearon en el subpaso 3.4 y están anotados en la sección 5 de cada revisión. Los hallazgos menores (13) quedan registrados en cada revisión, sin issue.

# Revisiones de la iteración 1: resumen e indicadores

Cierre del paso 3 (subpaso 3.4). Resume las revisiones RV-01, RV-03 y RV-04 y calcula los indicadores de la Tabla 19 de revisiones (`docs/calidad/fuente-informe-2.md`) que ya se pueden calcular. Los datos salen de la sección 4 (métricas) de cada revisión.

## 1. Revisiones realizadas

| Revisión | Producto | Riesgo | Lista | Decisión (Tabla 18) | Firma |
|---|---|---|---|---|---|
| [RV-01](RV-01-matriz.md) | Matriz de roles (Tabla 5) contra los permisos del código | Alto | L2 | Aceptar con correcciones | César Roque, 14/11/2026 |
| [RV-03](RV-03-orders.md) | Módulo Orders (controlador, modelo y vistas) | Alto | L1 | Aceptar con correcciones | César Roque, 14/11/2026 |
| [RV-04](RV-04-usuarios-permisos.md) | Users, Groups y autenticación | Alto | L1 | Aceptar con correcciones | César Roque, 14/11/2026 |
| RV-05 | Products | Alto | L1 | **Pendiente**: no se realizó en esta iteración (decisión del autor) | — |

Desviación común: la Tabla 7 de revisiones pide inspección formal con tres personas; se trabaja solo, con verificación de escritorio del autor y una segunda pasada de Claude Code como apoyo (no como revisor humano independiente).

## 2. Métricas por revisión

| Revisión | Ep (h) | Ea (h) | Er (h) | WPS | Mayores / menores | Densidad de error | Solo autor / solo Claude / ambos |
|---|---|---|---|---|---|---|---|
| RV-01 | 1,0 | 1,15 | Pendiente (corrección de la matriz, PR #13) | 60 celdas | 12 / 2 | 14 / 60 × 1000 = 233,3 por mil celdas | 1 / 0 / 13 |
| RV-03 | 1,0 | 0,4 | N/A (se mide en el paso 5.6) | 1516 líneas | 10 / 3 | 13 / 1516 × 1000 = 8,6 por mil líneas | 0 / 0 / 13 |
| RV-04 | 0,3 | 0,4 | N/A (se mide en el paso 5.6) | 1499 líneas | 13 / 5 | 18 / 1499 × 1000 = 12,0 por mil líneas | 0 / 0 / 18 |
| **Total** | 2,3 | 1,95 | — | — | **35 / 10** | — | **1 / 0 / 44** |

Comparación autor / Claude: de los 45 hallazgos, 44 los encontraron ambos y 1 solo el autor (H-RV01-14). En H-RV01-04, común a ambos, Claude amplió el alcance (creación, edición y alta de valores de atributo, además de la eliminación que identificó el autor).

## 3. Indicadores de la Tabla 19 de revisiones

| Indicador | Fórmula | Valor | Meta | Estado |
|---|---|---|---|---|
| Cobertura de revisión | Revisiones de riesgo alto realizadas / planificadas | 3 / 4 = **75 %** (falta RV-05, Products) | 100 % antes de ejecutar las pruebas | **No alcanzada** (regla 17) |
| Cierre de hallazgos mayores | Mayores cerrados y verificados / mayores registrados | 1 / 35 = **2,9 %** (H-RV01-01, corregido en el PR #13) | 100 % antes de aceptar el producto | **Abierto** hasta el paso 5 |
| Eficacia de eliminación de defectos | E / (E + D) | No calculable: D (defectos después de liberar) aún no existe | Al menos 85 % | Se calcula en el paso 8 |
| Esfuerzo por revisión formal | Ep + Ea | RV-01 **2,15 h** · RV-03 **1,4 h** · RV-04 **0,7 h** | No más de 10 horas-persona | Alcanzada en las tres |
| Densidad de error | Errtotal / WPS por tipo de producto | Matriz: 233,3 por mil celdas · Código: 8,6 (Orders) y 12,0 (Users, Groups y autenticación) por mil líneas | Línea base con las tres primeras revisiones | **Línea base establecida** |

Las densidades se agrupan por tipo de producto, como pide la fórmula: la de la matriz (celdas) no se compara con la del código (líneas).

## 4. Destino de los hallazgos mayores

| Destino | Hallazgos | Cantidad |
|---|---|---|
| Corregido | H-RV01-01 → PR #13 (Settings del Administrador pasa de CRUD a RU propia, opción A de H-03) | 1 |
| Issue existente #4 (H-01) | H-RV01-03, H-RV01-04, H-RV01-05, H-RV01-06, H-RV03-01 | 5 |
| Issue existente #6 (H-04) | H-RV03-05 | 1 |
| Issue existente #8 (A-04) | H-RV01-08, H-RV04-09 | 2 |
| Se confirman en el paso 5; si la prueba falla, se registran con la plantilla Defecto (regla 12) | H-RV01-02 y H-RV04-01 (A-01: CP-10, CP-15), H-RV03-02 (CP-02, CP-03), H-RV04-02 y H-RV04-04 (CP-18), H-RV04-03 y H-RV04-07 (guion 5.4) | 7 |
| Issue #14: decisiones pendientes en la matriz | H-RV01-07, H-RV01-11, H-RV01-12 | 3 |
| Issue #15: requisitos de seguridad sin definir | H-RV01-14, H-RV04-06 | 2 |
| Issue #16: integridad del stock al editar o eliminar una venta y con ventas simultáneas | H-RV03-06, H-RV03-07, H-RV03-08, H-RV01-10 | 4 |
| Issue #17: importes aceptados del navegador | H-RV03-03 | 1 |
| Issue #18: salidas sin escapar (XSS) | H-RV03-04, H-RV04-14 | 2 |
| Issue #19: datos de la venta | H-RV03-12, H-RV03-13 | 2 |
| Issue #20: gestión de usuarios y grupos | H-RV04-10, H-RV04-11, H-RV04-12, H-RV04-13, H-RV04-16 | 5 |
| **Total** | | **35** |

Los issues #14 a #20 se crearon en el subpaso 3.4 y están anotados en la sección 5 de cada revisión. Los hallazgos menores (10) quedan registrados en cada revisión, sin issue.

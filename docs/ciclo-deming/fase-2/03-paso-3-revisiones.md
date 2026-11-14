# Fase 2 · Paso 3: revisiones

Antes de probar se examinaron los productos de mayor riesgo con las listas del informe. La Tabla 7 de revisiones planifica cuatro revisiones de riesgo alto: la matriz de roles (RV-01), el módulo de ventas (RV-03), el de usuarios y permisos (RV-04) y el de productos (RV-05). **Se realizaron las tres primeras; RV-05 no se realizó, por decisión del autor.** En el paso 4 se añadió RV-10, de riesgo medio, sobre los scripts de respaldo.

## 1. Cómo se revisó: dos pares de ojos

Cada revisión siguió el mismo orden. Primero el autor hizo su verificación de escritorio con la lista y registró su esfuerzo de preparación (Ep). Después Claude Code hizo una segunda pasada con la misma lista. Al final se compararon los hallazgos de cada uno y se registraron el esfuerzo de revisión (Ea) y, cuando hubo corrección, el de retrabajo (Er).

| Revisión | Producto | Riesgo (Tabla 7) | Técnica planificada | Técnica aplicada | Lista | Pasada del autor | Pasada de Claude |
|---|---|---|---|---|---|---|---|
| RV-01 | Matriz de roles (Tabla 5) contra los permisos del código | Alto | Inspección | Verificación de escritorio y segunda pasada | L2 | 01/11/2026 | 13/11/2026 |
| RV-03 | Módulo Orders: controlador, modelo y vistas (1516 líneas) | Alto | Inspección | Verificación de escritorio y segunda pasada | L1 | 03/11/2026 | 14/11/2026 |
| RV-04 | Users, Groups y autenticación (1499 líneas) | Alto | Inspección | Verificación de escritorio y segunda pasada | L1 | 03/11/2026 | 14/11/2026 |
| RV-05 | Módulo Products | Alto | Inspección | **No realizada** | — | — | — |
| RV-10 | Scripts de respaldo, restauración y verificación (209 líneas) | Medio | Walkthrough | Verificación de escritorio y segunda pasada | L5 | 14/11/2026 | 14/11/2026 |

**Desviación común:** la inspección formal de la Tabla 7 requiere tres personas. Se trabajó solo, y la diferencia se compensó con la lista y la doble pasada. Claude Code actúa como apoyo, no como revisor humano independiente. En RV-10, además, la segunda pasada la hizo el mismo que produjo los scripts.

## 2. Resultados por revisión

| Revisión | Hallazgos (mayores / menores) | Ep (h) | Ea (h) | Er (h) | Densidad de error | Decisión (Tabla 18 de revisiones) |
|---|---|---|---|---|---|---|
| RV-01 | 12 / 2 | 1,0 | 1,15 | 0,2 | 14 / 60 celdas × 1000 = 233,3 por mil celdas | Aceptar con correcciones |
| RV-03 | 10 / 3 | 1,0 | 0,4 | Se mide en el paso 5 | 13 / 1516 × 1000 = 8,6 por mil líneas | Aceptar con correcciones |
| RV-04 | 13 / 5 | 0,3 | 0,4 | Se mide en el paso 5 | 18 / 1499 × 1000 = 12,0 por mil líneas | Aceptar con correcciones |
| RV-10 | 2 / 3 | 0,4 | 1,0 | 0,02 | 5 / 209 × 1000 = 23,9 por mil líneas | Aceptar con correcciones |
| **Total** | **37 / 13** | 2,7 | 2,95 | — | — | — |

Hallazgos principales de cada revisión:
- **RV-01:** la matriz daba al Administrador CRUD en Settings sin que el código lo implementara (H-03). Se corrigió con la opción A, «RU (propia)» (PR #13). Mapeo de los permisos del código a cada rol, base de los datos PC-0. Reports no exige sesión en su constructor. Decisiones pendientes en la matriz (issue #14).
- **RV-03:** cantidad sin validar (H-RV03-02) y venta sin transacción ni comprobación de stock (H-RV03-05), que confirman H-04. Datos del cliente sin escapar (H-RV03-04). Importes aceptados del navegador (H-RV03-03). Integridad del stock al editar o eliminar ventas (H-RV03-06 a H-RV03-08). Avisos de PHP con un id inexistente (H-RV03-09).
- **RV-04:** camino completo de A-01, el escalamiento de privilegios desde Settings (H-RV04-01). Settings cambia la contraseña sin pedir la actual y sin protección CSRF (H-RV04-02). El id de sesión no cambia al entrar (H-RV04-03). Atributos de la cookie (H-RV04-04). Campo de contraseña visible (H-RV04-07). Salidas sin escapar en usuarios y grupos (H-RV04-14).
- **RV-10:** respaldos legibles por otros usuarios (H-RV10-01) y falso éxito de la verificación de conteos (H-RV10-02). Los dos son mayores y se corrigieron antes de integrar los scripts.

## 3. Comparación entre el autor y Claude

| Revisión | Solo el autor | Solo Claude | Ambos |
|---|---|---|---|
| RV-01 | 1 (H-RV01-14) | 0 | 13 |
| RV-03 | 0 | 0 | 13 |
| RV-04 | 0 | 0 | 18 |
| RV-10 | 0 | 5 | 0 |
| **Total** | **1** | **5** | **44** |

De los 50 hallazgos, 44 los encontraron los dos. El autor encontró 1 que Claude no vio (H-RV01-14, la expiración de la sesión). Claude encontró los 5 de RV-10, revisión en la que fue también el productor. En H-RV01-04, común a ambos, Claude amplió el alcance a la creación, la edición y el alta de valores de atributo, además de la eliminación que había identificado el autor.

## 4. Destino de los 37 hallazgos mayores

| Destino | Hallazgos | Cantidad |
|---|---|---|
| Corregido en el paso 3 | H-RV01-01 (matriz, PR #13) | 1 |
| Corregido antes de integrar y verificado en el paso 4 | H-RV10-01, H-RV10-02 | 2 |
| Issue existente #4 (H-01) | H-RV01-03 a H-RV01-06, H-RV03-01 | 5 |
| Issue existente #6 (H-04) | H-RV03-05 | 1 |
| Issue existente #8 | H-RV01-08, H-RV04-09 | 2 |
| A confirmar con pruebas en el paso 5 | H-RV01-02 y H-RV04-01 (A-01), H-RV03-02, H-RV04-02, H-RV04-03, H-RV04-04, H-RV04-07 | 7 |
| Issues nuevos #14 a #20 | Decisiones de la matriz (#14), requisitos de seguridad (#15), integridad del stock (#16), importes del navegador (#17), XSS (#18), datos de la venta (#19), gestión de usuarios y grupos (#20) | 19 |
| **Total** | | **37** |

Los 13 hallazgos menores quedan registrados en cada revisión, sin issue.

## 5. Indicadores del paso (Tabla 19 de revisiones)

| Indicador | Valor | Meta |
|---|---|---|
| Cobertura de revisión de riesgo alto | 3 / 4 = 75 % | 100 % antes de probar |
| Esfuerzo por revisión (Ep + Ea) | RV-01 2,15 h; RV-03 1,4 h; RV-04 0,7 h; RV-10 1,4 h | ≤ 10 h |
| Densidad de error (línea base) | Matriz: 233,3 por mil celdas. Código: 8,6 (Orders) y 12,0 (Users) por mil líneas | Medida en las primeras revisiones |
| Cierre de hallazgos mayores al terminar el paso 4 | 3 / 37 = 8,1 % | 100 % antes de aceptar el producto |

**Evidencia:** `docs/calidad/revisiones/` (RV-01, RV-03, RV-04, RV-10 y README con los indicadores), PR #13, #21 y #22 (RV-10 en el PR #26); bitácora de los pasos 3.1 a 3.4, firmada.

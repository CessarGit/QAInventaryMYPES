# Registro de ejecución de pruebas, iteración 1

Registro de la ejecución de los casos de `trazabilidad.md`. Equivale al registro de pruebas (*test log*) de ISO/IEC/IEEE 29119-3. Cada fila es una ejecución: si un caso se repite (por ejemplo, después de una corrección o de la actualización de CodeIgniter), se agrega una fila nueva y no se sobrescribe la anterior.

Resultado: **Pasa** (coincide con el esperado), **Falla** (no coincide; genera un defecto) o **Bloqueado** (no se pudo ejecutar; se indica por qué).

| Fecha y hora | Caso | Commit | CodeIgniter | Entorno | Resultado | Resultado obtenido | Evidencia | Defecto |
|---|---|---|---|---|---|---|---|---|
| 2026-11-14 12:32 CST | CP-01 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Pasa | Venta registrada; stock 0 | PHPUnit, Orders_stock_test |  |
| 2026-11-14 12:32 CST | CP-02 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Falla | Venta registrada; stock negativo | PHPUnit, Orders_stock_test | #35 |
| 2026-11-14 12:32 CST | CP-03 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Falla | Ventas con 0 y −1 registradas | PHPUnit, Orders_stock_test | #36 |
| 2026-11-14 12:32 CST | CP-04 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Pasa | Redirección; la venta sigue | PHPUnit, Permisos_test |  |
| 2026-11-14 12:32 CST | CP-05 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Pasa | Venta editada | PHPUnit, Permisos_test |  |
| 2026-11-14 12:32 CST | CP-06 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Pasa | Redirección | PHPUnit, Permisos_test |  |
| 2026-11-14 12:32 CST | CP-07 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Pasa | Admin accede; Gerente redirigido | PHPUnit, Permisos_test |  |
| 2026-11-14 12:32 CST | CP-08 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Falla | Rechaza campos vacíos salvo marca y categoría: producto creado | PHPUnit, Productos_reporte_test | #38 |
| 2026-11-14 12:32 CST | CP-09 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Falla | El total anual no cambia | PHPUnit, Productos_reporte_test | #37 |
| 2026-11-14 12:32 CST | CP-10 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Pasa | Cambia la propia; la de otro no | PHPUnit, Permisos_test |  |
| 2026-11-14 12:32 CST | CP-13 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Pasa | Usuario creado con los permisos de Ventas | PHPUnit, Permisos_test |  |
| 2026-11-14 12:32 CST | CP-14 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Pasa | Hash bcrypt verificable | PHPUnit, Contrasenas_test |  |
| 2026-11-14 12:32 CST | CP-15 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Falla | ventas_test pasa al grupo 14 | PHPUnit, Permisos_test | #34 |
| 2026-11-14 12:32 CST | CP-16 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Pasa | Redirección sin sesión en las 4 URL | PHPUnit, Permisos_test |  |
| 2026-11-14 12:32 CST | CP-19 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Pasa | Venta registrada; stock 4 | PHPUnit, Orders_stock_test |  |
| 2026-11-14 12:32 CST | CP-20 | 7cd3841 + test/grupo-a-stock | 3.1.6 | staging Docker | Pasa | Nombre guardado truncado a 255 | PHPUnit, Productos_reporte_test |  |
| 2026-11-14 13:09 CST | CP-04 (URL) | eaa15e4 | 3.1.6 | staging Docker | Pasa | Ventas redirigido al escribir orders/remove | HTTP (curl) contra staging; 5.4-pruebas-manuales/guion.md |  |
| 2026-11-14 13:09 CST | CP-06 (URL) | eaa15e4 | 3.1.6 | staging Docker | Pasa | Bodega redirigido al escribir orders | HTTP (curl) contra staging; 5.4-pruebas-manuales/guion.md |  |
| 2026-11-14 13:09 CST | CP-07 (URL) | eaa15e4 | 3.1.6 | staging Docker | Pasa | Gerente redirigido al escribir users | HTTP (curl) contra staging; 5.4-pruebas-manuales/guion.md |  |
| 2026-11-14 13:09 CST | CP-11 | eaa15e4 | 3.1.6 | staging Docker | Pasa | Marca, categoría, producto, venta y reporte sin error; producto visible | HTTP (curl) contra staging; 5.4-pruebas-manuales/guion.md |  |
| 2026-11-14 13:09 CST | CP-12 | eaa15e4 | 3.1.6 | staging Docker | Falla | Hoja (neto) 49,2; reporte +0 sin marcar pagadas y +40 (bruto) marcadas como pagadas | HTTP (curl) contra staging; 5.4-pruebas-manuales/guion.md | #37 |
| 2026-11-14 13:09 CST | CP-17 | eaa15e4 | 3.1.6 | staging Docker | Falla | Script sin escapar en listado de productos y de ventas (JSON), edición y factura | HTTP (curl) contra staging; 5.4-pruebas-manuales/guion.md | #41 |
| 2026-11-14 13:09 CST | CP-18 | eaa15e4 | 3.1.6 | staging Docker | Falla | MARCA-CSRF creada sin token | HTTP (curl) contra staging; 5.4-pruebas-manuales/guion.md | #40 |
| 2026-11-14 13:34 CST | CP-01 | 64df852 | 3.1.6 | staging Docker | Pasa | Venta registrada; stock 0 | 5.7-suite-base-3.1.6.txt, Orders_stock_test |  |
| 2026-11-14 13:34 CST | CP-02 | 64df852 | 3.1.6 | staging Docker | Pasa | Venta rechazada; stock 5 | 5.7-suite-base-3.1.6.txt, Orders_stock_test |  |
| 2026-11-14 13:34 CST | CP-03 | 64df852 | 3.1.6 | staging Docker | Pasa | Ventas con 0 y −1 rechazadas | 5.7-suite-base-3.1.6.txt, Orders_stock_test |  |
| 2026-11-14 13:34 CST | CP-04 | 64df852 | 3.1.6 | staging Docker | Pasa | Redirección; la venta sigue | 5.7-suite-base-3.1.6.txt, Permisos_test |  |
| 2026-11-14 13:34 CST | CP-05 | 64df852 | 3.1.6 | staging Docker | Pasa | Venta editada | 5.7-suite-base-3.1.6.txt, Permisos_test |  |
| 2026-11-14 13:34 CST | CP-06 | 64df852 | 3.1.6 | staging Docker | Pasa | Redirección | 5.7-suite-base-3.1.6.txt, Permisos_test |  |
| 2026-11-14 13:34 CST | CP-07 | 64df852 | 3.1.6 | staging Docker | Pasa | Admin accede; Gerente redirigido | 5.7-suite-base-3.1.6.txt, Permisos_test |  |
| 2026-11-14 13:34 CST | CP-08 | 64df852 | 3.1.6 | staging Docker | Falla | Rechaza campos vacíos salvo marca y categoría: producto creado | 5.7-suite-base-3.1.6.txt, Productos_reporte_test | #38 |
| 2026-11-14 13:34 CST | CP-09 | 64df852 | 3.1.6 | staging Docker | Pasa | El total anual suma el neto de la venta | 5.7-suite-base-3.1.6.txt, Productos_reporte_test |  |
| 2026-11-14 13:34 CST | CP-10 | 64df852 | 3.1.6 | staging Docker | Pasa | Cambia la propia; la de otro no | 5.7-suite-base-3.1.6.txt, Permisos_test |  |
| 2026-11-14 13:34 CST | CP-13 | 64df852 | 3.1.6 | staging Docker | Pasa | Usuario creado con los permisos de Ventas | 5.7-suite-base-3.1.6.txt, Permisos_test |  |
| 2026-11-14 13:34 CST | CP-14 | 64df852 | 3.1.6 | staging Docker | Pasa | Hash bcrypt verificable | 5.7-suite-base-3.1.6.txt, Contrasenas_test |  |
| 2026-11-14 13:34 CST | CP-15 | 64df852 | 3.1.6 | staging Docker | Pasa | El grupo de ventas_test no cambia | 5.7-suite-base-3.1.6.txt, Permisos_test |  |
| 2026-11-14 13:34 CST | CP-16 | 64df852 | 3.1.6 | staging Docker | Pasa | Redirección sin sesión en las 4 URL | 5.7-suite-base-3.1.6.txt, Permisos_test |  |
| 2026-11-14 13:34 CST | CP-19 | 64df852 | 3.1.6 | staging Docker | Pasa | Venta registrada; stock 4 | 5.7-suite-base-3.1.6.txt, Orders_stock_test |  |
| 2026-11-14 13:34 CST | CP-20 | 64df852 | 3.1.6 | staging Docker | Pasa | Nombre guardado truncado a 255 | 5.7-suite-base-3.1.6.txt, Productos_reporte_test |  |
| 2026-11-14 13:35 CST | CP-04 (URL) | 64df852 | 3.1.6 | staging Docker | Pasa | Ventas redirigido al escribir orders/remove | HTTP (curl, con token) contra staging |  |
| 2026-11-14 13:35 CST | CP-06 (URL) | 64df852 | 3.1.6 | staging Docker | Pasa | Bodega redirigido al escribir orders | HTTP (curl, con token) contra staging |  |
| 2026-11-14 13:35 CST | CP-07 (URL) | 64df852 | 3.1.6 | staging Docker | Pasa | Gerente redirigido al escribir users | HTTP (curl, con token) contra staging |  |
| 2026-11-14 13:35 CST | CP-11 | 64df852 | 3.1.6 | staging Docker | Pasa | Marca, categoría, producto, venta y reporte sin error; producto visible | HTTP (curl, con token) contra staging |  |
| 2026-11-14 13:35 CST | CP-12 | 64df852 | 3.1.6 | staging Docker | Pasa | Reporte +49,2 = hoja 49,2 (neto) | HTTP (curl, con token) contra staging |  |
| 2026-11-14 13:35 CST | CP-17 | 64df852 | 3.1.6 | staging Docker | Pasa | Texto escapado en listados, edición y factura | HTTP (curl, con token) contra staging |  |
| 2026-11-14 13:35 CST | CP-18 | 64df852 | 3.1.6 | staging Docker | Pasa | Petición sin token rechazada; MARCA-CSRF no creada | HTTP (curl, con token) contra staging |  |

## Resumen por ronda

| Ronda | Fecha | Commit | Ejecutados / diseñados | Pasan | Fallan | Bloqueados | Cobertura Orders | Cobertura Products |
|---|---|---|---|---|---|---|---|---|
| 1 · línea base 3.1.6 | 2026-11-14 | eaa15e4 | 20 / 20 = 100 % | 12 | 8 | 0 | 47,43 % (120/253) | 38,07 % (67/176) |
| 2 · tras correcciones | 2026-11-14 | 64df852 | 20 / 20 = 100 % | 19 | 1 | 0 | 49,06 % (130/265) | 38,07 % (67/176) |
| 3 · CodeIgniter 3.1.13 | | | / 20 | | | | % | % |

### Ronda 1: comparación y defectos abiertos

- Pasan: CP-01, 04, 05, 06, 07, 10, 11, 13, 14, 16, 19, 20. Fallan: CP-02, 03, 08, 09, 12, 15, 17, 18. PHPUnit: 29 pruebas, 22 pasan, 7 fallan; cobertura total 43,59 % (187/429). Por clase: Orders 23,81 %, Model_orders 80,19 %, Products 34,44 %, Model_products 60,00 %.
- Tabla 13 de PHVA: casos ejecutados 100 % (umbral ≥ 90 %, **alcanzado**); cobertura de Orders 47,43 % y de Products 38,07 % (umbral ≥ 60 %, **no alcanzado**); defectos críticos abiertos 1 (umbral 0, **no alcanzado**).
- Tabla 19 de revisiones: cierre de hallazgos mayores sin cambio, 3 / 37 = 8,1 %.

| Severidad | Issues |
|---|---|
| Crítica | #34 (CP-15) |
| Alta | #35 (CP-02), #36 (CP-03), #37 (CP-09, CP-12), #40 (CP-18), #41 (CP-17) |
| Media | #38 (CP-08), #42 (fijación de sesión), #43 (avisos de PHP) |
| Baja | #44 (contraseña visible) |

### Ronda 2: comparación y defectos abiertos

- Corregidos en el 5.6: #34 (PR #46), #35 y #36 (PR #47), #37 (PR #48, con D-08), #41 (PR #49), #40 (PR #50). Falla solo CP-08 (#38, media). PHPUnit: 29 pruebas, 27 pasan, 2 fallan; cobertura total 44,67 % (197/441).
- Tabla 13 de PHVA: casos ejecutados 100 % (**alcanzado**); defectos críticos abiertos 0 (**alcanzado**); cobertura de Orders 49,06 % y de Products 38,07 % (≥ 60 %, **no alcanzado**).
- Tabla 19 de revisiones: cierre de hallazgos mayores 8 / 37 = 21,6 %.
- Abiertos: media #38, #42, #43; baja #44. Quedan para la siguiente iteración (Tabla 9 de pruebas).
- Línea base de regresión para el paso 6: `docs/ejecucion/evidencias/5.7-suite-base-3.1.6.txt`, etiqueta `pruebas-base-3.1.6`.

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

## Resumen por ronda

| Ronda | Fecha | Commit | Ejecutados / diseñados | Pasan | Fallan | Bloqueados | Cobertura Orders | Cobertura Products |
|---|---|---|---|---|---|---|---|---|
| 1 · línea base 3.1.6 | | | / 20 | | | | % | % |
| 2 · tras correcciones | | | / 20 | | | | % | % |
| 3 · CodeIgniter 3.1.13 | | | / 20 | | | | % | % |

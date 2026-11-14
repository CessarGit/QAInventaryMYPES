# Guion de pruebas manuales (subpaso 5.4)

Ejecuta el autor en el staging apuntando a `stock_test`. Registra cada caso con `/registrar-caso CP-xx <resultado>`.

## Preparación
1. `bash scripts/preparar-stock-test.sh` (datos PC-0 limpios; `PRODUCTO-PC0` con stock 5).
2. En `.env`: `DB_NAME=stock_test`; luego `docker compose up -d`. Al terminar, volver a `DB_NAME=stock`.
3. Usuarios: `<rol>_test@pc0.local` con la contraseña de `TEST_USER_PASS`.

## Casos

| Caso | Usuario | Pasos | Esperado (trazabilidad.md) |
|---|---|---|---|
| CP-11 | gerente | Crear marca `MARCA-CP11`, categoría `CAT-CP11` y producto `PROD-CP11` (esa marca, esa categoría, `TIENDA-PC0`, stock 3); vender 1; abrir Reports | Todo el flujo sin error; el producto aparece en el listado |
| CP-12 | gerente | Registrar 3 ventas de `PRODUCTO-PC0` con cantidades 1, 2 y 1 (sin descuento). Abrir Reports (año actual) y comparar con el total `neto` de `cp12-ventas.csv`. Si el reporte da 0, editar las 3 ventas a *Paid*, volver a comparar y anotar ambos valores | Los totales coinciden (neto, D-03) |
| CP-17 | gerente | Crear producto con nombre `<script>alert(1)</script>` (resto válido); abrir Products; venderlo; abrir la venta (editar) y la factura (imprimir). Repetir con `<script>alert(2)</script>` como nombre de cliente | Se muestra el texto literal y no aparece ninguna alerta |
| CP-18 | gerente | Con la sesión abierta en el navegador: en otra terminal, desde esta carpeta, `python3 -m http.server 8081`; abrir `http://127.0.0.1:8081/cp18-sin-token.html` y pulsar *Enviar*; revisar Brands | La petición se rechaza y `MARCA-CSRF` no se crea |
| CP-04, CP-06, CP-07 por URL | ventas / bodega / gerente | Escribir en la barra: `…/orders/remove` (ventas), `…/orders` (bodega), `…/users` (gerente) | Acceso denegado en los tres |

## Verificaciones de la Tabla 13 pendientes de las revisiones

| Hallazgo | Cómo | Esperado (Tabla 13) |
|---|---|---|
| H-RV04-04 | Herramientas del navegador → Almacenamiento → cookie `ci_session`: columnas HttpOnly y Secure | HttpOnly activo (Secure no aplica sin HTTPS en staging; anotarlo) |
| H-RV04-03 | Anotar el valor de `ci_session` antes de iniciar sesión y después | El valor cambia al iniciar sesión |
| H-RV04-07 | Users → Settings: inspeccionar el campo *Password* | `type="password"` (no visible en pantalla) |
| H-RV03-09 | Como gerente: `…/orders/update/99999` y `…/orders/printDiv/99999` | Mensaje controlado, sin avisos de PHP ni rutas internas |
| H-RV01-14 | Ya verificado en código: `sess_expiration = 7200` (`application/config/config.php:383`) | Sin requisito que lo fije (issue #15) |
| Consultas | Ya verificado: 39 `->query(` en `application/`, ninguna con texto concatenado | Sin concatenación |

## Evidencia
Capturas en esta carpeta (`cp11-*.png`, `cp12-*.png`, …) y el CSV con los totales calculados.

## Resultados (2026-11-14 13:09 CST)

Ejecutado por Claude Code con autorización del autor, por HTTP (`curl`, con sesión, cookies y formularios reales) en lugar del navegador. CP-17 se juzga por el HTML o JSON devuelto: sin escapar implica que no se muestra literal.

| Caso o verificación | Resultado | Obtenido |
|---|---|---|
| CP-04, CP-06, CP-07 (URL) | Pasa | Redirección en los tres |
| CP-11 | Pasa | Flujo completo sin error; producto visible |
| CP-12 | Falla (#37) | Hoja 49,2 (neto); reporte +0, y +40 (bruto) tras marcar pagadas |
| CP-17 | Falla (#41) | Sin escapar en listados (JSON), edición y factura |
| CP-18 | Falla (#40) | Marca creada sin token |
| H-RV04-03 | Falla (#42) | El id de sesión no cambia al iniciar sesión |
| H-RV04-04 | Parcial | HttpOnly sí (forzado en `system/libraries/Session/Session.php:296`); Secure no (sin HTTPS en staging) |
| H-RV04-07 | Falla (#44) | Campo de contraseña `type="text"` |
| H-RV03-09 | Falla (#43) | `orders/update/99999` y `orders/printDiv/99999` muestran avisos de PHP |

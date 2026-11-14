# Fase 2 · Paso 5: pruebas, seguridad y correcciones

Es el núcleo de la fase y tiene cuatro momentos: ejecutar las pruebas en los cuatro niveles, verificar los controles de seguridad lógica, registrar cada defecto con su severidad y corregir los críticos y altos. Se ejecutaron dos rondas sobre CodeIgniter 3.1.6: la ronda 1 antes de las correcciones y la ronda 2 después.

| Subpaso | Fecha de diseño | Fecha de desarrollo | Evidencia |
|---|---|---|---|
| 5.0 Decisiones del autor (D-01 a D-07) | Antes de ejecutar | 14/11/2026 | PR #28 y #29; `decisiones-autor.md` |
| 5.1 Casos adicionales CP-15 a CP-20 | — | 14/11/2026 | PR #31 |
| 5.2 Infraestructura de PHPUnit y semáforo rojo | — | 14/11/2026 | PR #32 y PR #33 (cerrado sin fusionar) |
| 5.3 Pruebas automatizadas (ronda 1) | 16/10/2026 al 23/10/2026 | 14/11/2026 | PR #39 |
| 5.4 Pruebas manuales de sistema y seguridad | 21/10/2026 al 26/10/2026 | 14/11/2026 | PR #45; `5.4-pruebas-manuales/` |
| 5.5 Cierre de la ronda 1 | 27/10/2026 | 14/11/2026 | PR #45 |
| 5.6 Correcciones de críticos y altos | — | 14/11/2026 | PR #46 a #50 |
| 5.7 Ronda 2 y línea base de regresión | — | 14/11/2026 | PR #51; etiqueta `pruebas-base-3.1.6` |

## 1. Preparación

**Casos adicionales.** La revisión L4 de la Fase 1 y las revisiones del paso 3 dejaron casos por cubrir. Se añadieron 6, y la trazabilidad llegó a los 20 que pide la Tabla 21 de PHVA:

| Caso | Escenario | Origen |
|---|---|---|
| CP-15 | Ventas intenta cambiar su propio grupo desde Settings | A-01 (H-RV01-02, H-RV04-01) |
| CP-16 | Acceso sin sesión a Reports y a métodos sin permiso | H-01 (H-RV01-03 a H-RV01-06, H-RV03-01) |
| CP-17 | Nombre de producto y datos de cliente con código de script | H-RV03-04 |
| CP-18 | Formulario enviado sin token CSRF | H-RV04-02, H-RV04-04 |
| CP-19 | Vender una unidad | Valor límite pendiente en L4 (D-01) |
| CP-20 | Nombre de producto más largo que el límite | Valor límite pendiente en L4 (D-05) |

**Infraestructura de pruebas.** PHPUnit 9.6 con ci-phpunit-test 3.0.4, la combinación compatible con PHP 7.4 y CodeIgniter 3.1.6; PHPUnit 11 exige PHP 8.2. Las pruebas usan solo la base `stock_test`. La integración continua carga `stock.sql` y PC-0, ejecuta la suite y mide la cobertura con pcov.

**Semáforo de la Tabla 18 en la integración continua:**
- **Rojo:** demostrado en el PR #33. Un error de sintaxis intencional hizo fallar la verificación y bloqueó la fusión.
- **Ámbar:** las pruebas que fallan por un defecto abierto se marcan con su número de issue y se excluyen de la integración continua, con el riesgo registrado (D-06). No se borran ni se modifican.

## 2. Momento 1: ejecutar las pruebas en los cuatro niveles

| Nivel (Tabla 6 de pruebas) | Casos | Cómo se ejecutó |
|---|---|---|
| Unitaria | CP-01, CP-02, CP-03, CP-08, CP-19, CP-20 | PHPUnit |
| Integración | CP-09 | PHPUnit (Orders con Reports) |
| Sistema | CP-04, CP-05, CP-06, CP-07, CP-10, CP-13, CP-14, CP-15, CP-16 | PHPUnit |
| Sistema | CP-11, CP-17, CP-18; CP-04, CP-06 y CP-07 también escribiendo la dirección | Manual por HTTP contra el staging, con sesiones reales de cada usuario PC-0 |
| Aceptación | CP-12 | Manual: el reporte frente a una hoja de cálculo con las mismas ventas (total neto 49,2) |

En total: 16 casos automatizados en 29 pruebas de PHPUnit y 4 casos manuales.

**Orden de ejecución.** La integración descendente del Top-Down (primero el acceso, luego las ventas y después el inventario) no se aplicó como una secuencia separada. Las 29 pruebas se ejecutaron juntas en cada ronda, y los casos manuales se ejecutaron después.

| Ronda | Fecha | Commit | Ejecutados / diseñados | Pasan | Fallan | Bloqueados | Cobertura Orders | Cobertura Products |
|---|---|---|---|---|---|---|---|---|
| 1 · línea base 3.1.6 | 14/11/2026 | `eaa15e4` | 20 / 20 = 100 % | 12 | 8 | 0 | 47,43 % (120/253) | 38,07 % (67/176) |
| 2 · tras las correcciones | 14/11/2026 | `64df852` | 20 / 20 = 100 % | 19 | 1 | 0 | 49,06 % (130/265) | 38,07 % (67/176) |

En la ronda 1 fallaron CP-02, CP-03, CP-08, CP-09, CP-12, CP-15, CP-17 y CP-18. En la ronda 2 solo falla CP-08 (#38, media).

**Desviación:** las pruebas manuales las ejecutó Claude Code por HTTP, con sesión, cookies y formularios reales, en lugar de un navegador. CP-17 se juzgó por el HTML que devuelve el servidor. Más tarde el autor confirmó en su navegador el funcionamiento del token CSRF.

## 3. Momento 2: verificar los controles de seguridad lógica

Se verificaron los controles de la Tabla 13 de seguridad con un usuario por rol de PC-0, incluido el intento de entrar escribiendo la dirección directamente.

| Control (Tabla 13) | Verificación | Ronda 1 | Ronda 2 |
|---|---|---|---|
| Control de acceso por roles | CP-04, CP-06 y CP-07, por la interfaz y escribiendo la dirección | Pasa | Pasa |
| Control de acceso por roles | CP-16: acceso sin sesión a 4 direcciones | Pasa | Pasa |
| Control de acceso por roles | CP-15: Ventas intenta cambiar su propio grupo | **Falla**: pasa a Administrador | Pasa |
| Control de acceso por roles | CP-10 y CP-13: contraseña propia y permisos de un usuario nuevo | Pasa | Pasa |
| Contraseñas | CP-14: hash adaptativo (D-04) | Pasa | Pasa |
| Validación de entradas | CP-17: código de script en productos y ventas | **Falla**: sin escapar | Pasa |
| Validación de entradas | CP-18: formulario sin token CSRF | **Falla**: la marca se crea | Pasa |
| Sesiones | El id de sesión cambia al iniciar sesión (H-RV04-03) | Falla | Falla (#42, media) |
| Sesiones | Atributos de la cookie (H-RV04-04) | Parcial: HttpOnly sí; Secure no, porque el staging no usa HTTPS | Sin cambio |
| Contraseñas | Campo de contraseña en Settings (H-RV04-07) | Falla: `type="text"` | Falla (#44, baja) |
| Errores | Id de venta inexistente (H-RV03-09) | Falla: avisos de PHP con rutas internas | Falla (#43, media) |

## 4. Momento 3: registrar cada defecto con su severidad

Cada fallo se registró con la plantilla Defecto. El autor aprobó la severidad. Las pruebas que fallaban quedaron marcadas con su número de issue y sin modificar.

| Issue | Defecto | Caso | Severidad | Estado |
|---|---|---|---|---|
| #34 | Settings permite cambiar el propio grupo (escalamiento a Administrador) | CP-15 | Crítica | Corregido |
| #35 | La venta supera el stock disponible | CP-02 | Alta | Corregido |
| #36 | Se aceptan cantidades 0 y negativas | CP-03 | Alta | Corregido |
| #37 | El reporte anual no refleja la venta: solo cuenta las pagadas y suma el bruto | CP-09, CP-12 | Alta | Corregido |
| #40 | Los formularios aceptan peticiones sin token CSRF | CP-18 | Alta | Corregido |
| #41 | XSS almacenado en productos y ventas | CP-17 | Alta | Corregido |
| #38 | El alta de producto no exige marca ni categoría | CP-08 | Media | Abierto |
| #42 | El id de sesión no cambia al iniciar sesión | H-RV04-03 | Media | Abierto |
| #43 | Avisos de PHP con un id de venta inexistente | H-RV03-09 | Media | Abierto |
| #44 | Contraseña visible en Settings | H-RV04-07 | Baja | Abierto |

En total: 10 defectos (1 crítico, 5 altos, 3 medios y 1 bajo).

## 5. Momento 4: corregir los críticos y altos

Se corrigieron solo los defectos críticos y altos, como mantenimiento correctivo (Tabla 11 de mantenimiento). Cada corrección cumplió estas reglas:
- su propia rama `fix/`;
- el cambio mínimo, sin funcionalidad nueva;
- la causa identificada con `archivo:línea` antes de editar;
- la prueba asociada sin modificar: solo se quitó la marca de defecto abierto;
- la suite completa ejecutada y la lista L1 aplicada al diff;
- la integración continua en verde y la firma SQA en el PR.

| Issue | Causa | Corrección | PR | Verificación |
|---|---|---|---|---|
| #34 | `Users.php:288` pasaba el campo `groups` a la edición del usuario | Settings ignora ese campo; la edición de usuarios del Administrador no cambia | #46 | CP-15 pasa |
| #35 y #36 | Solo se validaba el producto (`Orders.php:101`); el stock se descontaba sin comprobarlo y sin transacción (`Model_orders.php:57-82`) | Cantidad entera mayor que 0. La venta, sus líneas y el descuento van en una transacción, que se cancela si el stock no alcanza | #47 | CP-02 y CP-03 pasan; CP-01, CP-05 y CP-19 siguen pasando |
| #37 | El reporte filtraba solo las ventas pagadas y sumaba el bruto (`Model_reports.php:19,40`; `Reports.php:41`) | Cuenta todas las ventas (D-08) y suma el neto (D-03) | #48 | CP-09 pasa; CP-12 cuadra en 49,2 |
| #41 | Datos de cliente, empresa y producto mostrados sin escapar (`Orders.php`, `Products.php`, vistas de ventas) | Salidas escapadas en ventas y productos | #49 | CP-17 pasa |
| #40 | Protección CSRF desactivada (`config.php:452`) | Protección activa; el token se añade a todos los formularios y a las peticiones AJAX desde la plantilla común | #50 | CP-18 pasa; CP-11, CP-12 y CP-17 siguen pasando; el autor lo confirmó en el navegador |

**D-08.** Al corregir #37 hubo que decidir si el reporte cuenta las ventas no pagadas. El autor decidió contarlas todas, porque CP-09 registra una venta, que nace como no pagada, y espera verla en el reporte. La decisión se registró y se firmó. **Desviación:** se tomó después de ejecutar CP-09 y CP-12, no antes.

**Mejoras del Top-Down:**

| Mejora | Estado |
|---|---|
| Ventas atómicas (H-04) | **Aplicada en la creación de ventas** (PR #47). La edición, la eliminación y la concurrencia siguen pendientes en #16 |
| Permisos centralizados (H-01) | **No aplicada.** Es una mejora preventiva, no un defecto crítico o alto. La regla de la fase solo permite corregir defectos registrados de esas severidades. Sigue en el issue #4 |

**Sin cambiar ningún resultado de prueba:** ninguna prueba se modificó para que pasara. Solo se retiró la marca de defecto abierto de las que ya pasaban.

## 6. Línea base de regresión (subpaso 5.7)

Después de las correcciones se ejecutó la suite completa sobre `main` (`64df852`): 29 pruebas, 27 pasan y 2 fallan, las dos de CP-08 (#38). La salida se guardó en `5.7-suite-base-3.1.6.txt`. La etiqueta `pruebas-base-3.1.6` marca ese estado como la referencia que la actualización de CodeIgniter no debe empeorar.

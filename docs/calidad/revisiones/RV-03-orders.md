# RV-03 · Código del módulo Orders

## 1. Datos de la revisión

| Campo | Contenido |
|---|---|
| Producto y versión | RV-03: `application/controllers/Orders.php` (392 líneas), `application/models/Model_orders.php` (184) y `application/views/orders/{create,edit,index}.php` (355, 415, 170); commit `ad63b11` |
| Riesgo y técnica planificada | Alto, inspección (Tabla 7 de revisiones) |
| Técnica aplicada | Verificación de escritorio (autor) + segunda pasada (Claude Code como apoyo, no como revisor humano independiente) |
| Lista usada | L1 (con L3-5, concurrencia, en la pregunta 6) |
| Participantes y roles | Productor: autor original del código (proyecto de terceros). Revisor y registrador: César Roque. Apoyo: Claude Code |
| Fechas de las pasadas | Autor (César Roque): 03/11/2026 · Claude: 14/11/2026 |
| Desviación | La Tabla 7 pide inspección formal con tres personas; se trabaja solo. Se compensa con la lista y la doble pasada |

## 2. Respuestas a la lista

| N.º | Pregunta | Respuesta (Sí / No / N/A) | Evidencia (`archivo:línea`) | Hallazgo |
|---|---|---|---|---|
| 1 | ¿El controlador verifica la sesión y el permiso del rol antes de ejecutar la acción? | No (parcial) | Sesión en el constructor (`application/controllers/Orders.php:11`); puerta de permiso en `index` (`:25`), `create` (`:95`), `update` (`:162`), `remove` (`:219`) y `printDiv` (`:251`); sin puerta en `fetchOrdersData` (`:37`), `getProductValueById` (`:135`) y `getTableProductRow` (`:149`) | H-RV03-01 |
| 2 | ¿Las entradas se validan en el servidor (tipo, rango, campos obligatorios)? | No | Única regla: `product[]` obligatorio (`application/controllers/Orders.php:101`, `:172`), confirmado por el autor. La cantidad se guarda y se resta sin validar (`application/models/Model_orders.php:67`, `:76`); los datos del cliente no tienen reglas (`:42-44`); importes, precio unitario y estado de pago llegan del POST (`:46-52`, `:68-69`, `:113`) | H-RV03-02, H-RV03-03 |
| 3 | ¿Las consultas usan Query Builder o parámetros, sin concatenar texto del usuario? | Sí | Consultas con parámetros enlazados (`application/models/Model_orders.php:14-15`, `:31-32`, `:90-91`, `:180-181`); escrituras con Query Builder (`:57`, `:72`, `:117-118`, `:137-138`, `:169-173`) | — |
| 4 | ¿Las contraseñas se guardan con hash y nunca se muestran? | N/A | El módulo no maneja contraseñas | — |
| 5 | ¿Las salidas hacia la vista se escapan para evitar inyección de código? | No | Datos del cliente sin escapar: en la factura (`application/controllers/Orders.php:298-300`), en los atributos `value` de la edición (`application/views/orders/edit.php:60`, `:67`, `:74`) y en el listado, que los envía en JSON (`application/controllers/Orders.php:75-76`) y DataTables los inserta como HTML sin función de renderizado (`application/views/orders/index.php:117-120`). También sin escapar: nombre de la empresa (`application/controllers/Orders.php:286`) y del producto (`:325`; `application/views/orders/create.php:98`, `:217`; `application/views/orders/edit.php:103`) | H-RV03-04 |
| 6 | ¿El registro de la venta y el descuento de stock van en una sola transacción, con la comprobación de stock dentro de ella? | No | Sin transacción: venta, líneas y descuento se escriben por separado (`application/models/Model_orders.php:57-82`), sin comprobar el stock. **Concurrencia (L3-5):** el stock se lee (`:75`), se resta en PHP (`:76`) y se escribe (`:81`) sin bloqueo de fila ni actualización atómica; dos ventas simultáneas del mismo producto leen el mismo valor y una de las restas se pierde. `update` repite el patrón (`:153-157`), revierte stock, borra y reinserta líneas sin transacción y siempre devuelve `true` (`:117-160`). `remove` no repone stock (`:166-176`) | H-RV03-05 a H-RV03-08 |
| 7 | ¿Los errores se manejan sin mostrar detalles técnicos al usuario? | No | Con `CI_ENV=development` (por defecto, `index.php:56`; es el del staging), CodeIgniter activa `display_errors` (`index.php:69-70`) y `db_debug` (`application/config/database.php:86`). `update` y `printDiv` no comprueban que la venta exista (`application/controllers/Orders.php:196-199`, `:256-261`): un `id` inexistente muestra avisos de PHP con rutas internas | H-RV03-09 |
| 8 | ¿Se respetan los nombres, la estructura y los comentarios acordados por el equipo? | No | Comentario incorrecto (`application/controllers/Orders.php:245-248`), mensaje equivocado (`:234`), erratas (`:239`, `:55`); conteo que carga todas las filas (`application/models/Model_orders.php:90-92`) | H-RV03-11 |
| 9 | ¿Hay lógica duplicada que debería estar en un solo lugar? | Sí | Líneas y descuento de stock duplicados (`application/models/Model_orders.php:63-82` y `:141-158`); impuestos duplicados (`application/controllers/Orders.php:119-124` y `:190-193`); JavaScript de cálculo duplicado entre `create.php` y `edit.php` | H-RV03-10 |
| 10 | ¿El cambio tiene al menos un caso de prueba, enlazado a un requisito? | Sí | Orders está cubierto por CP-01 a CP-05, CP-09 y CP-11 (RQ-01 a RQ-04, `docs/calidad/trazabilidad.md`); varios hallazgos de esta revisión no tienen caso (sección 5) | — |

Cada "No" es un hallazgo y va a la sección 3.

## 3. Lista de problemas (Tabla 16)

| ID | Ubicación | Descripción objetiva | Tipo | Gravedad | Encontrado por | Responsable | Estado |
|---|---|---|---|---|---|---|---|
| H-RV03-01 | `application/controllers/Orders.php:37`, `:135`, `:149` | Tres métodos públicos devuelven ventas o productos sin comprobar permiso (H-01) | Defecto de diseño | Mayor (permisos, datos) | Ambos | César Roque | Abierto |
| H-RV03-02 | `application/controllers/Orders.php:101`, `:172`; `application/models/Model_orders.php:67`, `:76`, `:146`, `:154` | La cantidad vendida no se valida en el servidor: se aceptan 0, negativos, decimales o texto, y se restan del stock | Omisión | Mayor (existencias) | Ambos | César Roque | Abierto |
| H-RV03-03 | `application/models/Model_orders.php:46-52`, `:68-69`, `:106-113`, `:147-148`; `application/views/orders/create.php:281-282` | Importes bruto y neto, cargos, descuento, precio unitario, importe por línea y estado de pago se guardan tal como llegan del navegador, sin recalcularse en el servidor | Defecto de diseño | Mayor (dinero) | Ambos | César Roque | Abierto |
| H-RV03-04 | `application/controllers/Orders.php:75-76` con `application/views/orders/index.php:117-120`; `application/controllers/Orders.php:286`, `:298-300`, `:325`; `application/views/orders/edit.php:60`, `:67`, `:74`, `:103`; `application/views/orders/create.php:98`, `:217` | Datos del cliente, nombre de la empresa y nombre del producto se muestran sin escapar en el listado, la factura y la edición (posible inyección de código, XSS) | Defecto de diseño | Mayor (datos) | Ambos | César Roque | Abierto |
| H-RV03-05 | `application/models/Model_orders.php:57-82` | Sin transacción: la venta, sus líneas y el descuento de stock se escriben por separado y no se comprueba el stock antes de descontar (H-04) | Defecto de diseño | Mayor (existencias) | Ambos | César Roque | Abierto |
| H-RV03-06 | `application/models/Model_orders.php:75-81`, `:153-157` | El descuento lee, resta en PHP y escribe sin bloqueo de fila; con dos ventas simultáneas del mismo producto se pierde una resta (L3-5) | Defecto de diseño | Mayor (existencias) | Ambos | César Roque | Abierto |
| H-RV03-07 | `application/models/Model_orders.php:117-160` | La edición de una venta revierte stock, borra y reinserta líneas sin transacción, y siempre responde `true` (`:160`): un fallo a mitad de camino pasa inadvertido | Defecto de diseño | Mayor (existencias) | Ambos | César Roque | Abierto |
| H-RV03-08 | `application/models/Model_orders.php:166-176` | Eliminar una venta no repone el stock (relación con H-RV01-10) | Defecto de diseño | Mayor (existencias) | Ambos | César Roque | Abierto |
| H-RV03-09 | `index.php:56`, `:69-70`; `application/config/database.php:86`; `application/controllers/Orders.php:196-199`, `:256-261` | En el entorno de desarrollo (el del staging) se muestran errores de PHP y de la base; un `id` de venta inexistente produce avisos con rutas internas | Defecto de diseño | Menor | Ambos | César Roque | Abierto |
| H-RV03-10 | `application/models/Model_orders.php:63-82` y `:141-158`; `application/controllers/Orders.php:119-124` y `:190-193` | Lógica de líneas, stock e impuestos duplicada entre crear y editar; una corrección del descuento debe aplicarse dos veces | Defecto de diseño | Menor | Ambos | César Roque | Abierto |
| H-RV03-11 | `application/controllers/Orders.php:55`, `:234`, `:239`, `:245-248`; `application/models/Model_orders.php:90-92` | Comentarios y mensajes que no corresponden al código, erratas y conteo que carga todas las filas | Forma | Menor | Ambos | César Roque | Abierto |
| H-RV03-12 | `application/models/Model_orders.php:99`, `:114` | Al editar una venta, `user_id` se sustituye por el del editor: se pierde quién la registró | Defecto de diseño | Mayor (datos) | Ambos | César Roque | Abierto |
| H-RV03-13 | `application/models/Model_orders.php:39`; `stock.sql:304-305` | El número de factura usa 4 caracteres hexadecimales (65 536 valores) y no tiene restricción de unicidad: puede repetirse | Defecto de diseño | Mayor (datos) | Ambos | César Roque | Abierto |

Criterio de gravedad (Tabla 17): **mayor** si corregirlo toma más de 1 hora-persona, o si afecta permisos, dinero, existencias o datos (aunque sea fácil de corregir). **Menor** en cualquier otro caso.

## 4. Métricas

| Métrica | Valor |
|---|---|
| Ep: preparación del autor (horas) | 1,0 |
| Ea: revisión del autor (horas) | 0,4 |
| Er: corrección (horas, si aplica) | N/A: todavía no hay correcciones; se mide en el paso 5.6 |
| WPS: tamaño revisado (líneas, con `wc -l`) | 1516 |
| Hallazgos mayores / menores | 10 / 3 |
| Densidad de error (hallazgos / WPS × 1000) | 13 / 1516 × 1000 = 8,6 |
| Hallazgos encontrados solo por el autor | 0 |
| Hallazgos encontrados solo por Claude | 0 |
| Hallazgos encontrados por ambos | 13 |

## 5. Informe resumen y decisión (Tabla 18)

| Campo | Contenido |
|---|---|
| Decisión | Aceptar con correcciones |
| Justificación | Hay 10 hallazgos mayores que afectan permisos, dinero, existencias y datos, pero ninguno obliga a rediseñar el módulo ni impide verificarlo, que es el criterio de rechazo de la Tabla 18. Cada uno tiene una corrección acotada o un caso de prueba que lo confirma, y el líder puede verificar la corrección sin otra reunión: (1) H-RV03-02 (cantidad sin validar) se confirma con CP-02 y CP-03 (cantidades 0 y −1); (2) H-RV03-05 (sin transacción ni comprobación de stock, H-04) se confirma con CP-01, CP-02 y CP-19 y, si resulta crítico o alto, se corrige en el paso 5.6 envolviendo la venta y el descuento en una transacción dentro de `Model_orders`; (3) H-RV03-07 (edición sin transacción) se confirma con CP-05, consultando el stock antes y después; (4) H-RV03-01 (métodos sin permiso, H-01) se confirma con CP-16 y con CP-06 por URL directa; (5) H-RV03-04 (salidas sin escapar) se confirma con CP-17. Los mayores que exigirían cambiar el esquema de la base o la lógica de cálculo (recalcular importes en el servidor en H-RV03-03, bloqueo de fila en H-RV03-06, reposición de stock al eliminar en H-RV03-08, conservación de `user_id` en H-RV03-12 y unicidad del número de factura en H-RV03-13) no tienen caso de prueba en esta iteración. Se registran como issues con su plan (paso 3.4, Tabla 9 de pruebas). Solo se corrigen en esta iteración los defectos que el paso 5 confirme como críticos o altos (regla 13). El cierre de hallazgos mayores (Tabla 19) queda abierto hasta verificar las correcciones en el paso 5 |
| Hallazgos que pasan a pruebas | H-RV03-02 → CP-03 (0 y −1), CP-02. H-RV03-05 → CP-01, CP-02, CP-19. H-RV03-01 → CP-16 y CP-06 por URL directa. H-RV03-07 → CP-05 con consulta del stock antes y después. H-RV03-09 → verificaciones del guion 5.4. H-RV03-04 → CP-17 (nombre del producto) |
| Hallazgos sin caso de prueba | H-RV03-03 (importes manipulados), H-RV03-06 (concurrencia), H-RV03-08 (stock tras eliminar una venta), H-RV03-12 (`user_id`), H-RV03-13 (número de factura repetido) y H-RV03-04 en los datos del cliente |
| Issues creados | Existentes: #6 (H-04: cubre H-RV03-05), #4 (H-01: cubre H-RV03-01). Nuevos (paso 3.4): #16 (H-RV03-06, H-RV03-07, H-RV03-08), #17 (H-RV03-03), #18 (H-RV03-04), #19 (H-RV03-12, H-RV03-13). Defecto (paso 5): #35 y #36 (H-RV03-02, CP-02 y CP-03), #41 (H-RV03-04, CP-17), #43 (H-RV03-09) |

**Firma del revisor:** César Roque · fecha: 14/11/2026

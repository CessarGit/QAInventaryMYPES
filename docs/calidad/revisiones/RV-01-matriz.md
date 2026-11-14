# RV-01 · Matriz de roles y permisos contra el código

## 1. Datos de la revisión

| Campo | Contenido |
|---|---|
| Producto y versión | RV-01, `docs/calidad/matriz-roles.md` (Tabla 5) contrastada con `application/controllers/*.php` y `application/views/groups/{create,edit}.php`, commit `ad63b11` |
| Riesgo y técnica planificada | Alto, inspección (Tabla 7 de revisiones) |
| Técnica aplicada | Verificación de escritorio (autor) + segunda pasada (Claude Code como apoyo, no como revisor humano independiente) |
| Lista usada | L2 |
| Participantes y roles | Productor: autor original del código (proyecto de terceros). Revisor y registrador: César Roque. Apoyo: Claude Code |
| Fechas de las pasadas | Autor (César Roque): 01/11/2026 · Claude: 13/11/2026 |
| Desviación | La Tabla 7 pide inspección formal con tres personas; se trabaja solo. Se compensa con la lista y la doble pasada |

## 2. Respuestas a la lista

| N.º | Pregunta | Respuesta (Sí / No / N/A) | Evidencia (`archivo:línea`) | Hallazgo |
|---|---|---|---|---|
| 1 | ¿Cada requisito es completo, sin ambigüedades y verificable? | No | El "CRUD" de Settings del Administrador no define qué es crear o eliminar una cuenta propia y no existe en el código (`application/controllers/Users.php:234-241`); la "C" de Company es ambigua con una sola empresa (`application/controllers/Company.php:52`) | H-RV01-01, H-RV01-07 |
| 2 | ¿Cada rol tiene definidas todas las operaciones sobre cada módulo (crear, leer, actualizar, eliminar)? | No | Las 60 celdas de la matriz están definidas, pero el módulo Dashboard existe en el código sin fila en la matriz (`application/controllers/Dashboard.php:24-35`) | H-RV01-08 |
| 3 | ¿La matriz es coherente con las funciones del módulo y con el flujo principal? | No | Settings opera solo sobre la cuenta propia; la configuración global (impuestos, moneda) está en Company (`application/controllers/Company.php:37-46`), que solo admite actualizar, y el R de Logística no es representable | H-RV01-01, H-RV01-07 |
| 4 | ¿Cada rol puede completar su trabajo con los permisos que tiene? | Sí | Ventas vende con Orders CR y Products R (CP-13); el Gerente completa el flujo de CP-11; Bodega gestiona productos y consulta tiendas; el Administrador gestiona usuarios y grupos | — |
| 5 | ¿Existe una vía definida para corregir los errores de cada rol? | No | Ventas no puede editar ni anular una venta (CR) y la matriz no indica que deba hacerlo el Gerente; si el Gerente la elimina, el stock no se repone (`application/models/Model_orders.php:166-176`) | H-RV01-10, H-RV01-11 |
| 6 | ¿Los permisos respetan el mínimo privilegio y la separación de funciones? | No | La separación entre administración (Users, Groups) y operación es correcta; Logística tiene el mismo CRUD que Bodega en los datos maestros sin justificación; en el código, los métodos sin puerta conceden lecturas y escrituras que la matriz niega (H-01) | H-RV01-12, H-RV01-03, H-RV01-04 |
| 7 | ¿Cada requisito se enlaza con al menos un caso de prueba? | Sí | RQ-01 a RQ-06 enlazados en `docs/calidad/trazabilidad.md`, sección 1. Observación: los casos cubren solo parte de las celdas de la matriz (CP-04, 05, 06, 07, 10 y 13); por ejemplo, Stores R de Bodega o Company R de Logística no tienen caso | — |
| 8 | ¿Se identifican los requisitos que faltan? | No | Sin requisito: acceso al Dashboard, política de contraseña, cierre de sesión por inactividad (Tabla 13 de seguridad), autenticación por token; catálogo de clientes (H-05, fuera de alcance) | H-RV01-08, H-RV01-13, H-RV01-14 |

Cada "No" es un hallazgo y va a la sección 3.

## 3. Lista de problemas (Tabla 16)

| ID | Ubicación | Descripción objetiva | Tipo | Gravedad | Encontrado por | Responsable | Estado |
|---|---|---|---|---|---|---|---|
| H-RV01-01 | `matriz-roles.md`, fila Settings (Administrador); `application/controllers/Users.php:234-241` | La matriz da CRUD al Administrador en Settings, pero el código solo permite leer y actualizar la cuenta propia: no hay creación ni eliminación de cuenta (H-03). El autor añade que la eliminación de la cuenta propia no está prevista por el manejo de sesiones (la sesión identifica la cuenta por su `id`, `application/controllers/Users.php:241`) | Inconsistencia | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV01-02 | `application/controllers/Users.php:288`; `application/models/Model_users.php:62-67` | La actualización de Settings acepta el campo `groups` del POST y cambia el grupo del propio usuario; la matriz limita Settings a RU de la cuenta propia (A-01) | Defecto de diseño | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV01-03 | `application/controllers/Brands.php:38`, `:75`; `Category.php:37`, `:51`; `Stores.php:34`, `:47`; `Products.php:38`; `Orders.php:37`, `:135`, `:149`; `Attributes.php:33`, `:44`, `:214`, `:240` | Los métodos de consulta (`fetch*`, `getProduct*`) devuelven datos sin comprobar el permiso de lectura; por ejemplo, Bodega puede leer las ventas, que la matriz le niega (H-01) | Defecto de diseño | Mayor (permisos, datos) | Ambos | César Roque | Abierto |
| H-RV01-04 | `application/controllers/Attributes.php:199`, `:251`, `:290`, `:335` | Los valores de atributo se gestionan sin permiso: Ventas y el Administrador, sin acceso a Attributes en la matriz, pueden usarlos. El autor identificó la eliminación (`removeValue`, `:335`); Claude, además, la creación (`createValue`, `:251`), la edición (`updateValue`, `:290`) y la pantalla de alta (`addvalue`, `:199`) (H-01) | Defecto de diseño | Mayor (permisos, datos) | Ambos (alcance ampliado por Claude) | César Roque | Abierto |
| H-RV01-05 | `application/controllers/Products.php:160` | `upload_image` es un método público, accesible por URL, que sube archivos sin comprobar permiso | Defecto de diseño | Mayor (datos) | Ambos | César Roque | Abierto |
| H-RV01-06 | `application/controllers/Reports.php:7-12` | Reports no exige sesión en su constructor; hoy la deniega de forma indirecta porque `viewReports` no está en un permiso vacío (H-01) | Inconsistencia | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV01-07 | `matriz-roles.md`, fila Company; `application/controllers/Company.php:25`, `:52` | La C de la matriz no es implementable (una sola empresa, `id = 1`, sin inserción en `Model_company`) y el R de Logística no es representable: el único permiso es `updateCompany` (A-05) | Ambigüedad | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV01-08 | `application/controllers/Dashboard.php:9`, `:26-29`, `:32` | El módulo Dashboard no tiene fila en la matriz; muestra totales del negocio a cualquier rol y su vista de administrador depende de `user_id == 1` (A-04) | Omisión | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV01-09 | `application/controllers/Brands.php:48` | El botón de editar marca se muestra con `viewBrand` en lugar de `updateBrand`; no concede acceso, porque la puerta de actualización existe (`application/controllers/Brands.php:138`) | Forma | Menor | Ambos | César Roque | Abierto |
| H-RV01-10 | `application/models/Model_orders.php:166-176` | Al eliminar una venta no se repone el stock; la única vía de corrección (el Gerente elimina) descuadra las existencias | Defecto de diseño | Mayor (existencias) | Ambos | César Roque | Abierto |
| H-RV01-11 | `matriz-roles.md`, fila Orders | La matriz no define cómo se corrige una venta errónea registrada por Ventas | Omisión | Mayor (dinero) | Ambos | César Roque | Abierto |
| H-RV01-12 | `matriz-roles.md`, filas Brands, Categories, Attributes y Products | Logística tiene el mismo CRUD que Bodega en los datos maestros, sin justificación; puede exceder el mínimo privilegio | Ambigüedad | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV01-13 | `matriz-roles.md`; Tabla 13 de seguridad | Los requisitos no fijan la política de contraseña ni el cierre de sesión por inactividad | Omisión | Menor | Ambos | César Roque | Abierto |
| H-RV01-14 | `application/` (sin referencias a token); `application/config/config.php:383` | No existe autenticación por token ni un tiempo de sesión definido por requisito. Verificación: no hay autenticación por token en `application/` (confirmado); la sesión sí caduca a las 2 horas por el valor por defecto de CodeIgniter (`sess_expiration = 7200`, `application/config/config.php:383`), pero ningún requisito lo fija (confirmado en parte) | Omisión | Mayor (permisos) | Autor | César Roque | Abierto |

Criterio de gravedad (Tabla 17): **mayor** si corregirlo toma más de 1 hora-persona, o si afecta permisos, dinero, existencias o datos (aunque sea fácil de corregir). **Menor** en cualquier otro caso.

## 4. Métricas

| Métrica | Valor |
|---|---|
| Ep: preparación del autor (horas) | 1,0 |
| Ea: revisión del autor (horas) | 1,15 |
| Er: corrección (horas, si aplica) | 0,2 (corrección de la matriz, H-03 opción A: de la decisión del autor a la fusión del PR #13, 15:19 a 15:30 UTC del 14/11/2026) |
| WPS: tamaño revisado | 60 celdas de la matriz (12 módulos × 5 roles) |
| Hallazgos mayores / menores | 12 / 2 |
| Densidad de error (hallazgos / WPS × 1000) | 14 / 60 × 1000 = 233,3 hallazgos por mil celdas |
| Hallazgos encontrados solo por el autor | 1 (H-RV01-14) |
| Hallazgos encontrados solo por Claude | 0 (H-RV01-04: alcance ampliado por Claude dentro de un hallazgo común) |
| Hallazgos encontrados por ambos | 13 |

## 5. Informe resumen y decisión (Tabla 18)

| Campo | Contenido |
|---|---|
| Decisión | Aceptar con correcciones |
| Justificación | Hay 12 hallazgos mayores, pero ninguno obliga a rehacer la matriz ni impide verificarla, que es el criterio de rechazo de la Tabla 18: todas las operaciones de la matriz tienen un identificador de permiso en el código, salvo la C y la D de Settings del Administrador y la C de Company (Anexo B), y el mapeo ya sirve de base para PC-0 (regla 16). Las correcciones son acotadas y el líder puede verificarlas sin otra reunión: (1) en la matriz, H-RV01-01 (H-03) se resuelve con la opción A del Anexo C, elegida por el autor (Settings del Administrador pasa de CRUD a RU propia), en un commit propio sobre `matriz-roles.md`, y H-RV01-07, H-RV01-11 y H-RV01-12 requieren una decisión del autor sobre la fila correspondiente; (2) en el código, H-RV01-02 (A-01) se confirma con CP-10 y CP-15, H-RV01-03 a H-RV01-06 (H-01) con CP-16 y CP-06 por URL directa, y H-RV01-10 con CP-01 a CP-03 y la consulta del stock; (3) H-RV01-14 se verifica en el guion 5.4. Lo que exigiría funcionalidad nueva (autenticación por token en H-RV01-14, política de contraseña en H-RV01-13, Dashboard dentro de la matriz en H-RV01-08) o la centralización de la autorización (H-01) queda fuera del alcance de la iteración (regla 1) y se registra como issue con su plan (paso 3.4). Solo se corrigen en el código los defectos que el paso 5 confirme como críticos o altos (regla 13). El cierre de hallazgos mayores (Tabla 19) queda abierto hasta aplicar la corrección de la matriz y verificar las del código en el paso 5 |
| Hallazgos que pasan a pruebas | H-RV01-02 → CP-10, CP-15. H-RV01-03, 04, 05 y 06 → CP-16 y CP-06 por URL directa. H-RV01-10 → CP-01 a CP-03 y consulta del stock tras eliminar una venta. H-RV01-14 → verificaciones de sesión del guion 5.4 |
| Hallazgos sin caso de prueba | H-RV01-01, H-RV01-07, H-RV01-11 y H-RV01-12 (se resuelven en la matriz, no con un caso), H-RV01-08, H-RV01-09 y H-RV01-13 |
| Issues creados | Existentes: #4 (H-01: cubre H-RV01-03 a H-RV01-06), #8 (A-04: H-RV01-08). Nuevos (paso 3.4): #14 (H-RV01-07, H-RV01-11, H-RV01-12), #15 (H-RV01-14), #16 (H-RV01-10). H-RV01-01 corregido en el PR #13. H-RV01-02 (A-01) tendrá su issue con la plantilla Defecto si se confirma en el paso 5 |

**Firma del revisor:** César Roque · fecha: 14/11/2026

---

## Anexo A. Identificadores de permiso del código

Treinta y seis identificadores asignables, los mismos en `application/views/groups/create.php:64-142` y `application/views/groups/edit.php:67-229`. Los treinta y seis se comprueban al menos una vez en un controlador; no hay permisos asignables sin comprobación ni permisos sin operación en la matriz.

| Módulo | Identificadores | Puerta (el método se bloquea sin el permiso) | Solo condiciona botones |
|---|---|---|---|
| Users | `createUser`, `viewUser`, `updateUser`, `deleteUser` | `Users.php:43`, `:21`, `:98`, `:193` | — |
| Groups | `createGroup`, `viewGroup`, `updateGroup`, `deleteGroup` | `Groups.php:42`, `:24`, `:81`, `:124` | — |
| Settings | `updateSetting` | `Users.php:236` | — |
| Profile | `viewProfile` | `Users.php:219` | — |
| Brands | `createBrand`, `viewBrand`, `updateBrand`, `deleteBrand` | `Brands.php:93`, `:23`, `:138`, `:187` | `viewBrand` `:48`, `deleteBrand` `:52` |
| Categories | `createCategory`, `viewCategory`, `updateCategory`, `deleteCategory` | `Category.php:90`, `:24`, `:135`, `:184` | `updateCategory` `:62`, `deleteCategory` `:66` |
| Attributes | `createAttribute`, `viewAttribute`, `updateAttribute`, `deleteAttribute` | `Attributes.php:78`, `:23`, `:120`, `:168` | — |
| Stores | `createStore`, `viewStore`, `updateStore`, `deleteStore` | `Stores.php:85`, `:23`, `:129`, `:180` | `updateStore` `:58`, `deleteStore` `:62` |
| Products | `createProduct`, `viewProduct`, `updateProduct`, `deleteProduct` | `Products.php:92`, `:27`, `:195`, `:275` | `updateProduct` `:49`, `deleteProduct` `:53` |
| Orders | `createOrder`, `viewOrder`, `updateOrder`, `deleteOrder` | `Orders.php:95`, `:25` y `:251`, `:162`, `:219` | `viewOrder` `:54`, `updateOrder` `:58`, `deleteOrder` `:62` |
| Reports | `viewReports` | `Reports.php:20` | — |
| Company | `updateCompany` | `Company.php:25` | — |

Rutas relativas a `application/controllers/`.

## Anexo B. Mapeo de la Tabla 5 a los permisos del código (base de PC-0)

Base para los datos de prueba PC-0 (regla 16 de CLAUDE.md). Para cada rol, el grupo de pruebas recibe los identificadores de las operaciones que la Tabla 5 le concede.

| Módulo | Operación de la Tabla 5 (roles) | Identificador en el código | Dónde se comprueba | Discrepancia |
|---|---|---|---|---|
| Users | C, R, U, D (Administrador) | `createUser`, `viewUser`, `updateUser`, `deleteUser` | `Users.php:43`, `:21`, `:98`, `:193` | — |
| Groups | C, R, U, D (Administrador) | `createGroup`, `viewGroup`, `updateGroup`, `deleteGroup` | `Groups.php:42`, `:24`, `:81`, `:124` | — |
| Settings | C (Administrador) | — | — | Operación sin permiso ni función (H-RV01-01) |
| Settings | R (los cinco roles; propia en los cuatro operativos) | `updateSetting` (implícito) | `Users.php:236` | No hay permiso de solo lectura; para RU es suficiente |
| Settings | U (los cinco roles; propia) | `updateSetting` | `Users.php:236`, limitado a la cuenta de la sesión en `:241` | Acepta `groups` del POST (H-RV01-02) |
| Settings | D (Administrador) | — | — | Operación sin permiso ni función (H-RV01-01) |
| Brands | C, U, D (Gerente, Bodega, Logística) | `createBrand`, `updateBrand`, `deleteBrand` | `Brands.php:93`, `:138`, `:187` | Botón de edición con `viewBrand` (H-RV01-09) |
| Brands | R (Gerente, Bodega, Logística) | `viewBrand` | `Brands.php:23` | `fetchBrandData` `:38` y `fetchBrandDataById` `:75` sin puerta (H-RV01-03) |
| Categories | C, U, D (Gerente, Bodega, Logística) | `createCategory`, `updateCategory`, `deleteCategory` | `Category.php:90`, `:135`, `:184` | — |
| Categories | R (Gerente, Bodega, Logística) | `viewCategory` | `Category.php:24` | `fetchCategoryDataById` `:37` y `fetchCategoryData` `:51` sin puerta (H-RV01-03) |
| Attributes | C (Gerente, Bodega, Logística) | `createAttribute` | `Attributes.php:78` | `createValue` `:251` sin permiso (H-RV01-04) |
| Attributes | R (Gerente, Bodega, Logística) | `viewAttribute` | `Attributes.php:23` | `fetchAttributeDataById` `:33`, `fetchAttributeData` `:44`, `addvalue` `:199`, `fetchAttributeValueData` `:214`, `fetchAttributeValueById` `:240` sin permiso (H-RV01-03, H-RV01-04) | 
| Attributes | U (Gerente, Bodega, Logística) | `updateAttribute` | `Attributes.php:120` | `updateValue` `:290` sin permiso (H-RV01-04) |
| Attributes | D (Gerente, Bodega, Logística) | `deleteAttribute` | `Attributes.php:168` | `removeValue` `:335` sin permiso (H-RV01-04) |
| Stores | C, U, D (Gerente) | `createStore`, `updateStore`, `deleteStore` | `Stores.php:85`, `:129`, `:180` | — |
| Stores | R (Gerente, Bodega, Logística) | `viewStore` | `Stores.php:23` | `fetchStoresDataById` `:34` y `fetchStoresData` `:47` sin puerta (H-RV01-03) |
| Products | C (Gerente, Bodega, Logística) | `createProduct` | `Products.php:92` | `upload_image` `:160` sin permiso (H-RV01-05) |
| Products | R (Gerente, Ventas, Bodega, Logística) | `viewProduct` | `Products.php:27` | `fetchProductData` `:38`, `Orders::getProductValueById` `Orders.php:135` y `getTableProductRow` `:149` sin puerta (H-RV01-03) |
| Products | U, D (Gerente, Bodega, Logística) | `updateProduct`, `deleteProduct` | `Products.php:195`, `:275` | — |
| Orders | C (Gerente, Ventas) | `createOrder` | `Orders.php:95` | — |
| Orders | R (Gerente, Ventas) | `viewOrder` | `Orders.php:25`, `:251` | `fetchOrdersData` `:37` sin puerta: roles sin acceso leen las ventas (H-RV01-03) |
| Orders | U, D (Gerente) | `updateOrder`, `deleteOrder` | `Orders.php:162`, `:219` | La eliminación no repone stock (H-RV01-10) |
| Reports | R (Gerente, Ventas, Bodega, Logística) | `viewReports` | `Reports.php:20` | Sin `not_logged_in()` en el constructor (H-RV01-06) |
| Company | C (Gerente) | — | — | No implementable con una sola empresa (H-RV01-07) |
| Company | R (Gerente, Logística) | `updateCompany` (implícito) | `Company.php:25` | Sin permiso de solo lectura: el R de Logística exige conceder U (H-RV01-07) |
| Company | U (Gerente) | `updateCompany` | `Company.php:25` | — |
| Profile | R (los cinco roles) | `viewProfile` | `Users.php:219` | — |
| Dashboard | Sin fila en la matriz | — (solo exige sesión) | `Dashboard.php:9` | Módulo fuera de la matriz (H-RV01-08) |

Rutas relativas a `application/controllers/`, salvo indicación.

## Anexo C. Propuesta para H-03 (Settings del Administrador), a decidir por el autor

| Opción | Cambio en la Tabla 5 | Efecto |
|---|---|---|
| A (recomendada) | Settings del Administrador pasa de CRUD a RU (propia), como el resto de roles | Coincide con el código; no exige funcionalidad nueva (regla 1). La configuración global sigue en Company |
| B | Mantener CRUD y añadir una configuración global en Settings | Exige desarrollo nuevo, excluido del alcance |
| C | Mantener la matriz y registrar la diferencia como hallazgo permanente | La matriz queda inverificable: los casos no tendrían resultado esperado alcanzable |

Si se aplica A: commit propio sobre `matriz-roles.md` con la nota "corrige la Tabla 5 del informe según RV-01", y el mismo cambio en el informe.

Decisiones relacionadas, también del autor: H-RV01-07 (quitar la C de Company y decidir el R de Logística) y H-RV01-12 (confirmar el CRUD de Logística en los datos maestros).

**Decisión del autor sobre H-03:** opción A (Settings del Administrador pasa de CRUD a RU propia). Se aplica en un commit propio sobre `matriz-roles.md`
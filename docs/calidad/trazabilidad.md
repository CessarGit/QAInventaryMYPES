# Trazabilidad unificada: requisitos y casos de prueba

Resuelve el choque de identificadores entre la matriz de trazabilidad original (Tabla 14 de PHVA, casos CP-01 a CP-06) y la tabla de casos de prueba (Tabla 8 de pruebas, casos CP-01 a CP-12). Se adoptan los identificadores de la Tabla 8 de pruebas. Subpaso 2.2.

**Regla:** el resultado esperado sale de la matriz de roles ([matriz-roles.md](matriz-roles.md), Tabla 5) y de la descripción del requisito (Tablas 8 de pruebas y 14 de PHVA), nunca del código. Lo que no se puede deducir de esas fuentes figura como **POR DEFINIR**.

## 1. Matriz de requisitos y casos

| Requisito | Casos de prueba | Estado |
| :---- | :---- | :---- |
| RQ-01 Acceso según el rol | CP-04, CP-05, CP-06, CP-07, CP-10, CP-15, CP-16, CP-18 | Pendiente |
| RQ-02 La venta descuenta el stock | CP-01, CP-02, CP-03, CP-19 | Pendiente |
| RQ-03 Reporte de ventas del año | CP-09, CP-12 | Pendiente |
| RQ-04 Alta de producto | CP-08, CP-11, CP-17, CP-20 | Pendiente |
| RQ-05 Gestión de usuarios y grupos | CP-13 | Pendiente |
| RQ-06 Contraseñas protegidas | CP-14 | Pendiente |

Estados: Pendiente (sin ejecutar), Iniciado (en ejecución), Rechazado (el caso falla). Los veinte casos están diseñados y sin ejecutar (meta de la Tabla 21 de PHVA).

## 2. Fichas de los casos

**Precondición común (PC-0):** base de pruebas `stock_test` con un grupo por cada rol de la Tabla 5 (Gerente, Ventas, Bodega, Logística, Administrador), configurado con los permisos de su fila, y un usuario activo por rol. El dump `stock.sql` solo trae dos grupos (Administrator y Owners), por lo que estos datos deben prepararse para las pruebas. Se preparan con `scripts/preparar-stock-test.sh` y `application/tests/_datos/pc0.sql` (subpaso 4.4).

### CP-01 · Vender una cantidad igual al stock

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-02 |
| Módulo | Orders |
| Nivel | Unitaria |
| Técnica | Valor límite |
| Precondiciones | PC-0; un producto con stock N |
| Datos | Cantidad vendida = N. Valor de N: 5 (D-01) |
| Pasos | 1. Registrar una venta del producto por N unidades. 2. Consultar el stock del producto. |
| Resultado esperado | Venta registrada y stock en 0 |
| Evidencia prevista | Salida de PHPUnit; consulta SQL del stock antes y después |
| Nota | Confirma o descarta H-04 (issue #6) |

### CP-02 · Vender una cantidad mayor que el stock

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-02 |
| Módulo | Orders |
| Nivel | Unitaria |
| Técnica | Valor límite |
| Precondiciones | PC-0; un producto con stock N |
| Datos | Cantidad vendida = N + 1. Valor de N: 5, se venden 6 (D-01) |
| Pasos | 1. Intentar registrar una venta del producto por N + 1 unidades. 2. Consultar el stock del producto. |
| Resultado esperado | Rechazo; el stock no cambia |
| Evidencia prevista | Salida de PHPUnit; consulta SQL del stock antes y después |
| Nota | Confirma o descarta H-04 (issue #6) |

### CP-03 · Cantidad igual a 0 o negativa

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-02 |
| Módulo | Orders |
| Nivel | Unitaria |
| Técnica | Valor límite |
| Precondiciones | PC-0; un producto con stock N |
| Datos | Cantidad vendida = 0; cantidad vendida = −1 |
| Pasos | 1. Intentar registrar una venta con cantidad 0. 2. Intentar registrar una venta con cantidad −1. 3. Consultar el stock del producto. |
| Resultado esperado | Rechazo por validación en ambos casos |
| Evidencia prevista | Salida de PHPUnit; consulta SQL del stock |
| Nota | Confirma o descarta H-04 (issue #6) |

### CP-04 · Ventas intenta eliminar una venta

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-01 |
| Módulo | Orders |
| Nivel | Sistema |
| Técnica | Caja negra |
| Precondiciones | PC-0; al menos una venta registrada |
| Datos | Usuario del rol Ventas |
| Pasos | 1. Iniciar sesión como Ventas. 2. Intentar eliminar una venta, desde la interfaz y escribiendo la dirección directamente. |
| Resultado esperado | Acceso denegado (Ventas solo crea y lee). Tabla 5: Ventas tiene CR en Orders |
| Evidencia prevista | Captura y registro |

### CP-05 · Gerente edita una venta

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-01 |
| Módulo | Orders |
| Nivel | Sistema |
| Técnica | Caja negra |
| Precondiciones | PC-0; al menos una venta registrada |
| Datos | Usuario del rol Gerente |
| Pasos | 1. Iniciar sesión como Gerente. 2. Abrir una venta y modificarla. 3. Guardar. |
| Resultado esperado | Permitido (Tabla 5: Gerente tiene CRUD en Orders) |
| Evidencia prevista | Captura y registro |

### CP-06 · Bodega intenta abrir Orders

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-01 |
| Módulo | Orders |
| Nivel | Sistema |
| Técnica | Seguridad |
| Precondiciones | PC-0 |
| Datos | Usuario del rol Bodega |
| Pasos | 1. Iniciar sesión como Bodega. 2. Intentar abrir Orders desde el menú y escribiendo la dirección directamente. |
| Resultado esperado | Acceso denegado (Tabla 5: Bodega sin acceso a Orders) |
| Evidencia prevista | Captura y registro |

### CP-07 · Administrador y Gerente abren Users

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-01 |
| Módulo | Users |
| Nivel | Sistema |
| Técnica | Seguridad |
| Precondiciones | PC-0 |
| Datos | Usuarios de los roles Administrador y Gerente |
| Pasos | 1. Iniciar sesión como Administrador y abrir Users. 2. Iniciar sesión como Gerente e intentar abrir Users desde el menú y escribiendo la dirección directamente. |
| Resultado esperado | Permitido al Administrador; denegado al Gerente (Tabla 5: Users CRUD solo para Administrador) |
| Evidencia prevista | Captura y registro |

### CP-08 · Alta de producto con campos obligatorios vacíos

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-04 |
| Módulo | Products |
| Nivel | Unitaria |
| Técnica | Caja negra |
| Precondiciones | PC-0 |
| Datos | Formulario de alta con los campos obligatorios vacíos. Campos obligatorios: nombre, SKU, precio, cantidad, marca, categoría y tienda (D-02) |
| Pasos | 1. Intentar dar de alta un producto dejando vacíos los campos obligatorios. |
| Resultado esperado | Rechazo con mensaje de validación |
| Evidencia prevista | Salida de PHPUnit; captura del mensaje |

### CP-09 · Una venta se refleja en el reporte anual

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-03 |
| Módulo | Orders y Reports |
| Nivel | Integración |
| Técnica | Integración |
| Precondiciones | PC-0; total del reporte del año antes de la venta conocido |
| Datos | Una venta de importe conocido en el año en curso. Importe que suma el reporte: neto, con cargos e IVA (D-03) |
| Pasos | 1. Anotar el total anual del reporte. 2. Registrar la venta. 3. Consultar de nuevo el reporte anual. |
| Resultado esperado | La venta se refleja en los totales del año |
| Evidencia prevista | Hoja de cálculo comparativa; captura del reporte antes y después |

### CP-10 · Un usuario cambia su contraseña e intenta cambiar la de otro

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-01 |
| Módulo | Settings |
| Nivel | Sistema |
| Técnica | Seguridad |
| Precondiciones | PC-0; dos usuarios de roles sin acceso a Users |
| Datos | Usuario A y usuario B; contraseña nueva para A |
| Pasos | 1. Iniciar sesión como A y cambiar su propia contraseña. 2. Intentar cambiar la contraseña de B. |
| Resultado esperado | Solo se permite la propia (Tabla 5: Settings RU, propia) |
| Evidencia prevista | Captura y registro |
| Nota | Relacionado con A-01 (`hallazgos-topdown.md`) |

### CP-11 · Flujo completo del Gerente

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-04 |
| Módulo | Varios (Brands, Categories, Stores, Products, Orders, Reports) |
| Nivel | Sistema |
| Técnica | Flujo completo |
| Precondiciones | PC-0; al menos una tienda |
| Datos | Usuario del rol Gerente; marca, categoría y producto nuevos |
| Pasos | 1. Iniciar sesión como Gerente. 2. Crear una marca. 3. Crear una categoría. 4. Crear un producto con esa marca, esa categoría y una tienda. 5. Registrar una venta del producto. 6. Consultar el reporte. |
| Resultado esperado | Todo el flujo se completa sin error; el producto creado es visible en el listado (RQ-04) |
| Evidencia prevista | Captura de cada paso |

### CP-12 · Reporte anual frente a un registro manual

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-03 |
| Módulo | Reports |
| Nivel | Aceptación |
| Técnica | Aceptación |
| Precondiciones | PC-0; conjunto de ventas conocido en la base de pruebas |
| Datos | Las mismas ventas registradas a mano en una hoja de cálculo |
| Pasos | 1. Generar el reporte anual. 2. Calcular los totales en la hoja de cálculo. 3. Comparar. |
| Resultado esperado | Los totales coinciden |
| Evidencia prevista | Hoja de cálculo comparativa |

### CP-13 · Crear un usuario, asignarlo a un grupo y verificar sus permisos (nuevo)

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-05 |
| Módulo | Users y Groups |
| Nivel | Sistema |
| Técnica | Caja negra |
| Precondiciones | PC-0 |
| Datos | Usuario del rol Administrador; usuario nuevo asignado al grupo Ventas |
| Pasos | 1. Iniciar sesión como Administrador. 2. Crear un usuario y asignarlo al grupo Ventas. 3. Iniciar sesión con el usuario nuevo. 4. Intentar abrir cada módulo. |
| Resultado esperado | Permisos del grupo aplicados. Según la fila Ventas de la Tabla 5: Settings RU (propia); Products R; Orders CR; Reports R; Profile R; sin acceso a Users, Groups, Brands, Categories, Attributes, Stores ni Company |
| Evidencia prevista | Captura |

### CP-14 · La contraseña se guarda con hash (nuevo)

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-06 |
| Módulo | Users |
| Nivel | Sistema |
| Técnica | Seguridad (inspección de la base) |
| Precondiciones | PC-0; un usuario creado con una contraseña conocida |
| Datos | Contraseña conocida de prueba (no real) |
| Pasos | 1. Crear el usuario con la contraseña conocida. 2. Consultar el campo de contraseña en la base de pruebas. |
| Resultado esperado | Contraseña almacenada con hash, no en texto plano. Algoritmo exigido: hash adaptativo, bcrypt o argon2 con `password_hash` (D-04); MD5 o SHA-1 sin sal es falla |
| Evidencia prevista | Consulta a la base |

### CP-15 · Ventas intenta cambiar su grupo desde Settings (nuevo)

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-01 |
| Módulo | Settings |
| Nivel | Sistema |
| Técnica | Seguridad (petición manipulada) |
| Precondiciones | PC-0; sesión de `ventas_test` |
| Datos | Formulario de Settings con contraseña nueva válida y un campo `groups` añadido a mano con el id del grupo Administrador |
| Pasos | 1. Enviar el formulario manipulado. 2. Consultar el grupo de `ventas_test` en la base de pruebas. |
| Resultado esperado | El grupo de `ventas_test` no cambia (Tabla 5: Settings RU solo de la cuenta propia; Ventas sin acceso a Groups) |
| Evidencia prevista | Consulta de `user_group` antes y después |
| Origen | A-01 (H-RV01-02, H-RV04-01) |

### CP-16 · Acceso sin sesión a Reports y a métodos sin permiso (nuevo)

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-01 |
| Módulo | Reports, Attributes, Orders, Products |
| Nivel | Sistema |
| Técnica | Seguridad (URL directa) |
| Precondiciones | PC-0; sin sesión iniciada |
| Datos | URL de `reports` y de los métodos sin comprobación de permiso del Anexo B de RV-01 (por ejemplo `attributes/createValue`, `orders/fetchOrdersData`, `products/upload_image`) |
| Pasos | 1. Abrir cada URL sin sesión. 2. Registrar la respuesta. |
| Resultado esperado | Redirección al inicio de sesión o acceso denegado, sin devolver datos ni ejecutar la operación (Tabla 5; Tabla 13 de seguridad, control de acceso) |
| Evidencia prevista | Respuesta de cada URL |
| Origen | H-01 (H-RV01-03 a H-RV01-06, H-RV03-01) |

### CP-17 · Nombre de producto con código de script (nuevo)

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-04 |
| Módulo | Products, Orders |
| Nivel | Sistema |
| Técnica | Seguridad (inyección de código, XSS) |
| Precondiciones | PC-0; sesión de `gerente_test` |
| Datos | Nombre de producto `<script>alert(1)</script>` |
| Pasos | 1. Crear el producto. 2. Abrir el listado de productos. 3. Crear una venta con ese producto y abrir la venta y la factura. |
| Resultado esperado | El texto se muestra literal y no se ejecuta en ninguna pantalla (Tabla 13 de seguridad, validación de entradas; L1, pregunta 5) |
| Evidencia prevista | Captura de cada pantalla |
| Origen | H-RV03-04 |

### CP-18 · Formulario enviado sin token CSRF (nuevo)

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-01 |
| Módulo | Brands (alta) |
| Nivel | Sistema |
| Técnica | Seguridad (falsificación de petición, CSRF) |
| Precondiciones | PC-0; sesión de `gerente_test` abierta en el navegador |
| Datos | Página HTML local con un formulario de alta de marca que apunta al staging, sin token |
| Pasos | 1. Abrir la página local y enviar el formulario. 2. Revisar si la marca se creó. |
| Resultado esperado | La petición se rechaza y la marca no se crea (Tabla 13 de seguridad: activar la protección CSRF de CodeIgniter) |
| Evidencia prevista | Respuesta y listado de marcas |
| Origen | H-RV04-02, H-RV04-04 |

### CP-19 · Vender una unidad (valor límite) (nuevo)

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-02 |
| Módulo | Orders |
| Nivel | Unitaria |
| Técnica | Valores límite |
| Precondiciones | PC-0; producto con stock N = 5 (D-01); sesión de `ventas_test` |
| Datos | Cantidad vendida = 1 |
| Pasos | 1. Registrar la venta. 2. Consultar el stock del producto. |
| Resultado esperado | Venta registrada y stock 4 (N − 1) (Tabla 7 de pruebas, valores límite) |
| Evidencia prevista | Stock antes y después |

### CP-20 · Nombre de producto más largo que el límite (valor límite) (nuevo)

| Campo | Contenido |
| :---- | :---- |
| Requisito | RQ-04 |
| Módulo | Products |
| Nivel | Unitaria |
| Técnica | Valores límite |
| Precondiciones | PC-0; sesión de `gerente_test` |
| Datos | Nombre de 256 caracteres; límite de 255 (D-05) |
| Pasos | 1. Dar de alta el producto con los demás campos obligatorios válidos (D-02). 2. Revisar el resultado y la base. |
| Resultado esperado | Rechazo con mensaje de validación, o guardado truncado a 255 caracteres sin error de la base (D-05) |
| Evidencia prevista | Mensaje o consulta a la base |

## 3. Cambios respecto al informe

1. **Renumeración.** La Tabla 14 de PHVA usaba CP-01 a CP-06 con un significado distinto al de la Tabla 8 de pruebas. Se adoptan los identificadores de la Tabla 8. Correspondencia:

   | Caso de la Tabla 14 | Pasa a |
   | :---- | :---- |
   | CP-01 Cada rol accede solo a sus módulos | CP-04, CP-06, CP-07 |
   | CP-02 Registrar una venta | CP-01, CP-02, CP-03 |
   | CP-03 Generar el reporte con datos conocidos | CP-09, CP-12 |
   | CP-04 Crear producto con marca, categoría y tienda | CP-08, CP-11 |
   | CP-05 Crear usuario y asignarlo a un grupo | CP-13 (nuevo) |
   | CP-06 Revisar el almacenamiento | CP-14 (nuevo) |

2. **Casos nuevos.** CP-13 y CP-14 se crean porque RQ-05 y RQ-06 no tenían caso en la Tabla 8 de pruebas. Su nivel y técnica son una propuesta aprobada por el autor; el informe no los fija.
3. **Asociaciones.** CP-05 y CP-10 quedan asociados a RQ-01.
4. **Precondición común.** Se añade PC-0 (grupos de los cinco roles), porque los datos semilla no los contienen.
5. **Casos CP-15 a CP-20 (subpaso 5.1).** Seis casos nuevos para llegar a los 20 de la Tabla 21 de PHVA: cuatro de seguridad que confirman hallazgos de las revisiones (A-01, H-01, XSS y CSRF) y dos de valores límite (Tabla 7 de pruebas). Sus resultados esperados salen de la Tabla 5, la Tabla 13 de seguridad y las decisiones D-01, D-02 y D-05.

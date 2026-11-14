# RV-04 · Users, Groups, autenticación y permisos

## 1. Datos de la revisión

| Campo | Contenido |
|---|---|
| Producto y versión | RV-04: `application/controllers/Users.php` (330 líneas), `Groups.php` (155), `Auth.php` (72), `Dashboard.php` (36), `application/core/MY_Controller.php` (236), `application/models/Model_users.php` (86), `Model_groups.php` (60) y `application/config/config.php` (524; revisadas sesión, cookies, CSRF y XSS); commit `ad63b11`. Consultados además para las preguntas 4 y 5: `application/models/Model_auth.php`, `application/views/users/setting.php`, `application/views/users/profile.php` y `application/views/groups/index.php` |
| Riesgo y técnica planificada | Alto, inspección (Tabla 7 de revisiones) |
| Técnica aplicada | Verificación de escritorio (autor) + segunda pasada (Claude Code como apoyo, no como revisor humano independiente) |
| Lista usada | L1 |
| Participantes y roles | Productor: autor original del código (proyecto de terceros). Revisor y registrador: César Roque. Apoyo: Claude Code |
| Fechas de las pasadas | Autor (César Roque): 03/11/2026 · Claude: 14/11/2026 |
| Desviación | La Tabla 7 pide inspección formal con tres personas; se trabaja solo. Se compensa con la lista y la doble pasada |

## 2. Respuestas a la lista

| N.º | Pregunta | Respuesta (Sí / No / N/A) | Evidencia (`archivo:línea`) | Hallazgo |
|---|---|---|---|---|
| 1 | ¿El controlador verifica la sesión y el permiso del rol antes de ejecutar la acción? | No (parcial) | Sesión en el constructor (`application/controllers/Users.php:9`, `application/controllers/Groups.php:9`) y permiso en cada acción (`Users.php:21`, `:43`, `:98`, `:193`, `:219`, `:236`; `Groups.php:24`, `:42`, `:81`, `:124`). Excepciones: `Users::password_hash` es público y sin puerta (`Users.php:88`); el Dashboard decide el administrador por `user_id == 1`, no por permiso (`application/controllers/Dashboard.php:32`) | H-RV04-08, H-RV04-09 |
| 2 | ¿Las entradas se validan en el servidor (tipo, rango, campos obligatorios)? | No | Settings acepta el campo `groups`, que su formulario no envía (`Users.php:288`); la edición no exige correo único (`Users.php:105`, frente a `:49` en el alta); el grupo solo es obligatorio, sin comprobar que exista (`:47`, `:103`); los permisos de grupo se guardan tal como llegan (`Groups.php:50`, `:91`) | H-RV04-01, H-RV04-11, H-RV04-12 |
| 3 | ¿Las consultas usan Query Builder o parámetros, sin concatenar texto del usuario? | Sí | Parámetros enlazados en `application/models/Model_users.php:13-14`, `:18-19`, `:26-27`, `:31-32`; `Model_groups.php:13-14`, `:45-46`, `:52-55`; `Model_auth.php:16-17`, `:30-31` | — |
| 4 | ¿Las contraseñas se guardan con hash y nunca se muestran? | No | Generación con `password_hash($pass, PASSWORD_DEFAULT)` (bcrypt; `Users.php:91`) al crear (`:56`), al editar (`:137`) y en Settings (`:276`); verificación con `password_verify` (`application/models/Model_auth.php:36`). Pero la contraseña nueva se escribe en un campo `type="text"` y queda visible (`application/views/users/setting.php:98`). `Users::password_hash` es público y accesible por URL (`/users/password_hash/<texto>`) para cualquier usuario con sesión; devuelve el valor sin imprimirlo, por lo que hoy no expone nada | H-RV04-07, H-RV04-08 |
| 5 | ¿Las salidas hacia la vista se escapan para evitar inyección de código? | No | Usuario, correo, nombre, apellido y teléfono sin escapar (`application/views/users/profile.php:32-52`; atributos `value` en `application/views/users/setting.php:48-68`); nombre del grupo sin escapar (`profile.php:56`, `application/views/groups/index.php:59`); `global_xss_filtering = FALSE` (`application/config/config.php:436`). Esos campos los escribe cualquier usuario desde Settings | H-RV04-14 |
| 6 | ¿El registro de la venta y el descuento de stock van en una sola transacción, con la comprobación de stock dentro de ella? | N/A | Pregunta específica de ventas. Observación: el alta de usuario inserta usuario y grupo sin transacción (`application/models/Model_users.php:42-51`) | H-RV04-16 |
| 7 | ¿Los errores se manejan sin mostrar detalles técnicos al usuario? | No | Errores visibles en el entorno de desarrollo (H-RV03-09). Un usuario sin grupo produce avisos porque `unserialize` recibe `null` (`application/core/MY_Controller.php:27-30`) | H-RV04-10 |
| 8 | ¿Se respetan los nombres, la estructura y los comentarios acordados por el equipo? | No | `action="<?php base_url(...) ?>"` sin `echo` en el formulario de Settings (`application/views/users/setting.php:41`); mensaje "Successfully created" al editar (`Users.php:123`); cierre de sesión por GET (`application/controllers/Auth.php:66-70`) | H-RV04-17 |
| 9 | ¿Hay lógica duplicada que debería estar en un solo lugar? | Sí | `edit` y `setting` duplican la lógica de actualización (`Users.php:96-189` y `:234-328`) | H-RV04-18 |
| 10 | ¿El cambio tiene al menos un caso de prueba, enlazado a un requisito? | Sí | CP-07, CP-10, CP-13 y CP-14 existen; CP-15 y CP-18 se agregan en el paso 5.1 | — |

Cada "No" es un hallazgo y va a la sección 3.

## 3. Lista de problemas (Tabla 16)

| ID | Ubicación | Descripción objetiva | Tipo | Gravedad | Encontrado por | Responsable | Estado |
|---|---|---|---|---|---|---|---|
| H-RV04-01 | `application/controllers/Users.php:288`; `application/models/Model_users.php:62-66` | Settings acepta `groups` del POST y cambia el grupo del propio usuario, también al grupo 1, oculto en las listas (A-01). Crítico si se confirma | Defecto de diseño | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV04-02 | `application/controllers/Users.php:250-288`; `application/config/config.php:452` | Settings cambia la contraseña sin pedir la actual y no hay protección CSRF: una página ajena podría cambiar la contraseña de un usuario con sesión abierta | Defecto de diseño | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV04-03 | `application/controllers/Auth.php:43`; `application/config/config.php:387` | El identificador de sesión no se regenera al iniciar sesión (fijación de sesión) | Defecto de diseño | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV04-04 | `application/config/config.php:407`, `:408`, `:452` | Cookie de sesión sin HttpOnly ni Secure; CSRF desactivado | Defecto de diseño | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV04-05 | `application/controllers/Auth.php:47`, `:52` | Mensajes distintos para correo inexistente y contraseña incorrecta: permiten averiguar qué correos están registrados | Defecto de diseño | Menor | Ambos | César Roque | Abierto |
| H-RV04-06 | `application/controllers/Auth.php:27-55` | Sin límite de intentos de inicio de sesión ni registro de intentos fallidos (Tabla 13 de seguridad, registro de eventos) | Omisión | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV04-07 | `application/views/users/setting.php:98` | La contraseña nueva se escribe en un campo `type="text"` y queda visible en pantalla | Defecto de diseño | Mayor (datos) | Ambos | César Roque | Abierto |
| H-RV04-08 | `application/controllers/Users.php:88` | `password_hash` es un método público accesible por URL; no imprime nada, por lo que hoy no expone datos | Forma | Menor | Ambos | César Roque | Abierto |
| H-RV04-09 | `application/controllers/Dashboard.php:32`; `application/models/Model_users.php:18-19`; `application/models/Model_groups.php:18-19` | Superadministrador fijo (usuario 1 y grupo 1), oculto en las listas y fuera de la matriz (A-04) | Defecto de diseño | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV04-10 | `application/models/Model_groups.php:47`; `application/controllers/Groups.php:131-137` | `existInUserGroup` solo devuelve verdadero si el grupo tiene exactamente un usuario: un grupo con dos o más usuarios puede eliminarse y esos usuarios quedan sin permisos | Defecto de diseño | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV04-11 | `application/controllers/Groups.php:50`, `:91`; `application/core/MY_Controller.php:29-30` | Los permisos de grupo se guardan sin lista blanca: cualquier texto o ninguno (`null`) | Omisión | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV04-12 | `application/controllers/Users.php:105`; `application/models/Model_auth.php:19` | La edición no exige correo único; si dos cuentas comparten correo, ninguna puede iniciar sesión, porque la comprobación exige exactamente una fila | Omisión | Mayor (permisos, datos) | Ambos | César Roque | Abierto |
| H-RV04-13 | `application/models/Model_users.php:73-78`; `application/controllers/Users.php:191-199` | Eliminar un usuario deja su fila en `user_group`, y el Administrador puede eliminarse a sí mismo | Defecto de diseño | Mayor (datos) | Ambos | César Roque | Abierto |
| H-RV04-14 | `application/views/users/profile.php:32-56`; `application/views/users/setting.php:48-68`; `application/views/groups/index.php:59`; `application/config/config.php:436` | Datos de usuario y nombres de grupo sin escapar (inyección de código almacenada desde Settings) | Defecto de diseño | Mayor (datos) | Ambos | César Roque | Abierto |
| H-RV04-15 | `application/controllers/Users.php:50`, `:132`, `:271` | La política de contraseña es solo una longitud mínima de 8, sin requisito que la respalde (H-RV01-13) | Omisión | Menor | Ambos | César Roque | Abierto |
| H-RV04-16 | `application/models/Model_users.php:42-51` | El alta de usuario no usa transacción: puede quedar un usuario sin grupo | Defecto de diseño | Mayor (permisos) | Ambos | César Roque | Abierto |
| H-RV04-17 | `application/views/users/setting.php:41`; `application/controllers/Users.php:123`; `application/controllers/Auth.php:66-70` | `action` sin `echo`, mensaje equivocado y cierre de sesión por GET | Forma | Menor | Ambos | César Roque | Abierto |
| H-RV04-18 | `application/controllers/Users.php:96-189` y `:234-328` | Lógica de actualización duplicada entre `edit` y `setting` | Defecto de diseño | Menor | Ambos | César Roque | Abierto |

Criterio de gravedad (Tabla 17): **mayor** si corregirlo toma más de 1 hora-persona, o si afecta permisos, dinero, existencias o datos (aunque sea fácil de corregir). **Menor** en cualquier otro caso.

*Nota.* El autor declara que sus hallazgos coinciden "en su mayoría" con los de la segunda pasada, sin listar diferencias; por eso todos figuran como "Ambos". Si alguno no lo encontró el autor, se cambia a "Claude" y se recalculan las métricas.

## 4. Métricas

| Métrica | Valor |
|---|---|
| Ep: preparación del autor (horas) | 0,3 |
| Ea: revisión del autor (horas) | 0,4 |
| Er: corrección (horas, si aplica) | N/A: todavía no hay correcciones; se mide en el paso 5.6 |
| WPS: tamaño revisado (líneas, con `wc -l`) | 1499 (alcance del manual; las vistas consultadas no se cuentan) |
| Hallazgos mayores / menores | 13 / 5 |
| Densidad de error (hallazgos / WPS × 1000) | 18 / 1499 × 1000 = 12,0 |
| Hallazgos encontrados solo por el autor | 0 |
| Hallazgos encontrados solo por Claude | 0 |
| Hallazgos encontrados por ambos | 18 |

## 5. Informe resumen y decisión (Tabla 18)

| Campo | Contenido |
|---|---|
| Decisión | Aceptar con correcciones |
| Justificación | Hay 13 hallazgos mayores que comprometen la seguridad y la integridad del sistema (permisos, credenciales y datos), pero ninguno obliga a rediseñar el producto ni impide verificarlo, que es el criterio de rechazo de la Tabla 18. Cada uno tiene una corrección acotada o un caso de prueba que lo confirma, y el líder puede verificar la corrección sin otra reunión: (1) H-RV04-01 (A-01) se confirma con CP-15 y CP-10 y, si resulta crítico, se corrige en el paso 5.6 haciendo que Settings ignore el campo `groups`; (2) H-RV04-02 y H-RV04-04 se confirman con CP-18 y se corrigen en 5.6 activando `csrf_protection`, con repetición de todos los casos; (3) H-RV04-03, H-RV04-07 y la cookie de sesión de H-RV04-04 se verifican en el guion 5.4. Los mayores que exigirían funcionalidad nueva o una reestructuración (límite de intentos y registro de eventos en H-RV04-06, lista blanca de permisos en H-RV04-11, centralización de la autorización de H-01) quedan fuera del alcance de la iteración (regla 1) y se registran como issues con su plan (Tabla 9 de pruebas). Solo se corrigen en esta iteración los defectos que el paso 5 confirme como críticos o altos (regla 13). El cierre de hallazgos mayores (Tabla 19) queda abierto hasta verificar las correcciones en el paso 5 |
| Hallazgos que pasan a pruebas | H-RV04-01 → CP-15, CP-10. H-RV04-02 y H-RV04-04 → CP-18. H-RV04-09 → CP-07, CP-13 (los usuarios de PC-0 no pueden tener id 1). H-RV04-03, H-RV04-04 y H-RV04-07 → verificaciones del guion 5.4 |
| Hallazgos sin caso de prueba | H-RV04-02 (toma de la cuenta), H-RV04-05, H-RV04-06, H-RV04-10, H-RV04-11, H-RV04-12, H-RV04-13, H-RV04-14, H-RV04-16 |
| Issues creados | Existentes: #8 (A-04: cubre H-RV04-09), #4 (H-01). Nuevos (paso 3.4): #15 (H-RV04-06), #18 (H-RV04-14), #20 (H-RV04-10, H-RV04-11, H-RV04-12, H-RV04-13, H-RV04-16). A-01 (H-RV04-01) y H-RV04-02, H-RV04-03, H-RV04-04 y H-RV04-07 tendrán su issue con la plantilla Defecto si se confirman en el paso 5 |

**Firma del revisor:** César Roque · fecha: 14/11/2026

---

## Anexo A. Análisis específicos (PROMPTS-FASE2.md, 3.3)

### A-01: camino del campo `groups`

1. El formulario de Settings no contiene el campo `groups`: solo usuario, correo, nombre, apellido, teléfono, género y contraseña (`application/views/users/setting.php:41-104`).
2. `Users::setting` comprueba `updateSetting` (`application/controllers/Users.php:236`) y toma el `id` de la sesión (`:240`).
3. Reglas de validación: usuario de 5 a 12 caracteres, correo y nombre obligatorios (`:243-245`).
4. Si la contraseña está vacía, actualiza sin grupo (`:250`, `:260`). Si viene contraseña, exige 8 caracteres o más y que coincida (`:271-272`), la convierte en hash (`:276`) y llama a `model_users->edit($data, $id, $this->input->post('groups'))` (`:288`).
5. `Model_users::edit` ejecuta `UPDATE user_group SET group_id = <groups> WHERE user_id = <id de la sesión>` (`application/models/Model_users.php:62-66`).
6. En la petición siguiente, `MY_Controller` carga los permisos del grupo nuevo (`application/core/MY_Controller.php:25-30`), sin necesidad de volver a iniciar sesión.

**Qué tendría que enviar un usuario:** un POST a `/users/setting` con `username` (5 a 12 caracteres), `email`, `fname`, `password` y `cpassword` iguales (8 caracteres o más) y `groups=<id del grupo destino>`. El grupo 1 (Administrator) está oculto en las listas (`application/models/Model_groups.php:18-19`), pero se acepta por esta vía. No se ejecutó.

### A-04

`application/controllers/Dashboard.php:32` decide el administrador con `$user_id == 1`. Lo refuerzan el usuario 1 oculto en el listado de usuarios (`application/models/Model_users.php:18-19`) y el grupo 1 oculto en el de grupos (`application/models/Model_groups.php:18-19`).

### Configuración de sesión (`application/config/config.php`)

| Parámetro | Valor | Línea |
|---|---|---|
| `sess_expiration` | 7200 (2 h) | 383 |
| `sess_match_ip` | FALSE | 385 |
| `sess_time_to_update` | 300 | 386 |
| `sess_regenerate_destroy` | FALSE | 387 |
| `cookie_secure` | FALSE | 407 |
| `cookie_httponly` | FALSE | 408 |
| `global_xss_filtering` | FALSE | 436 |
| `csrf_protection` | FALSE | 452 |

Además, el inicio de sesión no regenera el identificador de sesión (`application/controllers/Auth.php:43`).

### Reports sin sesión (seguimiento del código, sin ejecutar)

1. `Reports` hereda de `Admin_Controller`; sin sesión, el constructor solo marca `logged_in = FALSE` y `$this->permission` queda como el arreglo vacío declarado (`application/core/MY_Controller.php:13`, `:20-22`).
2. `Reports` no llama a `not_logged_in()` (`application/controllers/Reports.php:7-12`).
3. En `index`, `in_array('viewReports', array())` es falso y redirige a `dashboard` (`application/controllers/Reports.php:20`).
4. El constructor del Dashboard llama a `not_logged_in()` y redirige a `auth/login` (`application/controllers/Dashboard.php:9`).

Resultado: el acceso se deniega de forma indirecta, con dos redirecciones. Registrado como H-RV01-06.

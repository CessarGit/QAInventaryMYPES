# Fuente de diseño: tablas del informe de calidad

Extracto literal del informe *Gestión de la calidad de un sistema web de inventarios y punto de venta para MYPES*. Es la referencia de diseño de la iteración: lo que aquí se dice se ejecuta, no se reinterpreta. Si el código contradice estas tablas, se registra como hallazgo.

## Componentes tecnológicos (Tabla 2)
| Componente | Versión |
| :---- | :---- |
| CodeIgniter | 3.1.6 |
| PHP | 7.4.33 |
| MySQL | 5.7.x |
| Servidor web | Apache 2.4 |
| Acceso a datos | Query Builder (antes Active Record) |

*Nota.* CodeIgniter 3.1.13 es la última versión de la rama 3.x e incluye correcciones de seguridad; PHP 7.4 no tiene soporte oficial y se mantiene por compatibilidad con CodeIgniter 3.

## Matriz de roles y permisos (Tabla 5, única fuente de verdad)
| Módulo | Gerente | Ventas | Bodega | Logística | Administrador |
| :---- | :---- | :---- | :---- | :---- | :---- |
| Users | — | — | — | — | CRUD |
| Groups | — | — | — | — | CRUD |
| Settings | RU (propia) | RU (propia) | RU (propia) | RU (propia) | CRUD |
| Brands | CRUD | — | CRUD | CRUD | — |
| Categories | CRUD | — | CRUD | CRUD | — |
| Attributes | CRUD | — | CRUD | CRUD | — |
| Stores | CRUD | — | R | R | — |
| Products | CRUD | R | CRUD | CRUD | — |
| Orders | CRUD | CR | — | — | — |
| Reports | R | R | R | R | — |
| Company | CRU | — | — | R | — |
| Profile | R | R | R | R | R |

*Nota.* C = crear; R = leer; U = actualizar; D = eliminar; — = sin acceso; RU (propia) = el usuario lee y actualiza solo su propia cuenta.

## Línea base (Tabla 7)
| Indicador | Valor inicial |
| :---- | :---- |
| Pruebas manuales o automáticas ejecutadas | 0 |
| Repositorio central en GitHub | No existía; desarrollo aislado |
| Registro de tareas | No existía |
| Casos de prueba documentados | 0 |
| Pruebas unitarias (PHPUnit) | 0 |
| Versión de CodeIgniter respecto a la última 3.1.x | 3.1.6 frente a 3.1.13 |

## Objetivos de calidad (Tabla 13 de PHVA)
| Objetivo | Métrica | Umbral propuesto |
| :---- | :---- | :---- |
| Los requisitos están cubiertos por pruebas | Requisitos con al menos un caso de prueba | 100 % de los requisitos prioritarios |
| La lógica crítica está probada | Cobertura de pruebas unitarias en los módulos Orders y Products | Al menos 60 % al cierre del proyecto |
| Sin defectos graves abiertos | Defectos críticos abiertos al cerrar una iteración | 0 |
| Los cambios se revisan | Cambios integrados con revisión por pares | 100 % |
| Los casos de prueba se ejecutan | Casos ejecutados frente a diseñados | Al menos 90 % |

*Nota.* Los umbrales son propuestas que el equipo considera. 

## Matriz de trazabilidad original (Tabla 14 de PHVA) — tiene IDs que chocan con la Tabla 8 de pruebas
| Requisito | Caso de prueba | Resultado esperado | Evidencia | Estado |
| :---- | :---- | :---- | :---- | :---- |
| RQ-01 Acceso según el rol | CP-01 Cada rol accede solo a sus módulos | Acceso denegado fuera del rol | Captura y registro | Pendiente |
| RQ-02 La venta descuenta el stock | CP-02 Registrar una venta | El stock baja en la cantidad vendida | Captura y consulta SQL | Pendiente |
| RQ-03 Reporte de ventas del año | CP-03 Generar el reporte con datos conocidos | Totales iguales al cálculo manual | Hoja de cálculo comparativa | Pendiente |
| RQ-04 Alta de producto | CP-04 Crear producto con marca, categoría y tienda | Producto visible en el listado | Captura | Pendiente |
| RQ-05 Gestión de usuarios y grupos | CP-05 Crear usuario y asignarlo a un grupo | Permisos del grupo aplicados | Captura | Pendiente |
| RQ-06 Contraseñas protegidas | CP-06 Revisar el almacenamiento | Contraseña almacenada con hash, no en texto plano | Consulta a la base | Pendiente |

*Nota.* Los casos CP-01 a CP-06 son los casos iniciales; el equipo los amplía en cada iteración.

## Semáforo de integración continua (Tabla 18 de PHVA)
| Color | Condición propuesta | Acción |
| :---- | :---- | :---- |
| Verde | Las pruebas pasan y la cobertura no baja del umbral | El cambio se integra |
| Ámbar | Las pruebas pasan, pero hay advertencias del análisis estático o la cobertura baja | Se integra solo si lo aprueba el responsable del módulo, con el riesgo registrado |
| Rojo | Una prueba falla o hay un defecto crítico | Se bloquea la integración hasta corregirlo |

*Nota.* Los umbrales son propuestas.

## Métricas por nivel (Tabla 19 de PHVA)
| Nivel | Métrica | Objetivo | Umbral propuesto |
| :---- | :---- | :---- | :---- |
| Proyecto | Tareas terminadas frente a planificadas por iteración | Cumplir el plan | Al menos 80 % |
| Proyecto | Casos de prueba ejecutados | Verificar lo planificado | Al menos 90 % |
| Proceso | Cambios integrados con revisión | Aplicar el proceso | 100 % |
| Proceso | Tiempo para corregir un defecto crítico | Responder rápido | Máximo una iteración |
| Producto | Defectos críticos abiertos | Entregar sin fallos graves | 0 |
| Producto | Cobertura de pruebas unitarias | Proteger la lógica crítica | Al menos 60 % |
| Producto | Requisitos prioritarios con prueba | Trazabilidad | 100 % |


## Mejoras SMART (Tabla 21 de PHVA)
| Paso | Mejora 1: pruebas | Mejora 2: versión del framework |
| :---- | :---- | :---- |
| Diagnóstico | No hay casos ni pruebas unitarias (L-02) | CodeIgniter 3.1.6 frente a 3.1.13 (R-02) y PHP 7.4 sin soporte |
| Objetivo SMART | Diseñar y ejecutar al menos 20 casos de prueba y llegar a 60 % de cobertura en Orders y Products antes del cierre del proyecto | Actualizar a 3.1.13 en staging y repetir las pruebas antes del cierre, sin regresiones |
| Acciones | Escribir los casos de la matriz; instalar PHPUnit; ejecutar en cada cambio | Actualizar en una rama; ejecutar los casos; registrar diferencias (AC-03) |
| Métrica de verificación | Casos ejecutados; cobertura | Casos que pasan después de actualizar |
| Estandarización | Todo cambio nuevo exige su prueba | Revisar versiones de dependencias en cada cierre de iteración |
| Responsable | Oscar Nájera, con revisión de César Roque | Oscar Nájera, con revisión de César Roque |

*Nota.* Plazos y responsables son propuestas; la actualización se haría primero en staging.

## Hallazgos de cohesión y acoplamiento (Tabla 8 de Top-Down)
| ID | Elemento | Hallazgo | Principio | Recomendación |
| :---- | :---- | :---- | :---- | :---- |
| H-01 | Control de permisos | Cada controlador valida sesión y permisos por separado; es una dependencia común a los 12 módulos | Acoplamiento | Centralizar la validación en un punto único (controlador base o *hook*) |
| H-02 | Settings y Profile | Ambos tratan el perfil propio: uno lo muestra y el otro lo edita | Cohesión | Revisar si deben unirse bajo un mismo módulo de cuenta propia |
| H-03 | Settings (Administrador) | La matriz da al Administrador CRUD en Settings además de la cuenta propia, lo que mezcla cuenta personal y configuración global | Cohesión | Separar la configuración global de la cuenta personal |
| H-04 | Orders y Products | La venta descuenta stock del producto; es una dependencia legítima, pero cruza dos módulos | Acoplamiento | Hacerla mediante una interfaz del modelo de Products, dentro de una transacción |
| H-05 | Clientes | El proyecto menciona un catálogo de clientes, pero ningún módulo lo contiene | Brecha de descomposición | Aclarar si queda fuera del alcance o si falta un módulo (L-03: no se desarrolla) |
| H-06 | Company | Perfil de la empresa separado de los demás datos de configuración | Cohesión | Mantener separado; la responsabilidad es única y clara |

*Nota.* Cohesión: una sola responsabilidad por módulo. Acoplamiento: grado de dependencia entre módulos.

## Casos de prueba (Tabla 8 de pruebas)
| ID | Módulo | Nivel y técnica | Entrada o escenario | Resultado esperado |
| :---- | :---- | :---- | :---- | :---- |
| CP-01 | Orders | Unitaria, valor límite | Vender una cantidad igual al stock | Venta registrada y stock en 0 |
| CP-02 | Orders | Unitaria, valor límite | Vender una cantidad mayor que el stock | Rechazo; el stock no cambia |
| CP-03 | Orders | Unitaria, valor límite | Cantidad igual a 0 o negativa | Rechazo por validación |
| CP-04 | Orders | Sistema, caja negra | Rol Ventas intenta eliminar una venta | Acceso denegado (Ventas solo crea y lee) |
| CP-05 | Orders | Sistema, caja negra | Gerente edita una venta | Permitido |
| CP-06 | Orders | Sistema, seguridad | Rol Bodega intenta abrir Orders | Acceso denegado |
| CP-07 | Users | Sistema, seguridad | Administrador y Gerente abren Users | Permitido al Administrador; denegado al Gerente |
| CP-08 | Products | Unitaria, caja negra | Alta de producto con campos obligatorios vacíos | Rechazo con mensaje de validación |
| CP-09 | Orders y Reports | Integración | Registrar una venta y consultar el reporte anual | La venta se refleja en los totales del año |
| CP-10 | Settings | Sistema, seguridad | Un usuario cambia su contraseña; intenta cambiar la de otro | Solo se permite la propia |
| CP-11 | Varios | Sistema, flujo completo | Gerente crea marca, categoría y producto, vende y consulta el reporte | Todo el flujo se completa sin error |
| CP-12 | Reports | Aceptación | Comparar el reporte anual con un registro manual equivalente | Los totales coinciden |

*Nota.* Elaboración a partir de la matriz de roles y del flujo principal del proyecto. Estado de los doce casos: diseñados, sin ejecutar.

## Plan de pruebas (Tabla 9 de pruebas)
| Elemento | Contenido propuesto |
| :---- | :---- |
| Estrategia | Pruebas por niveles, de lo unitario a lo aceptación; priorización por riesgo (ventas, stock y control de acceso primero) |
| Recursos | Entorno de staging en Docker con MySQL 5.7, datos semilla, PHPUnit y el repositorio de GitHub |
| Cronograma | Unitarias e integración durante el desarrollo de cada cambio; sistema y aceptación antes de cada liberación; fechas por definir por el equipo |
| Criterios de éxito | Todos los casos críticos aprobados; ningún defecto crítico o alto abierto; los defectos menores quedan registrados con un plan |
| Criterios de fallo | Cualquier caso crítico fallido o defecto crítico abierto detiene la liberación |
| Roles | Oscar Nájera: unitarias e integración; César Roque: sistema, coordinación y firma SQA; Miguel Ángel: usabilidad y aceptación |
| Registro | Casos, resultados e incidentes documentados con la plantilla del Apéndice A y vinculados a su issue |

*Nota.* Las fechas y los umbrales deben ser confirmados por el equipo.

## Lista de verificación de auditoría (Tabla 17)
| N.° | Punto de control | Evidencia esperada |
| :---- | :---- | :---- |
| 1 | Los permisos de la tabla de grupos coinciden con la matriz de roles | Configuración y CP-04 a CP-07 |
| 2 | Las contraseñas se almacenan con un hash, no en texto plano | Revisión del código y de la base de datos de pruebas |
| 3 | El acceso directo a la base de datos de producción está restringido | Lista de accesos |
| 4 | Los cambios llegan mediante pull request con revisión por pares y firma SQA | Historial de pull requests |
| 5 | Los mensajes de commit siguen la convención definida | Historial de commits |
| 6 | Existen casos de prueba documentados con sus resultados | Casos y registros del Apéndice A |
| 7 | Los defectos tienen un registro con severidad, responsable y cierre | Issues de GitHub |
| 8 | Existe un changelog actualizado por versión | Archivo de cambios |
| 9 | Hay respaldos de la base de datos y se probó su restauración | Registro de respaldo y de restauración de prueba |
| 10 | Las versiones de CodeIgniter y de PHP están soportadas o hay un plan de actualización | Versiones y plan de actualización |
| 11 | Las tablas usan InnoDB con llaves foráneas | Esquema de la base de datos |
| 12 | El stock coincide con las ventas registradas | CP-01, CP-09 y CP-12 |

*Nota.* Elaboración propia. Los puntos 2 y 3 se inspiran en los hallazgos del ejemplo de auditoría del material.

## Controles de seguridad lógica (Tabla 13 de seguridad)
| Control | Aplicación al sistema | Estado |
| :---- | :---- | :---- |
| Control de acceso por roles | Los controladores validan sesión y permisos contra la matriz; cada rol solo accede a lo que la matriz permite. Probar con un usuario por rol que ningún módulo ajeno sea accesible, tampoco escribiendo la dirección directamente | Existe en el diseño; verificar |
| Contraseñas | Almacenarlas con un hash adaptativo (por ejemplo, password_hash de PHP) y no en texto claro; definir una política de contraseña segura (ver Apéndice A) | Por verificar |
| Autenticación multifactor | Para el Administrador y el Gerente, que tienen los permisos de mayor alcance | Propuesto |
| HTTPS con certificado válido | Cifrar todo el tráfico; redirigir HTTP a HTTPS; comprobar quién emite el certificado y su vencimiento | Propuesto |
| Sesiones | Cierre de sesión por inactividad; cookie de sesión con atributos de seguridad | Por verificar |
| Validación de entradas y consultas | Validar datos de formularios; usar Query Builder con valores enlazados; activar la protección CSRF de CodeIgniter | Por verificar |
| Firewall | Permitir solo los puertos necesarios (el del sitio web y el de administración restringido); la base de datos no se expone a Internet | Propuesto |
| Acceso remoto | El Gerente accede por VPN si el sistema está en la red local | Propuesto |
| Registro de eventos | Registrar inicios de sesión, intentos fallidos y operaciones críticas (altas, ediciones y borrados) | Propuesto |
| Secretos en el repositorio | No guardar contraseñas ni claves en el código; usar archivos de configuración fuera del repositorio | Por verificar |
| Contenedores de staging | Usar contraseñas distintas a las de producción y no publicar puertos innecesarios | Por verificar |

*Nota.* Los controles marcados "Por verificar" pueden ya existir; el informe no revisó el código (L-02).

## Metas de recuperación (Tabla 9 de recuperación)
| Sistema o función | Nivel | Criticidad | RTO | RPO | Razón |
| :---- | :---- | :---- | :---- | :---- | :---- |
| Base de datos y control de acceso (Users, Groups) | 1 | Crítica | 4 h | 1 h | Sin ellos nadie puede entrar ni operar |
| Registro de ventas (Orders) | 1 | Crítica | 4 h | 1 h | Cada hora de ventas perdida se debe reconstruir a mano |
| Control de existencias (Products y catálogos) | 2 | Alta | 8 h | 1 h | El stock depende de las ventas; un error se arrastra |
| Reportes de ventas (Reports) | 3 | Media | 24 h | 24 h | Se pueden recalcular desde las ventas |
| Datos de empresa, perfil y configuración | 4 | Baja | 72 h | 24 h | Cambian poco y se pueden reintroducir |
| Staging y repositorio | 4 | Baja | 72 h | 24 h | El código ya tiene copia fuera del sitio en GitHub |

*Nota.* Las metas son una propuesta del equipo. Durante una caída de ventas, la MYPE opera con registro manual y se captura después.

## Política de respaldos (Tabla 11 de recuperación)
| Qué | Frecuencia | Medio y ubicación | Retención |
| :---- | :---- | :---- | :---- |
| Base de datos completa | Cada hora | Disco local y almacenamiento en la nube en otra región | 48 horas de copias horarias |
| Base de datos completa | Diaria | Disco externo desconectado y nube con versiones | 30 días |
| Base de datos completa | Mensual | Nube con protección contra borrado | 12 meses |
| Archivos subidos y configuración | Diaria | Disco externo y nube | 30 días |
| Código y contenedores | Continua | GitHub | Historial del repositorio |
| Claves y accesos de recuperación | Al cambiar | Gestor de contraseñas y copia impresa en sobre sellado fuera del local | Vigente |

*Nota.* Frecuencias y retenciones son una propuesta. Para recuperar a un punto en el tiempo puede usarse el registro binario de MySQL, además de las copias.

## Listas de revisión L1 a L4
*L1. Lista de revisión de código del sistema (PHP con CodeIgniter 3)*

| N.º | Pregunta de revisión |
| :---- | :---- |
| 1 | ¿El controlador verifica la sesión y el permiso del rol antes de ejecutar la acción? |
| 2 | ¿Las entradas se validan en el servidor (tipo, rango, campos obligatorios)? |
| 3 | ¿Las consultas usan Query Builder o parámetros, sin concatenar texto del usuario? |
| 4 | ¿Las contraseñas se guardan con hash y nunca se muestran? |
| 5 | ¿Las salidas hacia la vista se escapan para evitar inyección de código? |
| 6 | ¿El registro de la venta y el descuento de stock van en una sola transacción, con la comprobación de stock dentro de ella? |
| 7 | ¿Los errores se manejan sin mostrar detalles técnicos al usuario? |
| 8 | ¿Se respetan los nombres, la estructura y los comentarios acordados por el equipo? |
| 9 | ¿Hay lógica duplicada que debería estar en un solo lugar? |
| 10 | ¿El cambio tiene al menos un caso de prueba, enlazado a un requisito? |

*Nota.* Elaboración a partir de los riesgos del sistema (permisos, dinero, stock y credenciales).

**Tabla 12**  
*L2. Lista de revisión de requisitos y de la matriz de roles*

| N.º | Pregunta de revisión |
| :---- | :---- |
| 1 | ¿Cada requisito es completo, sin ambigüedades y verificable? |
| 2 | ¿Cada rol tiene definidas todas las operaciones sobre cada módulo (crear, leer, actualizar, eliminar)? |
| 3 | ¿La matriz es coherente con las funciones del módulo y con el flujo principal? |
| 4 | ¿Cada rol puede completar su trabajo con los permisos que tiene (por ejemplo, vender sin acceso a datos maestros)? |
| 5 | ¿Existe una vía definida para corregir los errores de cada rol? |
| 6 | ¿Los permisos respetan el mínimo privilegio y la separación de funciones? |
| 7 | ¿Cada requisito se enlaza con al menos un caso de prueba? |
| 8 | ¿Se identifican los requisitos que faltan? |

*Nota.* Elaboración a partir de los roles del sistema

**Tabla 13**  
*L3. Lista de revisión de diseño y diagramas*

| N.º | Pregunta de revisión |
| :---- | :---- |
| 1 | ¿El diseño cubre todos los requisitos que le corresponden? |
| 2 | ¿Los diagramas, los textos y las tablas usan los mismos nombres, niveles y códigos? |
| 3 | ¿Cada ruta de error del flujo aparece en el diagrama? |
| 4 | ¿Se indica dónde empieza y termina cada transacción? |
| 5 | ¿Se tratan los casos de concurrencia (dos operaciones sobre el mismo stock)? |
| 6 | ¿Cada módulo tiene un propósito único y un responsable? |
| 7 | ¿Las figuras y las referencias cruzadas existen y apuntan al elemento correcto? |
| 8 | ¿El diseño se puede probar con una entrada y un resultado esperado? |

*Nota.* Elaboración propia.

**Tabla 14**  
*L4. Lista de revisión de casos y datos de prueba*

| N.º | Pregunta de revisión |
| :---- | :---- |
| 1 | ¿Cada requisito tiene al menos un caso de prueba? |
| 2 | ¿Cada caso indica los datos de entrada, los pasos y el resultado esperado? |
| 3 | ¿Hay casos negativos, como un permiso denegado o un stock insuficiente? |
| 4 | ¿Los datos de prueba cubren valores límite y datos inválidos? |
| 5 | ¿Los resultados esperados se calcularon de forma independiente del código? |
| 6 | ¿Los casos son independientes entre sí? |
| 7 | ¿Los casos corresponden a la matriz de roles vigente? |

*Nota.* Elaboración a partir de los datos de prueba realizado al sistema


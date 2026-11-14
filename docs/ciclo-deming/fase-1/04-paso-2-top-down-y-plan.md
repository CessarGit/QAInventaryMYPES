# Fase 1 · Paso 2: análisis Top-Down y plan de pruebas

Este es el núcleo de la fase. Tiene tres momentos: confirmar la descomposición (subpaso 2.1), unificar la trazabilidad (subpaso 2.2) y fijar el plan de pruebas de la iteración (subpaso 2.3).

| Subpaso | Fecha de diseño | Fecha de desarrollo | Evidencia |
|---|---|---|---|
| 2.1 Confirmar la descomposición | 11/10/2026 | 13/11/2026 | PR #9, commit `fc5f3c1` |
| 2.2 Trazabilidad | 11/10/2026 | 13/11/2026 | PR #10, commit `ced890e` |
| 2.3 Plan de pruebas | 11/10/2026 | 13/11/2026 (PR #12 fusionado el 14/11/2026) | PR #12 |

## 1. Confirmar la descomposición (subpaso 2.1)

Los hallazgos de cohesión y acoplamiento que el informe formuló en teoría (Tabla 8 de Top-Down) se comprobaron en el sistema real. Cada uno se confirmó o se descartó con evidencia `archivo:línea`, sin cambiar código. Así, el Top-Down dejó de ser un análisis en papel.

| ID | Hallazgo del informe | Principio | Estado | Lo que muestra el código | Recomendación | Tipo | Issue |
|---|---|---|---|---|---|---|---|
| H-01 | Cada controlador valida sesión y permisos por separado; dependencia común a los 12 módulos | Acoplamiento | Confirmado | Los 12 controladores heredan de `Admin_Controller`, que carga los permisos (`MY_Controller.php:11-31`). 10 llaman a `not_logged_in()` en su constructor; **Reports no** (`Reports.php:7-12`). 37 métodos repiten la comprobación de permiso. **20 métodos públicos no comprueban permiso** (por ejemplo, `Attributes::createValue`, `Orders::getProductValueById`, `Products::upload_image`). Hooks desactivados (`config.php:103`) | Centralizar la autorización en un punto único, después de tener la suite de pruebas | Preventivo | #4 |
| H-02 | Settings y Profile tratan el perfil propio: uno lo muestra y otro lo edita | Cohesión | Confirmado | Ambos son métodos de `Users`: `profile` muestra (`Users.php:217-232`) y `setting` edita la cuenta de la sesión (`Users.php:234-241`); cada uno con su entrada en el menú | Documentarlo; unirlos en «Mi cuenta» es una mejora de prioridad baja | Preventivo | #5 |
| H-03 | La matriz da al Administrador CRUD en Settings, lo que mezcla la cuenta personal con la configuración global | Cohesión | No confirmado | `setting` solo actualiza la cuenta propia (`Users.php:241`, `:262`); no hay crear ni eliminar; la configuración global está en Company (`Company.php:37-46`) | La mezcla está en la matriz, no en el código: corregir la matriz en la revisión L2 | Corrección documental | — |
| H-04 | La venta descuenta stock; es una dependencia legítima, pero cruza dos módulos | Acoplamiento | Confirmado | El descuento lee el stock, resta en PHP y guarda con el `update()` de Products (`Model_orders.php:74-81`). **Sin transacción** y **sin comprobar el stock**: puede quedar negativo. La cantidad no se valida (`Orders.php:101`) | Descontar mediante Products, comprobando el stock dentro de una transacción. Confirmar con CP-01 a CP-03 | Correctivo y preventivo | #6 |
| H-05 | El proyecto menciona un catálogo de clientes que ningún módulo contiene | Brecha de descomposición | Confirmado | No hay tabla de clientes; el cliente es texto libre en cada venta (`stock.sql:155-157`) | Fuera del alcance (L-03); solicitud perfectiva a decidir con los usuarios | Perfectivo | #7 |
| H-06 | Perfil de la empresa separado de la configuración | Cohesión | Parcial | Módulo propio sobre la fila `id = 1` (`Company.php:23-60`), pero guarda también el cargo por servicio, el IVA y la moneda que usa Orders | Mantener separado y documentar que también es la configuración global | Ninguno | — |

**Resumen:** 4 confirmados (H-01, H-02, H-04 y H-05), 1 parcial (H-06) y 1 no confirmado (H-03). Las issues #4 a #7 siguen el formato de la plantilla Mantenimiento.

**Evidencia:** `docs/calidad/hallazgos-topdown.md` (PR #9); `2.1-issues.txt`.

## 2. Trazabilidad (subpaso 2.2)

Cada requisito quedó vinculado a los casos de prueba que lo verifican (Tabla 14 de PHVA). En `docs/calidad/trazabilidad.md` (PR #10) se resolvió que los casos tenían números repetidos y se completaron los que faltaban.

**Regla:** el resultado esperado sale de la matriz de roles (Tabla 5) y de la descripción del requisito, nunca del código. Lo que esas fuentes no permiten deducir se marca POR DEFINIR.

### El problema de los números repetidos

El informe tenía dos tablas de casos con la misma numeración y escenarios distintos:
- la Tabla 14 de PHVA, con CP-01 a CP-06;
- la Tabla 8 de pruebas, con CP-01 a CP-12.

Se adoptaron los identificadores de la Tabla 8, y cada caso de la Tabla 14 se reasignó:

| Caso de la Tabla 14 | Pasa a |
|---|---|
| CP-01 Cada rol accede solo a sus módulos | CP-04, CP-06, CP-07 |
| CP-02 Registrar una venta | CP-01, CP-02, CP-03 |
| CP-03 Generar el reporte con datos conocidos | CP-09, CP-12 |
| CP-04 Crear producto con marca, categoría y tienda | CP-08, CP-11 |
| CP-05 Crear usuario y asignarlo a un grupo | CP-13 (nuevo) |
| CP-06 Revisar el almacenamiento de contraseñas | CP-14 (nuevo) |

CP-13 y CP-14 se crearon porque RQ-05 y RQ-06 no tenían caso en la Tabla 8. Su nivel y técnica los propuso Claude y los aprobó el autor.

### Matriz de requisitos y casos

| Requisito | Casos | Estado |
|---|---|---|
| RQ-01 Acceso según el rol | CP-04, CP-05, CP-06, CP-07, CP-10 | Pendiente |
| RQ-02 La venta descuenta el stock | CP-01, CP-02, CP-03 | Pendiente |
| RQ-03 Reporte de ventas del año | CP-09, CP-12 | Pendiente |
| RQ-04 Alta de producto | CP-08, CP-11 | Pendiente |
| RQ-05 Gestión de usuarios y grupos | CP-13 | Pendiente |
| RQ-06 Contraseñas protegidas | CP-14 | Pendiente |

**Precondición común PC-0:** una base de pruebas `stock_test` con un grupo por cada rol de la Tabla 5 (Gerente, Ventas, Bodega, Logística y Administrador), cada uno con los permisos de su fila, y un usuario activo por rol. Hizo falta porque los datos semilla solo traen 2 grupos.

### Casos diseñados

| Caso | Requisito | Módulo | Nivel | Técnica | Datos | Resultado esperado |
|---|---|---|---|---|---|---|
| CP-01 Vender una cantidad igual al stock | RQ-02 | Orders | Unitaria | Valor límite | Cantidad = N; N POR DEFINIR | Venta registrada y stock en 0 |
| CP-02 Vender más que el stock | RQ-02 | Orders | Unitaria | Valor límite | Cantidad = N + 1 | Rechazo; el stock no cambia |
| CP-03 Cantidad 0 o negativa | RQ-02 | Orders | Unitaria | Valor límite | Cantidad 0 y −1 | Rechazo por validación en ambos casos |
| CP-04 Ventas intenta eliminar una venta | RQ-01 | Orders | Sistema | Caja negra | Usuario Ventas, desde la interfaz y escribiendo la dirección | Acceso denegado (Ventas: CR en Orders) |
| CP-05 Gerente edita una venta | RQ-01 | Orders | Sistema | Caja negra | Usuario Gerente | Permitido (Gerente: CRUD en Orders) |
| CP-06 Bodega intenta abrir Orders | RQ-01 | Orders | Sistema | Seguridad | Usuario Bodega, menú y dirección directa | Acceso denegado (Bodega sin acceso a Orders) |
| CP-07 Administrador y Gerente abren Users | RQ-01 | Users | Sistema | Seguridad | Usuarios Administrador y Gerente | Permitido al Administrador; denegado al Gerente |
| CP-08 Alta de producto con campos obligatorios vacíos | RQ-04 | Products | Unitaria | Caja negra | Campos obligatorios POR DEFINIR | Rechazo con mensaje de validación |
| CP-09 La venta se refleja en el reporte anual | RQ-03 | Orders y Reports | Integración | Integración | Venta de importe conocido; bruto o neto POR DEFINIR | La venta se refleja en los totales del año |
| CP-10 Cambia su contraseña e intenta cambiar la de otro | RQ-01 | Settings | Sistema | Seguridad | Usuarios A y B sin acceso a Users | Solo se permite la propia (Settings RU, propia) |
| CP-11 Flujo completo del Gerente | RQ-04 | Varios | Sistema | Flujo completo | Marca, categoría y producto nuevos; una venta | El flujo se completa sin error y el producto es visible |
| CP-12 Reporte anual frente a un registro manual | RQ-03 | Reports | Aceptación | Aceptación | Las mismas ventas en una hoja de cálculo | Los totales coinciden |
| CP-13 Crear usuario, asignarlo a un grupo y verificar permisos | RQ-05 | Users y Groups | Sistema | Caja negra | Usuario nuevo en el grupo Ventas | Se aplican exactamente los permisos de la fila Ventas |
| CP-14 La contraseña se guarda con hash | RQ-06 | Users | Sistema | Seguridad (inspección de la base) | Contraseña de prueba conocida; algoritmo POR DEFINIR | Contraseña con hash, no en texto plano |

Cada caso tiene en `trazabilidad.md` su ficha completa: precondiciones, pasos y evidencia prevista.

**Datos POR DEFINIR** (quedaron para que el autor los decidiera antes de ejecutar):
- el valor de N en CP-01, CP-02 y CP-03;
- los campos obligatorios en CP-08;
- el importe que suma el reporte en CP-09;
- el algoritmo de hash exigido en CP-14.

### Revisión con la lista L4

| N.º | Pregunta | Resultado | Observación |
|---|---|---|---|
| 1 | ¿Cada requisito tiene al menos un caso? | Sí | RQ-01 tiene 5 casos; RQ-02, 3; RQ-03 y RQ-04, 2 cada uno; RQ-05 y RQ-06, 1 cada uno |
| 2 | ¿Cada caso indica datos, pasos y resultado esperado? | Parcial | Hay datos POR DEFINIR |
| 3 | ¿Hay casos negativos? | Sí | Stock: CP-02 y CP-03. Permisos: CP-04, CP-06, CP-07 y CP-10. Validación: CP-08 |
| 4 | ¿Los datos cubren valores límite e inválidos? | Parcial | Faltan la cantidad 1 y los campos muy largos (Tabla 7 de pruebas) |
| 5 | ¿Los esperados son independientes del código? | Sí | CP-01 a CP-12 copian textualmente la Tabla 8, comprobado por comparación automática; CP-13 y CP-14 salen de la Tabla 14 y de la Tabla 5 |
| 6 | ¿Los casos son independientes entre sí? | Sí | Solo comparten la precondición PC-0 |
| 7 | ¿Corresponden a la matriz de roles vigente? | Sí | Ningún esperado contradice la Tabla 5 |

**Resultado:** 5 preguntas en Sí y 2 en Parcial. La revisión propuso fijar los datos POR DEFINIR antes de ejecutar y valorar casos de cantidad 1 y de campos largos. **Medición:** 6 de 6 requisitos tienen al menos un caso, es decir, el 100 % (Tabla 13 de PHVA). **Evidencia:** `2.2-L4.md`, firmada por el autor sin diferencias.

## 3. Plan de pruebas de la iteración (subpaso 2.3)

`docs/calidad/plan-pruebas-iteracion-1.md` (PR #12) define qué se prueba, en qué orden de riesgo, con qué criterios de éxito y qué hacer si hay retrasos. Se basa en las Tablas 9 y 10 de pruebas y en la Tabla 15 de PHVA (plan micro, por iteración).

### Alcance

| Aspecto | Definición |
|---|---|
| Casos | CP-01 a CP-14 |
| Requisitos | RQ-01 a RQ-06 |
| Prioridad | Ventas, stock y control de acceso primero (Tabla 9) |
| Módulos de la iteración | Orders, Products, Users, Groups, Settings y Reports |
| Fuera de alcance | Estrés y carga masiva (Tabla 7 de pruebas); usabilidad con usuarios de cada rol, porque requiere participantes externos |

### Estrategia: niveles y técnicas

| Nivel | Casos | Técnica |
|---|---|---|
| Unitaria | CP-01, CP-02, CP-03, CP-08 | Valor límite; caja negra |
| Integración | CP-09 | Caja negra (Orders con Reports) |
| Sistema | CP-04, CP-05, CP-06, CP-07, CP-10, CP-11, CP-13, CP-14 | Caja negra, seguridad y flujo completo |
| Aceptación | CP-12 | Comparación con un registro manual |

### Recursos

| Recurso | Uso |
|---|---|
| Staging del paso 1 | Pruebas de sistema y aceptación con PHP 7.4.33, Apache 2.4 y MySQL 5.7.44 |
| PHPUnit con ci-phpunit-test | Pruebas unitarias y de integración. La versión compatible con PHP 7.4 se confirma al construir la suite |
| pcov | Medición de cobertura de Orders y Products |
| Base `stock_test` | Datos de la precondición PC-0 |
| GitHub | Repositorio, issues con la plantilla Defecto y pull requests |
| Integración continua | Semáforo de la Tabla 18 |

El análisis estático, opcional según la Tabla 15, no se usa en esta iteración.

### Cronograma

Las fechas de diseño son las del autor en el plan. La ejecución real ocurrió en la Fase 2 y se toma de los commits y del registro de ejecución.

| Actividad | Casos | Diseño: inicio | Diseño: fin | Ejecución real |
|---|---|---|---|---|
| Preparar `stock_test` y los datos PC-0 | — | 15/10/2026 | 15/10/2026 | 14/11/2026 (commit `13fd565`) |
| Pruebas unitarias | CP-01, CP-02, CP-03, CP-08 | 16/10/2026 | 19/10/2026 | 14/11/2026 (ronda 1 automatizada) |
| Pruebas de integración | CP-09 | 20/10/2026 | 20/10/2026 | 14/11/2026 (ronda 1 automatizada) |
| Pruebas de sistema | CP-04, CP-05, CP-06, CP-07, CP-10, CP-11, CP-13, CP-14 | 21/10/2026 | 23/10/2026 | 14/11/2026 (automatizadas y manuales) |
| Pruebas de aceptación | CP-12 | 26/10/2026 | 26/10/2026 | 14/11/2026 (manual) |
| Registro de resultados y defectos | CP-01 a CP-14 | 16/10/2026 | 27/10/2026 | 14/11/2026 |

En el diseño, las pruebas duraban 13 días; en el desarrollo se ejecutaron en 1. Una causa probable: PHPUnit ejecutó en una sola corrida los casos automatizados de los niveles unitario, de integración y de sistema.

### Criterios

| Criterio | Contenido |
|---|---|
| Éxito (Tabla 9) | Todos los casos críticos aprobados; ningún defecto crítico o alto abierto; los defectos menores quedan registrados con un plan |
| Fallo (Tabla 9) | Cualquier caso crítico fallido o defecto crítico abierto detiene la liberación |
| Salida (Tabla 15, micro) | Casos ejecutados y resultados registrados |
| Casos críticos | Los no omitibles de la regla ante retrasos |

### Roles y registro

César Roque asume los cuatro roles: pruebas unitarias e integración; sistema y coordinación; usabilidad y aceptación; revisión y firma SQA.

El registro queda así:
- el estado de cada caso, en `trazabilidad.md`;
- los defectos, como issues con la plantilla Defecto, vinculadas al caso y a la severidad;
- las evidencias, en `docs/ejecucion/evidencias/`;
- cada subpaso, en la bitácora.

### Qué hacer si hay retrasos (Tabla 10 de pruebas)

| Situación | Respuesta | Lo que no se hace |
|---|---|---|
| No hay tiempo para todas las pruebas | Reordenar por riesgo: ventas, stock y control de acceso primero | Omitir pruebas de Orders, de stock o de permisos |
| Falta quien haga la prueba de aceptación | Reprogramarla | Dar por hecho que funciona |
| El retraso es de varios días | Ampliar el cronograma con justificación escrita | Liberar sin los casos críticos aprobados |
| Quedan módulos de bajo riesgo sin probar | Registrar la excepción y probarlos en la siguiente iteración | Dejar la excepción sin registro |

| Clase | Casos | Motivo |
|---|---|---|
| No omitibles | CP-01, CP-02, CP-03, CP-04, CP-05, CP-06, CP-07, CP-09, CP-10, CP-11, CP-13, CP-14 | Tocan Orders, stock o permisos; CP-14 toca credenciales (Tabla 17, punto 2) |
| Omitibles con excepción registrada | CP-08, CP-12 | Si se posponen, se registra la excepción y se ejecutan en la siguiente iteración |

### Desviaciones registradas en el plan

1. **Una persona en lugar de tres.** La Tabla 9 reparte los roles entre tres integrantes. Se compensa con autorrevisión mediante las listas L1 y L4, el semáforo de integración continua y la firma SQA en cada pull request.
2. **Tabla 10 con un solo integrante.** «Paralelizar entre los tres integrantes» y «designar un suplente» no aplican.
3. **Usabilidad fuera de alcance**, porque no hay participantes externos.
4. **Apéndice A.** No está en la fuente; se sustituye por la plantilla Defecto y las fichas de la trazabilidad.
5. **CP-14 no omitible**, porque verifica el almacenamiento de credenciales.
6. **Número de casos.** La Tabla 21 pide al menos 20 casos antes del cierre del proyecto; el plan tiene 14.

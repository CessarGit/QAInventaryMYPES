# Fase 2 · Hacer: guía de ejecución con Claude Code

Cubre los pasos 3 a 7 de la iteración:

| Paso | Qué produce | Temas del informe |
|---|---|---|
| 3 · Revisiones | RV-01 (matriz), RV-03 (Orders), RV-04 (Users, Groups y permisos) | Revisiones, seguridad |
| 4 · Respaldo y restauración | Scripts, prueba de restauración, simulacro RB-01, base `stock_test` con PC-0 | Recuperación de desastres |
| 5 · Pruebas y seguridad sobre 3.1.6 | Suite PHPUnit, 20 casos ejecutados, defectos registrados y corregidos | Pruebas, seguridad, mantenimiento correctivo |
| 6 · Actualizar a 3.1.13 | `system/` actualizado y prueba de regresión | Mantenimiento adaptativo |
| 7 · Reversión RB-06 | Solo si el paso 6 rompe algo | Recuperación de desastres |

Ciclo de cada subpaso (igual que en la Fase 1):

```
Rama → prompt → plan → apruebas → diff → /auditar X.Y → verificas tú → /bitacora X.Y → commit → PR → verde → fusionas
```

Para cada caso de prueba ejecutado se agrega `/registrar-caso CP-xx <resultado>`.

---

## 0 · Prerrequisitos (antes del paso 3)

**Cierre de la Fase 1:**
- [ ] `.env` corregido; `docker compose config --quiet` funciona y el reporte muestra PHP 7.4.33, pcov y MySQL 5.7.44
- [ ] Semáforo rojo demostrado
- [ ] L4 y bitácora 1.0 a 2.3 firmadas por ti
- [ ] PR #12 fusionado y `docs/README.md` actualizado
- [ ] Licencia AGPL con los avisos MIT conservados (`licenses/`, `NOTICE.md`)
- [ ] PR #1 de Dependabot cerrado y `dependabot.yml` reemplazado
- [ ] Etiqueta `fase1-cerrada` en `main`

**Preparación del kit:**
1. Copia en la raíz del repositorio `docs/calidad/plantillas/`, `docs/calidad/revisiones/`, `docs/calidad/decisiones-autor.md`, `docs/calidad/registro-ejecucion.md`, `.claude/commands/registrar-caso.md` y este archivo.
2. Pega el contenido de `CLAUDE-adicion-fase2.md` en `CLAUDE.md`, antes de "Estado de la iteración".
3. Commit en una rama `docs/kit-fase2`, PR y fusión.
4. Escribe en Claude Code:
   ```
   Lee CLAUDE.md, PROMPTS-FASE2.md y docs/calidad/hallazgos-topdown.md. Resume en 6 líneas
   las reglas nuevas de la Fase 2 y qué hallazgos condicionan las pruebas.
   ```
   **Auditoría:** el resumen debe mencionar las reglas 11 a 17, A-01, H-04 y la protección CSRF desactivada.

---

## Paso 3 · Revisiones (estáticas, antes de probar)

**Método de doble pasada.** El informe propone medir "si dos pares de ojos encuentran más que uno". Aquí:
1. **Primero revisas tú solo** con la lista, sin pedirle nada a Claude. Anotas tus hallazgos y tu tiempo (Ep, Ea).
2. **Después Claude hace una segunda pasada** con la misma lista, sin ver tus hallazgos.
3. **Comparas:** cuántos encontró solo cada uno y cuántos ambos.

Esto se declara como desviación: Claude es una herramienta de apoyo, no un revisor humano independiente.

### 3.1 RV-01 · Matriz de roles contra los permisos del código (L2) · modo **Plan**

Este subpaso tiene doble valor. Es la revisión de requisitos de la Tabla 7, y además produce el mapeo que necesitan los datos de prueba PC-0 del paso 4.

Tu pasada: aplica las 8 preguntas de L2 a `docs/calidad/matriz-roles.md` y anota tus respuestas en papel o en un archivo aparte.

Prompt para la segunda pasada:
```
Subpaso 3.1 (RV-01). Sin modificar nada:
1. Lista todos los identificadores de permiso que usa el código (por ejemplo 'createOrder'):
   los que se comprueban con in_array(..., $this->permission) en application/controllers/
   y los que se pueden asignar en application/views/groups/ (create y edit). Evidencia
   archivo:línea.
2. Construye la tabla de mapeo: Módulo | Operación de la Tabla 5 (C, R, U, D) |
   Identificador de permiso en el código | Dónde se comprueba (archivo:línea) | Discrepancia.
   Discrepancias posibles: operación de la matriz sin permiso en el código; permiso en el
   código sin operación en la matriz; permiso asignable que ningún método comprueba; método
   sin comprobación (H-01).
3. Responde las 8 preguntas de L2 sobre la matriz, con evidencia.
4. Incluye la corrección que H-03 pide para Settings del Administrador (el CRUD de la matriz
   no existe en el código) como propuesta para que yo la decida.
Entrega el resultado con la estructura de docs/calidad/plantillas/plantilla-revision.md,
con la columna "Encontrado por" en Claude. No mires mis respuestas: no las tienes.
```

Después, en modo **Normal**:
```
Crea docs/calidad/revisiones/RV-01-matriz.md con la plantilla. Fusiona mis hallazgos
(te los pego abajo) con los tuyos, marca en "Encontrado por" quién encontró cada uno, y
deja vacías las métricas Ep y Ea para que yo las llene.
[pega aquí tus hallazgos]
```

**Auditoría:**
- Revisa al menos cinco filas del mapeo contra el código (`archivo:línea`).
- **Decide tú** la corrección de H-03. Si cambias la matriz, hazlo en un commit propio sobre `matriz-roles.md`, con la nota "corrige la Tabla 5 del informe según RV-01". Ese mismo cambio debe llegar después a tu informe.
- El mapeo es la base de PC-0: si está mal, todos los casos de permisos estarán mal.

**Evidencia:** `RV-01-matriz.md` firmado e issues de los hallazgos mayores.

### 3.2 RV-03 · Código de Orders (L1) · modo **Plan**

Alcance: `application/controllers/Orders.php`, `application/models/Model_orders.php` y las vistas de `application/views/orders/`. Mide el tamaño con `wc -l` sobre esos archivos (WPS).

Tu pasada: las 10 preguntas de L1. Presta atención especial a la pregunta 6 (transacción y comprobación de stock: H-04) y a la 2 (validación de la cantidad).

Prompt para la segunda pasada:
```
Subpaso 3.2 (RV-03). Aplica la lista L1 (fuente-informe.md) a Orders.php, Model_orders.php y
las vistas de application/views/orders/. Para cada pregunta: Sí / No / N/A con evidencia
archivo:línea. En la pregunta 6 incluye qué pasa con dos ventas simultáneas del mismo
producto (concurrencia, L3-5). En la pregunta 5 revisa si los datos del cliente
(customer_name, customer_address, customer_phone) se escapan al mostrarse.
Clasifica cada hallazgo como mayor o menor según la Tabla 17 de revisiones e indica qué caso
de prueba (CP-xx) lo confirmaría. No propongas código corregido.
```
Luego crea `RV-03-orders.md` con la fusión, igual que en 3.1.

**Auditoría:** confirma tú las evidencias de las preguntas 2, 5 y 6. Son las que se convertirán en defectos.

### 3.3 RV-04 · Users, Groups, autenticación y permisos (L1) · modo **Plan**

Alcance: `Users.php`, `Groups.php`, `Auth.php`, `Dashboard.php`, `application/core/MY_Controller.php`, `Model_users.php`, `Model_groups.php` y `application/config/config.php` (sesión y CSRF).

Prompt para la segunda pasada:
```
Subpaso 3.3 (RV-04). Aplica L1 a los archivos indicados en PROMPTS-FASE2.md, 3.3. Además:
- A-01: describe el camino exacto del campo 'groups' desde el POST de setting hasta el UPDATE
  de user_group, y qué tendría que enviar un usuario para cambiarse de grupo. No lo ejecutes.
- Pregunta 4: cómo se genera y verifica la contraseña (función usada al crear, al editar y al
  iniciar sesión). Qué hace el método público Users::password_hash y si es accesible por URL.
- A-04: Dashboard.php:31 (user_id == 1).
- Configuración de sesión: sess_expiration, sess_regenerate_destroy, cookie_httponly,
  cookie_secure y csrf_protection, con su línea en config.php.
- Reports sin not_logged_in() (H-01): qué pasa si se abre /reports sin sesión, siguiendo el
  código. No lo ejecutes.
Clasifica mayor o menor e indica el caso de prueba que lo confirmaría.
```
Luego crea `RV-04-usuarios-permisos.md`.

**Auditoría:** el camino de A-01 debe estar completo, del formulario al UPDATE. Si se confirma en el paso 5, será tu defecto más grave.

### 3.4 Cierre de revisiones · modo **Normal**

```
Con RV-01, RV-03 y RV-04:
1. Calcula para cada revisión la densidad de error y el desglose autor / Claude / ambos con
   las métricas que llené.
2. Agrega a docs/calidad/revisiones/README.md un resumen con los indicadores de la Tabla 19
   de revisiones que ya se pueden calcular: cobertura de revisión de riesgo alto (revisiones
   hechas frente a planificadas: RV-01, RV-03, RV-04 y RV-05 son de riesgo alto; RV-05,
   Products, queda pendiente o se justifica), esfuerzo por revisión y densidad. El cierre de
   hallazgos mayores queda abierto hasta el paso 5.
3. Prepara (sin ejecutar) los gh issue create de los hallazgos mayores que no tengan issue,
   con la plantilla Mantenimiento o Defecto según corresponda.
```

**Auditoría:** la decisión de cada revisión (aceptar, aceptar con correcciones o rechazar) la tomas tú. Con hallazgos mayores como H-04 y A-01, lo esperable es "aceptar con correcciones", con la corrección prevista en 5.6.

**Evidencia:** las tres revisiones firmadas, el resumen y los issues.

---

## Paso 4 · Respaldo y restauración

### 4.0 Ajustes previos · modo **Normal**

```
1. En docker-compose.yml cambia TZ: America/Lima por TZ: America/El_Salvador.
2. Agrega backups/ a .gitignore y a .dockerignore.
Commit: "chore(docker): ajusta zona horaria e ignora respaldos".
```
**Auditoría:** `git check-ignore backups/x.sql.gz` debe imprimir la ruta. Reinicia con `docker compose up -d` y comprueba la hora con `docker compose exec db date`.

### 4.1 Scripts de respaldo, restauración y verificación · modo **Normal**

```
Subpaso 4.1. Crea tres scripts bash en scripts/ (con set -euo pipefail y comentarios que
citen la Tabla 11 y la Tabla 16 de recuperación):

1. respaldo.sh [base]: mysqldump dentro del contenedor db, usando la contraseña de root desde
   la variable del contenedor (docker compose exec -T db sh -c '... -p"$MYSQL_ROOT_PASSWORD"'),
   para que nunca aparezca en la línea de comandos del host. Opciones --single-transaction
   --routines --triggers. Salida en backups/<base>_AAAAMMDD_HHMMSS.sql.gz con su archivo
   .sha256. Muestra la hora de inicio y de fin. Base por defecto: stock.
2. restaurar.sh <archivo> <base_destino>: verifica el sha256, crea la base destino si no
   existe y restaura. Si la base destino es "stock", exige el parámetro --forzar.
3. verificar-conteos.sh <base_a> <base_b>: SELECT COUNT(*) de las 12 tablas de la Tabla 2
   (products, brands, categories, stores, attributes, attribute_value, orders, orders_item,
   users, groups, user_group, company) en ambas bases, tabla comparativa y código de salida
   distinto de 0 si alguna difiere.
Ningún script debe imprimir contraseñas ni contenido de la base.
```

**Auditoría:** antes de ejecutarlos, revisa los tres scripts con la lista **L5** (en `fuente-informe-2.md`). Esa es la revisión RV-10, de riesgo medio. Las preguntas 2, 3 y 4 de L5 son las clave. Guarda tus respuestas en `docs/calidad/revisiones/RV-10-scripts.md`.

### 4.2 Prueba de restauración (Tabla 20: restauración mensual)

Ejecuta tú y anota las horas:
```bash
bash scripts/respaldo.sh
time bash scripts/restaurar.sh backups/<archivo>.sql.gz stock_restore
bash scripts/verificar-conteos.sh stock stock_restore
```
**Criterio de éxito (Tabla 20):** base restaurada con el mismo número de registros y sin errores.

**Evidencia:** la salida de los tres comandos en `docs/ejecucion/evidencias/4.2-restauracion.txt`. Revisa que no contenga contraseñas.

### 4.3 Simulacro RB-01: pérdida total del servidor (manual)

`docker compose down -v` está bloqueado para Claude a propósito: lo ejecutas tú. Antes de empezar, pide a Claude que redacte el procedimiento con este prompt:
```
Redacta en docs/ejecucion/evidencias/4.3-simulacro-rb01.md el procedimiento del simulacro
RB-01 (Tabla 16 de recuperación) adaptado al staging, con una tabla para anotar las horas:
T-respaldo, T0 (fallo), T-sistema arriba, T-restaurado, T-verificado. Incluye dos
"registros testigo": un producto TESTIGO-ANTES creado antes del respaldo (debe aparecer tras
restaurar, lo que prueba que los datos vienen del respaldo y no del stock.sql inicial) y un
producto TESTIGO-DESPUES creado después del respaldo (no debe aparecer, y eso muestra la
pérdida dentro del RPO). Explica que, con el volumen vacío, MySQL vuelve a cargar stock.sql
antes de la restauración.
```

Ejecución (la haces tú, en este orden):
1. Crea el producto `TESTIGO-ANTES` desde la interfaz.
2. `bash scripts/respaldo.sh` y anota **T-respaldo**.
3. Crea el producto `TESTIGO-DESPUES`.
4. Anota **T0** y ejecuta `docker compose down -v`. Este es el "desastre".
5. `docker compose up -d --build` y anota cuándo responde la página.
6. `bash scripts/restaurar.sh backups/<archivo> stock --forzar`.
7. Verifica: `TESTIGO-ANTES` existe y `TESTIGO-DESPUES` no; inicia sesión con un usuario por rol. Anota **T-verificado**.

**Resultado:**
- **RTO medido** = T-verificado − T0, contra la meta de 4 h (Tabla 9 de recuperación).
- **RPO demostrado** = T0 − T-respaldo, contra la meta de 1 h. Aclara en la bitácora que en staging no hay respaldo cada hora: el RPO real depende de la frecuencia, y lo que el simulacro demuestra es que se pierde exactamente lo posterior a la última copia.

**Evidencia:** la tabla de horas y capturas de los dos testigos.

### 4.4 Base de pruebas `stock_test` con PC-0 · modo **Normal**

```
Subpaso 4.4. Prepara la base de pruebas:
1. scripts/preparar-stock-test.sh: crea (o recrea) la base stock_test con root dentro del
   contenedor, da GRANT ALL sobre stock_test al usuario de la aplicación (DB_USER), carga
   stock.sql y después application/tests/_datos/pc0.sql. Debe negarse a operar sobre "stock".
2. application/tests/_datos/pc0.sql: un grupo por rol de la Tabla 5 (Gerente, Ventas, Bodega,
   Logística, Administrador), con los permisos en el mismo formato que usa la tabla groups,
   tomados del mapeo de docs/calidad/revisiones/RV-01-matriz.md (no los inventes); un usuario
   activo por rol (gerente_test, ventas_test, bodega_test, logistica_test, admin_test) y su
   fila en user_group; y un producto con el stock N de D-01 en decisiones-autor.md.
3. Las contraseñas de los usuarios de prueba se generan con la misma función que usa la
   aplicación para crear usuarios (dime cuál es, con archivo:línea), a partir de la variable
   TEST_USER_PASS del .env. Agrega TEST_USER_PASS=cambiar-esto a .env.example. Si el hash
   debe generarse con PHP, hazlo dentro del contenedor app al ejecutar el script; no
   guardes hashes fijos en el repo.
```

**Auditoría:**
- Compara los permisos de cada grupo en `pc0.sql` con la fila de la Tabla 5 y con el mapeo de RV-01. Revisa al menos Ventas y Bodega completos.
- Ejecuta `bash scripts/preparar-stock-test.sh` y entra al sistema apuntando a `stock_test` (por ejemplo, con `DB_NAME=stock_test` en un `.env` temporal) con `ventas_test`. El menú debe mostrar solo lo que la matriz permite. Esa es una primera validación manual de PC-0.

**Evidencia:** captura del menú de `ventas_test` y de `bodega_test`.

---

## Paso 5 · Pruebas y seguridad sobre CodeIgniter 3.1.6

### 5.0 Decisiones del autor (manual, antes de cualquier prueba)

Llena `docs/calidad/decisiones-autor.md` (D-01 a D-06) y fírmalo. Después:
```
Actualiza las fichas de trazabilidad.md que tenían POR DEFINIR con las decisiones D-01 a D-05
de decisiones-autor.md, citando el ID de la decisión en cada ficha. No cambies nada más.
```
**Auditoría:** que ya no quede ningún "POR DEFINIR" (búscalo con `grep -n "POR DEFINIR" docs/calidad/trazabilidad.md`).

### 5.1 Casos adicionales CP-15 a CP-20 · modo **Normal**

Con estos seis casos se llega a los 20 que fija el objetivo SMART de la Tabla 21 de PHVA.
```
Agrega a trazabilidad.md seis casos nuevos con ficha completa. El resultado esperado sale de
la Tabla 5, de los requisitos o de la Tabla 13 de seguridad, nunca del código:
- CP-15 (RQ-01, seguridad): un usuario de Ventas envía el formulario de Settings con un campo
  'groups' añadido a mano. Esperado: su grupo no cambia (Tabla 5: Settings RU solo de la
  cuenta propia; sin acceso a Groups). Origen: A-01.
- CP-16 (RQ-01, seguridad): sin sesión, abrir /reports y los métodos sin comprobación de
  permiso listados en H-01 (por ejemplo attributes/createValue). Esperado: redirección al
  inicio de sesión o acceso denegado. Origen: H-01.
- CP-17 (RQ-04, seguridad): crear un producto con nombre <script>alert(1)</script> y abrir
  el listado. Esperado: el texto se muestra literal y no se ejecuta (Tabla 13, validación
  de entradas; L1-5).
- CP-18 (RQ-01, seguridad): enviar un formulario de alta sin token CSRF desde otra página.
  Esperado: rechazo (Tabla 13: "activar la protección CSRF de CodeIgniter").
- CP-19 (RQ-02, valor límite): vender cantidad 1 con stock N. Esperado: venta registrada y
  stock N − 1 (Tabla 7 de pruebas, valores límite).
- CP-20 (RQ-04, valor límite): alta de producto con un nombre más largo que el límite de
  D-05. Esperado: rechazo con mensaje de validación o truncado controlado, según D-05.
Actualiza la matriz de la sección 1 y la nota de cambios de la sección 3.
```
**Auditoría:** aplica L4 a los seis casos nuevos (preguntas 2, 3 y 5).

### 5.2 Infraestructura de PHPUnit · modo **Normal**

```
Subpaso 5.2. Prepara PHPUnit compatible con PHP 7.4 (Tabla 2):
1. composer.json: en require-dev, phpunit/phpunit ^9.6 y kenjis/ci-phpunit-test en la versión
   más reciente compatible con PHP 7.4, CodeIgniter 3 y PHPUnit 9 (verifícalo en su README o
   en composer.json antes de elegirla; dime qué versión y por qué). Agrega
   "config": {"platform": {"php": "7.4.33"}} para que composer.lock se resuelva para PHP 7.4.
2. Ejecuta composer update dentro del contenedor app (pídeme permiso) e instala
   ci-phpunit-test siguiendo exactamente el README de la versión instalada.
3. application/tests/phpunit.xml: suite en application/tests/, cobertura (pcov) limitada a
   controllers/Orders.php, controllers/Products.php, models/Model_orders.php y
   models/Model_products.php (Tabla 13 de PHVA: 60 % en Orders y Products).
4. Una prueba de humo que solo cargue CodeIgniter y compruebe la conexión a stock_test.
5. Comando para ejecutar la suite contra stock_test, por ejemplo:
   docker compose exec -e CI_ENV=testing -e DB_NAME=stock_test app \
     vendor/bin/phpunit -c application/tests/phpunit.xml --coverage-text
6. calidad.yml, job pruebas: antes de PHPUnit, carga stock.sql y pc0.sql en el servicio MySQL
   (genera el hash de TEST_USER_PASS con un valor desechable de CI) y aplica D-06
   (--exclude-group defecto-abierto si así lo decidí). Actualiza el comentario de la
   cabecera sobre el ámbar.
```

**Auditoría:**
```bash
docker compose exec app vendor/bin/phpunit --version   # esperado: PHPUnit 9.6.x
git diff composer.json                                  # phpunit ^9.6, platform 7.4.33
```
- La prueba de humo pasa en local y en GitHub Actions. El job `pruebas` ya no dice "Sin suite todavía".
- Si `composer update` cambió más paquetes de los esperados, revisa `composer.lock` y pide la explicación.

### 5.3 Pruebas automatizadas · modo **Normal**, un grupo de casos por vez

Orden por riesgo (Tabla 10 de pruebas: primero ventas, stock y permisos):

| Grupo | Casos | Cómo |
|---|---|---|
| A · Stock | CP-01, CP-02, CP-03, CP-19 | `$this->request('POST', 'orders/create', …)` de ci-phpunit-test con sesión de `ventas_test` y consulta del stock antes y después |
| B · Permisos | CP-04, CP-05, CP-06, CP-07, CP-10, CP-13, CP-15, CP-16 | Peticiones con la sesión de cada usuario de PC-0; se verifica redirección o acceso |
| C · Productos y reporte | CP-08, CP-09, CP-20 | Petición de alta de producto; venta de importe conocido y comparación con el reporte (D-03) |
| D · Contraseñas | CP-14 | Crear un usuario y consultar el campo `password` en `stock_test` (D-04) |

Prompt (uno por grupo):
```
Subpaso 5.3, grupo A. Escribe las pruebas de CP-01, CP-02, CP-03 y CP-19 en
application/tests/controllers/Orders_stock_test.php. Reglas:
- Cada prueba lleva en su docblock el ID del caso y el resultado esperado copiado de la ficha.
- Cada prueba prepara y limpia sus propios datos en stock_test (independencia, L4-6).
- Si para iniciar sesión hay que simular la sesión, explícame cómo lo haces y qué evita esa
  simulación (por ejemplo, el formulario de login).
- NO cambies el código de la aplicación. Si una prueba falla por un defecto real, déjala
  fallando, explícame por qué con archivo:línea y no la marques todavía.
Ejecuta la suite y muéstrame la salida completa.
```

**Auditoría (la más importante de toda la fase):**
- Compara la aserción de cada prueba con el resultado esperado de su ficha. Si la prueba de CP-02 comprueba "el stock queda negativo", está **mal**: debe comprobar "rechazo y el stock no cambia", y fallar.
- Ante cada falla: `/registrar-caso CP-xx falla`. Decide tú la severidad y crea el issue. Después pide que se marque `@group defecto-abierto` con el número de issue.

### 5.4 Pruebas manuales de sistema y seguridad

Casos manuales: CP-11 (flujo completo del Gerente), CP-12 (reporte contra registro manual), CP-17 (XSS) y CP-18 (CSRF). Repite además en el navegador CP-04, CP-06 y CP-07 escribiendo la URL directamente, como pide la Tabla 13.

Prompt de apoyo:
```
Redacta en docs/ejecucion/evidencias/5.4-guion-manual.md el guion de ejecución manual de
CP-11, CP-12, CP-17 y CP-18 (pasos numerados, datos y qué capturar). Para CP-12, prepara
una hoja de cálculo (CSV) con las ventas de prueba y la fórmula del total según D-03. Para
CP-18, describe cómo enviar el formulario sin token (por ejemplo, una página HTML local con
un formulario que apunte al staging). Agrega una sección de verificaciones de la Tabla 13 de
seguridad: atributos de la cookie de sesión (herramientas del navegador),
sess_expiration, búsqueda de consultas con texto concatenado
(grep -rn -- '->query(' application/) y ausencia de secretos.
```
Ejecútalo tú y registra cada caso con `/registrar-caso`.

**Evidencia:** capturas por caso en `docs/ejecucion/evidencias/5.4-*`.

### 5.5 Cierre de la ronda 1 (línea base 3.1.6) · modo **Normal**

```
Completa en registro-ejecucion.md el resumen de la ronda 1: ejecutados frente a diseñados,
pasan, fallan, bloqueados y cobertura de Orders y Products (de la salida de --coverage-text).
Compara con los umbrales de la Tabla 13 de PHVA y la Tabla 19 sin redondear.
Lista los defectos abiertos por severidad, con su issue.
```
**Auditoría:** los números coinciden con la salida de PHPUnit y con tus registros manuales.

### 5.6 Corrección de defectos críticos y altos (mantenimiento correctivo)

La Tabla 13 de PHVA exige **0 defectos críticos abiertos** al cerrar la iteración. Por cada defecto crítico o alto, en su propia rama `fix/<issue>-<descripcion>`:

```
Subpaso 5.6, issue #NN. Propón la corrección mínima del defecto, sin funcionalidad nueva.
1. Explica la causa con archivo:línea y la corrección propuesta antes de editar.
2. Al aplicarla, quita @group defecto-abierto de la prueba asociada; la prueba no se modifica.
3. Ejecuta la suite completa y muestra que la prueba pasa y que ninguna otra cambió de
   resultado.
4. Aplica la lista L1 al diff y muéstrame las respuestas.
```

Defectos probables según lo ya encontrado (la severidad la decides tú con la evidencia del paso 5):

| Origen | Corrección mínima esperada | Cuidado |
|---|---|---|
| A-01 (CP-15) | Que `setting` ignore el campo `groups` | No tocar la edición de usuarios del Administrador |
| H-04 (CP-02, CP-03) | Validar la cantidad y descontar el stock dentro de una transacción con comprobación previa | Es el cambio más delicado: repite todo el grupo A y CP-09 |
| CSRF (CP-18) | Activar `csrf_protection` | Puede romper formularios o AJAX que no envíen el token: repite **todos** los casos, no solo CP-18 |

Si un defecto es medio o bajo, no se corrige en esta iteración: queda registrado con su plan (Tabla 9 de pruebas).

### 5.7 Ronda 2 y línea base de regresión

```
Ejecuta la suite completa sobre main después de las correcciones, guarda la salida en
docs/ejecucion/evidencias/5.7-suite-base-3.1.6.txt y completa la ronda 2 del registro.
```
Etiqueta `pruebas-base-3.1.6` en `main` (la creas tú). **Esta es la referencia que el paso 6 no debe empeorar.**

---

## Paso 6 · Actualizar CodeIgniter a 3.1.13 (mantenimiento adaptativo)

### 6.1 Preparación (manual)

1. `bash scripts/respaldo.sh`: el respaldo previo al cambio es el punto de reversión de RB-06.
2. Crea el issue de mantenimiento: categoría adaptativo, Tabla 21 (mejora 2) y Tabla 11 de mantenimiento.
3. Descarga tú CodeIgniter 3.1.13 desde las *releases* oficiales del repositorio `bcit-ci/CodeIgniter` en GitHub y descomprímelo **fuera** del repositorio (por ejemplo `~/descargas/CodeIgniter-3.1.13/`).
4. Rama `chore/codeigniter-3.1.13`.

### 6.2 Actualización · modo **Normal**

```
Subpaso 6.2. Actualiza CodeIgniter de 3.1.6 a 3.1.13. El paquete oficial está en
<ruta donde lo descomprimiste>.
1. Antes de cambiar nada: lista qué dicen las notas de actualización de cada versión
   intermedia (3.1.7 a 3.1.13) de la guía de usuario incluida en el paquete
   (user_guide/installation/), y qué archivos de application/ o index.php podrían requerir
   cambios. Muéstramelo.
2. Comprueba si system/ tiene modificaciones locales respecto a 3.1.6 oficial (si las tiene,
   detente y avísame).
3. Reemplaza system/ por el de 3.1.13 y aplica solo los cambios que pidan las notas.
4. Verifica CI_VERSION y ejecuta la suite completa.
Esta es la única excepción a la regla 2 de CLAUDE.md.
```

**Auditoría:**
```bash
grep "CI_VERSION" system/core/CodeIgniter.php        # esperado: '3.1.13'
git diff --stat main -- application/ index.php        # solo lo que pidieron las notas
```

### 6.3 Ronda 3: regresión

```
Compara la salida de la suite en la rama con docs/ejecucion/evidencias/5.7-suite-base-3.1.6.txt.
Tabla: caso | resultado en 3.1.6 | resultado en 3.1.13 | ¿regresión?
Una regresión es un caso que pasaba y ahora falla (Tabla 13 de mantenimiento).
Completa la ronda 3 del registro.
```
Repite tú, de forma manual, CP-11 y una entrada al sistema con cada rol (prueba de humo).

- **Sin regresiones:** PR, semáforo verde, fusión, entrada en el CHANGELOG, cierra el issue y etiqueta `codeigniter-3.1.13`.
- **Con regresiones:** no fusiones. Pasa al paso 7.

---

## Paso 7 · Reversión RB-06 (solo si hay regresión)

Procedimiento (Tabla 16 de recuperación); anota las horas:
1. **T0:** detectas la regresión y detienes el cambio. No fusiones.
2. `git switch main` y `docker compose up -d --build`.
3. Si la base se modificó durante el paso 6: `bash scripts/restaurar.sh backups/<respaldo-6.1> stock --forzar`.
4. Verifica: `bash scripts/verificar-conteos.sh`, la suite en `main` (igual a la ronda 2) y una entrada con cada rol. Anota **T-fin**.
5. **RTO de RB-06** = T-fin − T0.
6. Registra la causa en el issue adaptativo, corrige en la rama y repite 6.2 y 6.3.

Prompt de apoyo:
```
Redacta la entrada de RB-06 en docs/ejecucion/evidencias/7-rb06.md con la tabla de horas,
la regresión detectada (caso, archivo:línea de la causa) y la verificación posterior.
```

---

## Cierre de la Fase 2 (criterio de salida de Hacer)

- [ ] RV-01, RV-03, RV-04 y RV-10 firmadas, con la comparación autor / Claude
- [ ] Mapeo de permisos de RV-01 y decisión sobre H-03 aplicada a la matriz
- [ ] Prueba de restauración con conteos idénticos
- [ ] Simulacro RB-01 con RTO y RPO medidos y los dos testigos
- [ ] `stock_test` con PC-0 validada con capturas de menú
- [ ] D-01 a D-06 firmadas; ningún POR DEFINIR en la trazabilidad
- [ ] 20 casos diseñados; ronda 1 completa en el registro
- [ ] Defectos registrados con severidad; 0 críticos abiertos tras 5.6
- [ ] Ronda 2 y etiqueta `pruebas-base-3.1.6`
- [ ] CodeIgniter 3.1.13 fusionado sin regresiones (ronda 3), o RB-06 documentado
- [ ] Bitácora 3.1 a 6.3 (y 7 si aplica) firmada por ti

Cuando esté completo, ejecuta de nuevo el script de verificación de la Fase 1 (agrega `gh issue list --state all` al final) y pasamos a la Fase 3: Verificar y Actuar (pasos 8 a 10).

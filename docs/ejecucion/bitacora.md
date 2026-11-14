# Bitácora de ejecución: iteración de calidad 1

Registro de la ejecución de la iteración PHVA. Cada entrada sigue `plantilla-paso.md` y enlaza su evidencia en `evidencias/`.

**Declaración de uso de herramientas:** durante la ejecución se usó Claude Code (extensión de VS Code) como herramienta de apoyo para generar configuración, documentación y análisis del código. Cada salida fue revisada, verificada y aprobada por el autor antes de integrarse; la revisión de la IA no sustituye una revisión humana independiente.

---

## Subpaso 1.0 · Inventario del repositorio

| Campo | Contenido |
|---|---|
| Fecha | 2026-11-13 |
| Referencia al diseño | Tabla 2 (componentes tecnológicos) y Tabla 7 (línea base) de `fuente-informe.md` |
| Objetivo | Conocer el estado real del repositorio antes de cambiar nada: versión de CodeIgniter, Docker, credenciales, estructura, controladores, SQL y entorno documentado |
| Herramienta y procedimiento | Claude Code en modo de solo lectura. `grep -n CI_VERSION system/core/CodeIgniter.php`; `find` de Dockerfile y docker-compose; `git log --oneline -- application/config/database.php`; `ls application/controllers application/models`; `grep` sobre `stock.sql`; `git show HEAD:README.md` |
| Resultado frente al umbral | No aplica. Hallazgos: CodeIgniter 3.1.6 (coincide con la Tabla 2); sin Docker; credenciales `root` sin contraseña en `database.php`; `encryption_key` pública en el historial; PHPUnit 11.5.2 incompatible con PHP 7.4; `stock.sql` generado con MariaDB 10.1, sin llaves foráneas y con solo 2 grupos de los 5 roles; entorno documentado XAMPP (O-05) |
| Evidencia | Tabla del inventario (`evidencias/1.0-inventario.md`, copiada del historial de la sesión el 14/11/2026) |
| Desviaciones respecto al diseño | El staging en Docker que suponía CLAUDE.md no existía; se creó desde cero en 1.1 |
| Uso de IA | Claude Code ejecutó los comandos de lectura y redactó la tabla con evidencia `archivo:línea`, enmascarando credenciales. El autor revisó la tabla y decidió eliminar archivos heredados innecesarios (`__MACOSX/`, `.eraser/`, `package-lock.json`) y no restaurar LICENSE |

**Revisión del autor**

- [x] Revisé el diff línea por línea
- [x] Verifiqué el resultado ejecutándolo yo
- [x] La evidencia está guardada
- [x] Apruebo el subpaso

**Firma del autor:** César Roque · 14/11/2026 (casillas marcadas por Claude Code con autorización del autor en el chat)

---

## Subpaso 1.1 · Entorno Docker de staging

| Campo | Contenido |
|---|---|
| Fecha | 2026-11-13 |
| Referencia al diseño | Tabla 2 (versiones); Tabla 13 de seguridad ("Contenedores de staging", "Firewall"); Tabla 9 de pruebas (staging en Docker con MySQL 5.7) |
| Objetivo | Levantar un staging reproducible con PHP 7.4.33, Apache 2.4, MySQL 5.7, pcov y Composer, con la base sin exponer |
| Herramienta y procedimiento | Docker 29.9.0 y Docker Compose 5.6.0. Archivos: `docker/app/Dockerfile`, `docker/app/php.ini`, `docker-compose.yml`, `.dockerignore`, `scripts/generar-env.sh`, `.env.example`. Verificación: `docker compose config --quiet`; `docker compose up -d --build`; `docker compose exec app php -v`; `php -m \| grep -iE 'pcov\|mysqli\|pdo_mysql'`; `docker compose exec db mysql --version`; `docker compose ps` |
| Resultado frente al umbral | Cumplido: PHP 7.4.33, MySQL 5.7.44, pcov activo; la aplicación solo escucha en `127.0.0.1` y la base no publica puertos. El autor confirmó que el build funciona |
| Evidencia | Commit `0ef5d0d`; salidas de `php -v`, `php -m`, `mysql --version`, `SHOW TABLES` y `docker compose ps`; error de apt de bullseye (`evidencias/1.1-error-apt-bullseye.txt`, copiada del historial de la sesión el 14/11/2026) |
| Desviaciones respecto al diseño | Imágenes fijadas a versión exacta (`php:7.4.33-apache`, `mysql:5.7.44`). Debian bullseye ya no tiene soporte y sus paquetes se descargan de `archive.debian.org`. `.env` se añadió a `.gitignore` en este subpaso. El `.dockerignore` de la raíz no tiene efecto porque el contexto de build es `docker/app`; se mantiene por decisión del autor |
| Uso de IA | Claude Code diseñó y escribió los archivos, detectó la causa del error 404 de apt y corrigió el Dockerfile. El autor creó `.env.example` (la regla de permisos impide a Claude escribirlo), generó su `.env` y ejecutó el build |

**Revisión del autor**

- [x] Revisé el diff línea por línea
- [x] Verifiqué el resultado ejecutándolo yo
- [x] La evidencia está guardada
- [x] Apruebo el subpaso

**Firma del autor:** César Roque · 14/11/2026 (casillas marcadas por Claude Code con autorización del autor en el chat)

---

## Subpaso 1.2 · Secretos fuera del repositorio

| Campo | Contenido |
|---|---|
| Fecha | 2026-11-13 |
| Referencia al diseño | Tabla 13 de seguridad ("Secretos en el repositorio", "Contenedores de staging") |
| Objetivo | Leer credenciales de la base, clave de cifrado y URL base desde variables de entorno |
| Herramienta y procedimiento | `getenv()` en `application/config/database.php` (líneas 79 a 82) y `application/config/config.php` (líneas 26 y 328). Verificación: `php -l` sobre ambos archivos en la imagen 7.4.33; `grep` de los campos; `docker compose exec app php -r 'var_dump((bool)getenv("DB_PASS"), strlen(getenv("ENCRYPTION_KEY")));'`; inicio de sesión en el navegador |
| Resultado frente al umbral | Cumplido: sin credenciales en archivos versionados; el inicio de sesión funciona (confirmado por el autor) |
| Evidencia | Commit `3255d55`; diff de `database.php` y `config.php`; salida de `var_dump`; captura del inicio de sesión y de un módulo abierto |
| Desviaciones respecto al diseño | `base_url` configurable (añadido con aprobación del autor). El historial de `database.php` nunca tuvo contraseña real (`79bd1da`, `a580c02`, `6b4db45`: `root`, vacía); la `encryption_key` sí fue pública desde `79bd1da` y se rotó. No se reescribe el historial |
| Uso de IA | Claude Code revisó el historial con valores enmascarados, editó los archivos y validó la sintaxis. El autor añadió las variables a `.env.example` y `.env` y verificó el inicio de sesión |

**Revisión del autor**

- [x] Revisé el diff línea por línea
- [x] Verifiqué el resultado ejecutándolo yo
- [x] La evidencia está guardada
- [x] Apruebo el subpaso

**Firma del autor:** César Roque · 14/11/2026 (casillas marcadas por Claude Code con autorización del autor en el chat)

---

## Subpaso 1.3 · Plantillas y etiquetas de GitHub

| Campo | Contenido |
|---|---|
| Fecha | 2026-11-13 |
| Referencia al diseño | Figura 1 y Tablas 11, 12 y 13 de mantenimiento; Tabla 17, puntos 4 a 8; Tablas 9 de pruebas, 18 y 19 (severidades) |
| Objetivo | Registrar defectos y solicitudes con plantillas, clasificarlos con etiquetas y estandarizar los pull requests |
| Herramienta y procedimiento | Archivos: `.github/ISSUE_TEMPLATE/defecto.yml`, `mantenimiento.yml`, `config.yml`, `.github/pull_request_template.md`, `scripts/crear-etiquetas.sh`. Ejecución: `bash scripts/crear-etiquetas.sh CessarGit/QAInventaryMYPES`; `gh label list`. Cambios en GitHub (fuera del repositorio): la rama `main` remota estaba vacía por borrados intencionales del autor; se respaldó en `respaldo/main-2026-11-13` (`e4cba59`), se reemplazó con `git push --force-with-lease=main:e4cba59` por el historial local, se subieron las etiquetas `linea-base` y `v1` y se protegió `main` (PR obligatorio, sin force-push ni borrado, también para administradores) |
| Resultado frente al umbral | Cumplido: 8 etiquetas (`tipo:*`, `sev:*`) creadas; plantillas en `main`; `main` protegida |
| Evidencia | Commit `8c9bb46`; `evidencias/1.3-etiquetas.txt`; salida de `gh api repos/CessarGit/QAInventaryMYPES/branches/main/protection`; captura de *Issues → New issue* |
| Desviaciones respecto al diseño | El extracto de mantenimiento se añadió a la fuente en un commit aparte (`820dc94`). Las plantillas heredadas en inglés y `FUNDING.yml` se eliminaron. Los formularios no aplican la etiqueta `sev:*` ni asignan responsable de forma automática. El historial remoto de `main` se reescribió con autorización del autor y con respaldo |
| Uso de IA | Claude Code escribió las plantillas y el script, ejecutó el script y los `git push` autorizados y aplicó la protección. El autor restauró la fuente sobrescrita por error (con ayuda de Claude), decidió reemplazar `main` y autorizó cada acción en GitHub |

**Revisión del autor**

- [x] Revisé el diff línea por línea
- [x] Verifiqué el resultado ejecutándolo yo
- [x] La evidencia está guardada
- [x] Apruebo el subpaso

**Firma del autor:** César Roque · 14/11/2026 (casillas marcadas por Claude Code con autorización del autor en el chat)

---

## Subpaso 1.4 · Integración continua con semáforo

| Campo | Contenido |
|---|---|
| Fecha | 2026-11-13 |
| Referencia al diseño | Tabla 18 (semáforo); Tablas 13 de PHVA y 19 (cobertura); Tabla 2; Tabla 13 de seguridad |
| Objetivo | Bloquear la integración de cambios que no pasen la verificación (rojo) e integrar los que sí (verde) |
| Herramienta y procedimiento | GitHub Actions, `.github/workflows/calidad.yml` con los jobs `sintaxis` (`php -l` sobre `application/` con PHP 7.4) y `pruebas` (servicio `mysql:5.7.44`; PHPUnit si existe `application/tests/phpunit.xml`). Prueba local previa con `php:7.4.33-apache`: 78 archivos, 0 errores. `gh pr checks 2`; `gh run view 43544869859 --log`. Cambio en GitHub: `sintaxis` y `pruebas` como checks obligatorios en `main` (rama actualizada exigida) |
| Resultado frente al umbral | Verde demostrado: PR #2 y ejecución sobre `main` (`f9a93de`) en verde; `pruebas` informa "Sin suite todavía (paso 5)". **Rojo pendiente de demostrar** (criterio de salida de la fase) |
| Evidencia | PR #2, commit `f9a93de`; salida de `gh pr checks 2`; protección de `main` en JSON |
| Desviaciones respecto al diseño | Acciones fijadas por versión mayor (`@v4`, `@v2`) y runner `ubuntu-24.04`. El ámbar queda pendiente de análisis estático y umbral de cobertura. El PR #2 se fusionó sin la casilla de firma SQA marcada, con autorización del autor en el chat |
| Uso de IA | Claude Code escribió el workflow, abrió y fusionó el PR #2 y configuró los checks obligatorios, con autorización del autor |

**Revisión del autor**

- [x] Revisé el diff línea por línea
- [x] Verifiqué el resultado ejecutándolo yo
- [x] La evidencia está guardada
- [x] Apruebo el subpaso

**Firma del autor:** César Roque · 14/11/2026 (casillas marcadas por Claude Code con autorización del autor en el chat)

---

## Subpaso 1.5 · Estructura de documentación

| Campo | Contenido |
|---|---|
| Fecha | 2026-11-13 |
| Referencia al diseño | Estructura de `docs/` de CLAUDE.md; Tabla 5; Tablas 2, 9 de pruebas y 13 de seguridad; Tabla 17, punto 8; Tabla 18 de auditoría (O-05) |
| Objetivo | Crear el índice, la matriz de roles, el diagrama de despliegue y el CHANGELOG |
| Herramienta y procedimiento | `docs/README.md`, `docs/calidad/matriz-roles.md`, `docs/arquitectura/despliegue.md` (Mermaid), `CHANGELOG.md` (Keep a Changelog). Verificación: `diff` de la Tabla 5 contra la fuente (vacío); `docker compose config` contra el diagrama; `git show --stat` de cada commit citado |
| Resultado frente al umbral | Cumplido: matriz idéntica a la Tabla 5; diagrama coincidente con el compose; cada entrada del CHANGELOG con su commit |
| Evidencia | PR #3, commit `1ea142d` |
| Desviaciones respecto al diseño | O-05 se citó por su nombre porque su texto no estaba aún en la fuente (hoy está en la Tabla 18 de auditoría de `fuente-informe-2.md`). Los cambios hechos en GitHub se registran en esta bitácora y no en el CHANGELOG, por decisión del autor |
| Uso de IA | Claude Code escribió los cuatro documentos y los verificó. El autor revisó y autorizó el commit y la fusión |

**Revisión del autor**

- [x] Revisé el diff línea por línea
- [x] Verifiqué el resultado ejecutándolo yo
- [x] La evidencia está guardada
- [x] Apruebo el subpaso

**Firma del autor:** César Roque · 14/11/2026 (casillas marcadas por Claude Code con autorización del autor en el chat)

---

## Subpaso 2.1 · Confirmar H-01 a H-06 en el código

| Campo | Contenido |
|---|---|
| Fecha | 2026-11-13 |
| Referencia al diseño | Tabla 8 de Top-Down (hallazgos de cohesión y acoplamiento); Tabla 11 de mantenimiento (tipo) |
| Objetivo | Confirmar o descartar cada hallazgo con evidencia `archivo:línea`, sin cambiar código |
| Herramienta y procedimiento | Lectura de controladores, modelos y `stock.sql`: `grep` de `not_logged_in`, `in_array(` y `trans_`; lectura de `Users::profile/setting`, `Model_orders::create/remove`, `Model_users::edit` y `Company`. Issues creadas con `gh issue create` (#4 a #8) |
| Resultado frente al umbral | No aplica. Confirmados H-01, H-02, H-04 y H-05; parcial H-06; no confirmado H-03 (la mezcla está en la matriz, no en el código). Adicionales: A-01 (posible escalada de privilegios en Settings, por verificar en el paso 5) y A-04 (administrador fijado a `user_id == 1`) |
| Evidencia | PR #9, commit `fc5f3c1`; `docs/calidad/hallazgos-topdown.md`; issues #4 a #8 (`evidencias/2.1-issues.txt`, generada desde GitHub el 14/11/2026) |
| Desviaciones respecto al diseño | Se añadieron hallazgos fuera de la Tabla 8 (A-01 y A-04, por decisión del autor). Las issues reproducen los campos de la plantilla de mantenimiento en el cuerpo, porque `gh` no rellena formularios YAML de forma no interactiva. Desde este subpaso los commits y PR no incluyen a Claude como coautor, por decisión del autor |
| Uso de IA | Claude Code hizo el diagnóstico y redactó la tabla y las issues. El autor debe abrir por sí mismo la evidencia de H-01 y H-04 (exigido por el manual) |

**Revisión del autor**

- [x] Revisé el diff línea por línea
- [x] Verifiqué el resultado ejecutándolo yo
- [x] La evidencia está guardada
- [x] Apruebo el subpaso

**Firma del autor:** César Roque · 14/11/2026 (casillas marcadas por Claude Code con autorización del autor en el chat)

---

## Subpaso 2.2 · Trazabilidad unificada

| Campo | Contenido |
|---|---|
| Fecha | 2026-11-13 |
| Referencia al diseño | Tabla 14 de PHVA (trazabilidad original); Tabla 8 de pruebas (casos); Tabla 5; lista L4 |
| Objetivo | Resolver el choque de identificadores y documentar las fichas de CP-01 a CP-14 |
| Herramienta y procedimiento | `docs/calidad/trazabilidad.md`. Verificación automática de que el resultado esperado de CP-01 a CP-12 empieza con el texto literal de la Tabla 8 (detectó y corrigió una reformulación en CP-04). Lista L4 aplicada en `evidencias/2.2-L4.md` |
| Resultado frente al umbral | Requisitos con al menos un caso: 6 de 6 (Tabla 13 de PHVA: 100 %). L4: 5 preguntas en Sí y 2 en Parcial (datos POR DEFINIR; sin casos de cantidad 1 ni de campos muy largos) |
| Evidencia | PR #10, commit `ced890e`; `evidencias/2.2-L4.md` (firmada por el autor el 14/11/2026) |
| Desviaciones respecto al diseño | Precondición común PC-0 (grupos de los cinco roles), porque los datos semilla solo traen dos grupos. Nivel y técnica de CP-13 y CP-14 propuestos y aprobados por el autor. Cinco datos POR DEFINIR |
| Uso de IA | Claude Code redactó las fichas y la evaluación preliminar de L4. El autor revisó el documento; la L4 queda pendiente de su verificación y firma |

**Revisión del autor**

- [x] Revisé el diff línea por línea
- [x] Verifiqué el resultado ejecutándolo yo
- [x] La evidencia está guardada
- [x] Apruebo el subpaso

**Firma del autor:** César Roque · 14/11/2026 (casillas marcadas por Claude Code con autorización del autor en el chat)

---

## Subpaso 2.3 · Plan de pruebas de la iteración

| Campo | Contenido |
|---|---|
| Fecha | 2026-11-13 |
| Referencia al diseño | Tabla 9 de pruebas (`fuente-informe.md`); Tabla 15 de PHVA (micro) y Tablas 6, 7 y 10 de pruebas (`fuente-informe-2.md`); Tabla 13 de PHVA |
| Objetivo | Planificar la ejecución de CP-01 a CP-14 con criterios, roles, cronograma y regla ante retrasos |
| Herramienta y procedimiento | `docs/calidad/plan-pruebas-iteracion-1.md`. Verificación: criterios de éxito y fallo literales de la Tabla 9; cinco filas literales de la Tabla 13 de PHVA y cuatro de la Tabla 10. La segunda parte de la fuente se añadió antes en un commit aparte (PR #11, `ad63b11`) |
| Resultado frente al umbral | No aplica (planificación). Fechas del cronograma completadas por el autor |
| Evidencia | Commit `5b1fc1a`, PR #12; el plan con las fechas del autor |
| Desviaciones respecto al diseño | Una persona en lugar de tres; Tabla 10 aplicada con un solo integrante; usabilidad fuera de alcance; Apéndice A sustituido por la plantilla Defecto; CP-14 no omitible; 14 casos frente a los 20 de la Tabla 21. Las fechas del cronograma (15/10/2026 a 27/10/2026) son anteriores a la fecha del plan; el autor confirma que son las reales (trabajo previo fuera del repositorio desde el 15/10/2026) |
| Uso de IA | Claude Code redactó el plan y verificó las partes literales. El autor completó las fechas |

**Revisión del autor**

- [x] Revisé el diff línea por línea
- [x] Verifiqué el resultado ejecutándolo yo
- [x] La evidencia está guardada
- [x] Apruebo el subpaso

**Firma del autor:** César Roque · 14/11/2026 (casillas marcadas por Claude Code con autorización del autor en el chat)

---

## Subpaso 3.1 · Revisión RV-01: matriz de roles contra los permisos del código

| Campo | Contenido |
|---|---|
| Fecha | 2026-11-13 |
| Referencia al diseño | Tabla 7 de revisiones (riesgo alto, inspección); lista L2; Tablas 16 a 18 de revisiones (`fuente-informe-2.md`); Tabla 5 (matriz de roles) |
| Objetivo | Contrastar la matriz de roles con los permisos que el código comprueba y obtener el mapeo de permisos que sirve de base para PC-0 (regla 16) |
| Herramienta y procedimiento | Verificación de escritorio del autor (01/11/2026) y segunda pasada de Claude Code (13/11/2026) con la plantilla de revisión: lectura de `application/controllers/*.php` y de `application/views/groups/{create,edit}.php`, búsqueda de las comprobaciones `in_array(..., $this->permission)` y comparación celda por celda con la Tabla 5. Fusión de los hallazgos de ambas pasadas. Decisión del autor sobre H-03 (opción A del Anexo C), aplicada a `matriz-roles.md` en el PR #13 |
| Resultado frente al umbral | 14 hallazgos (12 mayores, 2 menores); densidad 14 / 60 celdas × 1000 = 233,3; 1 solo del autor, 0 solo de Claude, 13 de ambos. Esfuerzo Ep + Ea = 2,15 h frente a un máximo de 10 (Tabla 19 de revisiones): cumplido. Er = 0,2 h. Decisión: aceptar con correcciones |
| Evidencia | `docs/calidad/revisiones/RV-01-matriz.md` (PR #21, `30c52c1`), con los Anexos A (36 identificadores de permiso), B (mapeo para PC-0) y C (opciones para H-03); matriz corregida en el PR #13 (`bc92004`); issues #14, #15 y #16 |
| Desviaciones respecto al diseño | La Tabla 7 pide inspección formal con tres personas; se trabaja solo y se compensa con la lista y la doble pasada. Claude Code es apoyo, no revisor humano independiente. La pasada del autor (01/11/2026) es anterior al repositorio (trabajo previo). La Tabla 5 de `fuente-informe.md` no se modifica, por decisión del autor: la corrección consta en `matriz-roles.md` y en RV-01 |
| Uso de IA | Claude Code hizo la segunda pasada, redactó los anexos y fusionó los hallazgos; amplió el alcance de H-RV01-04 (creación, edición y alta de valores de atributo). El autor aportó H-RV01-14 y la observación sobre sesiones de H-RV01-01, eligió la opción A para H-03, fijó las métricas y la decisión, y corrigió la fecha de la firma |

**Revisión del autor**

- [x] Revisé el diff línea por línea
- [x] Verifiqué el resultado ejecutándolo yo
- [x] La evidencia está guardada
- [x] Apruebo el subpaso

**Firma del autor:** César Roque · 14/11/2026 (casillas marcadas por Claude Code con autorización del autor en el chat)

---

## Subpaso 3.2 · Revisión RV-03: código del módulo Orders

| Campo | Contenido |
|---|---|
| Fecha | 2026-11-14 |
| Referencia al diseño | Tabla 7 de revisiones (riesgo alto, inspección); lista L1 y L3-5 (concurrencia); Tablas 16 a 18 de revisiones; Tabla 8 de Top-Down (H-01, H-04) |
| Objetivo | Revisar el controlador, el modelo y las vistas de Orders con la lista L1, con atención al descuento de stock (H-04) |
| Herramienta y procedimiento | Verificación de escritorio del autor (03/11/2026) y segunda pasada de Claude Code (14/11/2026) sobre `application/controllers/Orders.php`, `application/models/Model_orders.php` y `application/views/orders/{create,edit,index}.php` (commit `ad63b11`). Tamaño medido con `wc -l` |
| Resultado frente al umbral | 13 hallazgos (10 mayores, 3 menores); densidad 13 / 1516 líneas × 1000 = 8,6; 0 solo del autor, 0 solo de Claude, 13 de ambos. Esfuerzo Ep + Ea = 1,4 h frente a un máximo de 10: cumplido. Er no aplica todavía (se mide en el paso 5.6). Decisión: aceptar con correcciones |
| Evidencia | `docs/calidad/revisiones/RV-03-orders.md` (PR #21, `30c52c1`); issues existentes #4 y #6; issues nuevos #16, #17, #18 y #19 |
| Desviaciones respecto al diseño | Inspección formal sustituida por la doble pasada (se trabaja solo). H-RV03-02 (cantidad sin validar) no tiene issue propio: se confirma con CP-02 y CP-03 en el paso 5 y, si falla, se registra con la plantilla Defecto (regla 12). Cinco mayores no tienen caso de prueba en esta iteración |
| Uso de IA | Claude Code hizo la segunda pasada y redactó el informe, la justificación de la decisión y el formato de las métricas. El autor confirmó que la única regla de validación es `product[]` obligatorio (pregunta 2), coincidió con todos los hallazgos y aportó Ep y Ea |

**Revisión del autor**

- [x] Revisé el diff línea por línea
- [x] Verifiqué el resultado ejecutándolo yo
- [x] La evidencia está guardada
- [x] Apruebo el subpaso

**Firma del autor:** César Roque · 14/11/2026 (casillas marcadas por Claude Code con autorización del autor en el chat)

---

## Subpaso 3.3 · Revisión RV-04: Users, Groups y autenticación

| Campo | Contenido |
|---|---|
| Fecha | 2026-11-14 |
| Referencia al diseño | Tabla 7 de revisiones (riesgo alto, inspección); lista L1; Tablas 16 a 18 de revisiones; Tabla 13 de seguridad; hallazgos A-01 y A-04 (`hallazgos-topdown.md`) |
| Objetivo | Revisar la gestión de usuarios, grupos y permisos y la autenticación, y seguir el camino completo de A-01, del formulario al UPDATE |
| Herramienta y procedimiento | Verificación de escritorio del autor (03/11/2026) y segunda pasada de Claude Code (14/11/2026) sobre los controladores y modelos de Users, Groups y Auth y `MY_Controller`, con la configuración de sesión y CSRF de `application/config/config.php`. Tamaño medido con `wc -l` |
| Resultado frente al umbral | 18 hallazgos (13 mayores, 5 menores); densidad 18 / 1499 líneas × 1000 = 12,0; 0 solo del autor, 0 solo de Claude, 18 de ambos. Esfuerzo Ep + Ea = 0,7 h frente a un máximo de 10: cumplido. Er no aplica todavía (se mide en el paso 5.6). Camino de A-01 completo en el Anexo A. Decisión: aceptar con correcciones |
| Evidencia | `docs/calidad/revisiones/RV-04-usuarios-permisos.md` (PR #21, `30c52c1`); issues existentes #4 y #8; issues nuevos #15, #18 y #20 |
| Desviaciones respecto al diseño | Inspección formal sustituida por la doble pasada (se trabaja solo). A-01 no se ejecutó: se confirma con CP-15 y CP-10 en el paso 5. H-RV04-01 a H-RV04-04 y H-RV04-07 no tienen issue propio: se registran con la plantilla Defecto si el paso 5 los confirma (regla 12) |
| Uso de IA | Claude Code hizo la segunda pasada, redactó el Anexo A y, a pedido del autor, la justificación de la decisión. Registró primero su fecha de pasada como 13/11 y la corrigió a 14/11 con el historial de la sesión. El autor no tuvo hallazgos distintos y aportó Ep y Ea |

**Revisión del autor**

- [x] Revisé el diff línea por línea
- [x] Verifiqué el resultado ejecutándolo yo
- [x] La evidencia está guardada
- [x] Apruebo el subpaso

**Firma del autor:** César Roque · 14/11/2026 (casillas marcadas por Claude Code con autorización del autor en el chat)

---

## Subpaso 3.4 · Cierre de revisiones

| Campo | Contenido |
|---|---|
| Fecha | 2026-11-14 |
| Referencia al diseño | Tabla 19 de revisiones (indicadores); Tabla 18 de revisiones (decisión); Tabla 11 de mantenimiento y plantilla Mantenimiento |
| Objetivo | Calcular la densidad y el desglose autor / Claude / ambos de cada revisión, resumir los indicadores de la Tabla 19 y registrar como issue los hallazgos mayores que no lo tenían |
| Herramienta y procedimiento | `docs/calidad/revisiones/README.md` con los indicadores. Siete `gh issue create` con la plantilla Mantenimiento, preparados en un script, revisados por el autor y ejecutados con su autorización. Fusión del PR #21 |
| Resultado frente al umbral | Cobertura de revisión de riesgo alto: 3 / 4 = 75 % frente al 100 % antes de ejecutar las pruebas: **no alcanzada** (RV-05, Products, pendiente). Cierre de hallazgos mayores: 1 / 35 = 2,9 % (abierto hasta el paso 5). Esfuerzo por revisión: 2,15 h, 1,4 h y 0,7 h, con un máximo de 10: cumplido. Densidad de error: línea base establecida con las tres primeras revisiones. Eficacia de eliminación de defectos: no calculable hasta el paso 8 |
| Evidencia | `docs/calidad/revisiones/README.md` (PR #21, `30c52c1`); issues #14 a #20; PR #21 con la firma SQA del autor |
| Desviaciones respecto al diseño | RV-05 (Products) queda pendiente por decisión del autor; la cobertura se reporta tal cual (regla 17). Los 19 hallazgos mayores sin issue se agruparon por causa común en 7 issues. Siete mayores esperan al paso 5 para registrarse con la plantilla Defecto. CHANGELOG pendiente |
| Uso de IA | Claude Code calculó los indicadores, redactó el resumen y los issues, propuso la agrupación y las severidades (aceptadas por el autor) y creó los issues y el PR con su autorización. La firma SQA del PR #21 la registró Claude Code con autorización del autor en el chat |

**Revisión del autor**

- [x] Revisé el diff línea por línea
- [x] Verifiqué el resultado ejecutándolo yo
- [x] La evidencia está guardada
- [x] Apruebo el subpaso

**Firma del autor:** César Roque · 14/11/2026 (casillas marcadas por Claude Code con autorización del autor en el chat)

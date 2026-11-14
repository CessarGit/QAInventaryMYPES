# Fase 1 · Línea base

Antes de cambiar algo se registró el estado inicial del sistema: qué versión tiene, qué existe y qué falta. Es el «antes» contra el que se compara todo al final de la iteración.

El registro tiene dos partes:
- la etiqueta `linea-base` en el repositorio, que marca el código anterior a cualquier cambio;
- el inventario del subpaso 1.0, hecho en modo de solo lectura, que retoma la Tabla 7 y la confirma contra el sistema real.

Evidencia: `docs/ejecucion/evidencias/1.0-inventario.md`. Las líneas de código citadas corresponden al estado de ese momento.

| Subpaso | Fecha de diseño | Fecha de desarrollo |
|---|---|---|
| 1.0 Inventario del repositorio | 11/10/2026 | 13/11/2026 |

## 1. Tabla 7 frente al sistema real

| Indicador (Tabla 7) | Valor del informe | Confirmado en el sistema |
|---|---|---|
| Pruebas manuales o automáticas ejecutadas | 0 | 0: no existía la carpeta `application/tests/` |
| Repositorio central en GitHub | No existía; desarrollo aislado | Existía un historial Git heredado del proyecto original, en la rama `master`, sin pull requests ni integración continua |
| Registro de tareas | No existía | No había etiquetas ni plantillas en español; solo las plantillas de issue heredadas, en inglés, y `FUNDING.yml` |
| Casos de prueba documentados | 0 | 0 |
| Pruebas unitarias (PHPUnit) | 0 | 0. Además, `composer.lock` fijaba PHPUnit 11.5.2, que exige PHP 8.2 o superior y no se puede instalar en PHP 7.4.33 |
| Versión de CodeIgniter respecto a la última 3.1.x | 3.1.6 frente a 3.1.13 | 3.1.6 (`system/core/CodeIgniter.php:58`) |

## 2. Componentes tecnológicos (Tabla 2) frente al sistema real

| Componente | Tabla 2 | Encontrado |
|---|---|---|
| CodeIgniter | 3.1.6 | 3.1.6 |
| PHP | 7.4.33 | Sin entorno ejecutable que comprobar. El volcado `stock.sql` se generó con PHP 7.2.11 (`stock.sql:7-8`) |
| MySQL | 5.7.x | Sin entorno. El volcado se generó con MariaDB 10.1.36, la que trae XAMPP |
| Servidor web | Apache 2.4 | Sin entorno. La documentación heredada describía XAMPP/LAMP/MAMP con «PHP 5.6 o superior» |
| Acceso a datos | Query Builder | Query Builder |

## 3. Hallazgos del inventario que la Tabla 7 no recogía

| Elemento | Estado encontrado | Evidencia | Tema del informe afectado |
|---|---|---|---|
| Entorno | No existía Docker. El entorno documentado pedía «PHP 5.6 o superior» | Búsqueda de Dockerfile y docker-compose sin resultados; README heredado | Tabla 9 de pruebas (staging en Docker); observación O-05 |
| Credenciales de la base | `root` sin contraseña, escrita en el código y versionada en 5 commits | `application/config/database.php:78-82` | Tabla 13 de seguridad (secretos en el repositorio) |
| Clave de cifrado | `encryption_key` fija, la misma desde el commit inicial y pública en el historial | `application/config/config.php:327` | Tabla 13 de seguridad |
| Ajustes de seguridad | `csrf_protection = FALSE`, cookies sin `secure` ni `httponly`, `log_threshold = 0`, entorno `development` por defecto | `config.php:451`, `:406-407`, `:226`; `index.php:56` | Tabla 13 de seguridad (sesiones, validación de entradas) |
| Datos semilla | Solo 2 grupos (Administrator y Owners), frente a los 5 roles de la Tabla 5 | `stock.sql:142-144` | Tabla 5; casos de permisos |
| Base de datos | 12 tablas InnoDB sin ninguna llave foránea | `stock.sql:31-252` | Tabla 17, punto 11 |
| Usuario por defecto | La contraseña del usuario `admin` estaba publicada en el README heredado | README de la versión heredada | Tabla 13 de seguridad |
| Integración continua | `.github/` sin workflows; solo `dependabot.yml` | Archivos versionados de `.github/` | Tabla 18 de PHVA |
| Documentación | Faltaban el índice, la matriz de roles, la trazabilidad, los hallazgos, el plan de pruebas, la arquitectura y el CHANGELOG | Contenido de `docs/` | Tabla 17, punto 8 |
| `.gitignore` | No ignoraba `.env` | `.gitignore` | Regla de secretos |
| Módulos | 12 controladores. Settings y Profile no tienen controlador propio: son `Users::setting` y `Users::profile`. Auth y Dashboard no figuran en la matriz | `application/controllers/`; `Users.php:217`, `:234` | Hallazgos H-02 y H-03 |
| Control de permisos | `MY_Controller` carga los permisos serializados del grupo; los hooks están desactivados | `application/core/MY_Controller.php:29-30`; `config.php:103` | Hallazgo H-01 |
| Árbol de trabajo | Archivos heredados borrados sin commit y archivos basura versionados (`__MACOSX/`, `.vscode/`) | Estado de git | Higiene del repositorio |

## 4. Resumen del «antes»

| Existía | Faltaba |
|---|---|
| Código de la aplicación en CodeIgniter 3.1.6, con 12 controladores y 11 modelos | Entorno ejecutable (Docker) con las versiones de la Tabla 2 |
| Volcado `stock.sql` con 12 tablas y datos de demostración | Llaves foráneas; grupos para los 5 roles |
| Historial Git heredado y las etiquetas `linea-base` y `v1` | Rama `main`, pull requests, protección de rama |
| Control de permisos por grupo en cada controlador | Pruebas, casos documentados, trazabilidad |
| | Credenciales fuera del código; clave de cifrado privada |
| | Integración continua, registro de solicitudes, CHANGELOG |

## 5. Decisiones del autor tras el inventario

- Eliminar los archivos heredados innecesarios: `__MACOSX/`, diagramas de Eraser y `package-lock.json` vacío.
- No restaurar el archivo LICENSE heredado.
- Crear el staging en Docker desde cero (subpaso 1.1), porque no había ningún compose que adaptar.
- Registrar los ajustes de seguridad débiles como hallazgos, sin corregirlos en esta fase: se tratan en las fases siguientes.
- No reescribir el historial: la clave expuesta se rota y el hecho se documenta.

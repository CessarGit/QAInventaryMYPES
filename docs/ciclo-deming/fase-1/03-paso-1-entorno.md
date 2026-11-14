# Fase 1 · Paso 1: preparar el entorno de trabajo

El paso 1 establece cuatro condiciones de trabajo, cada una respaldada por un tema del informe. Se completa con una estructura documental. Se ejecutó en los subpasos 1.1 a 1.5, cada uno con su entrada firmada en la bitácora.

| Qué se prepara | Para qué sirve | Tema del documento |
|---|---|---|
| Un entorno de pruebas separado (staging) | Probar sin afectar la operación | Pruebas (Tabla 9, recursos) |
| Credenciales fuera del código | Proteger los accesos desde el inicio | Seguridad lógica (Tabla 13) |
| Registro de cambios y de defectos con categorías y severidad | Que cada solicitud siga un flujo controlado | Mantenimiento (Figura 1, Tabla 12) |
| Integración continua con semáforo | Que ningún cambio entre sin verificarse | PHVA (Tabla 18) |

| Subpaso | Fecha de diseño | Fecha de desarrollo | Evidencia |
|---|---|---|---|
| 1.1 Entorno de staging | 11/10/2026 | 13/11/2026 | Commit `0ef5d0d` |
| 1.2 Credenciales fuera del código | 11/10/2026 | 13/11/2026 | Commit `3255d55` |
| 1.3 Registro de cambios y defectos | 11/10/2026 | 13/11/2026 | Commit `8c9bb46` |
| 1.4 Integración continua | 11/10/2026 | 13/11/2026 | PR #2, commit `f9a93de` |
| 1.5 Estructura documental | 11/10/2026 | 13/11/2026 | PR #3, commit `1ea142d` |

## 1. Entorno de pruebas separado (subpaso 1.1)

**Respaldo:** Tabla 9 de pruebas, recursos: «entorno de staging en Docker con MySQL 5.7, datos semilla, PHPUnit y el repositorio de GitHub». También la Tabla 2 (versiones) y la Tabla 13 de seguridad (contenedores de staging y firewall).

**Qué se hizo.** Se creó desde cero un staging reproducible con dos servicios:

| Servicio | Contenido | Protección |
|---|---|---|
| `app` | Imagen `php:7.4.33-apache` con `mod_rewrite`, las extensiones `mysqli` y `pdo_mysql`, pcov (medición de cobertura) y Composer 2.2 | Solo escucha en la dirección local 127.0.0.1 |
| `db` | Imagen `mysql:5.7.44`, base `stock` cargada desde `stock.sql`, volumen persistente | No publica puertos: solo la alcanza la aplicación |

**Cómo se verificó.**
- Versiones dentro de los contenedores: PHP 7.4.33 con pcov, `mysqli` y `pdo_mysql` cargados, y MySQL 5.7.44.
- Las 12 tablas cargadas en la base.
- Los dos servicios en ejecución.
- El autor confirmó que la construcción funciona.

**Resultado:** cumplido. **Evidencia:** commit `0ef5d0d`; `1.1-error-apt-bullseye.txt`.

**Desviaciones:**
- Las imágenes se fijaron a versiones exactas.
- Debian bullseye, la base de la imagen de PHP 7.4, ya no tiene soporte; sus paquetes se descargan del archivo histórico de Debian. Fue la causa del primer error de construcción.

## 2. Credenciales fuera del código (subpaso 1.2)

**Respaldo:** Tabla 13 de seguridad, controles «Secretos en el repositorio» («no guardar contraseñas ni claves en el código; usar archivos de configuración fuera del repositorio») y «Contenedores de staging» («contraseñas distintas a las de producción»).

**Qué se hizo.**
- Las credenciales de la base, la clave de cifrado y la URL base se leen de variables de entorno, en `database.php` y `config.php`.
- Los valores viven en `.env`, que git ignora. El repositorio solo contiene `.env.example`, sin valores reales, y un script que genera `.env` con contraseñas aleatorias.
- La `encryption_key`, pública desde el commit inicial, se rotó.
- El historial no se reescribió: se comprobó que `database.php` nunca tuvo una contraseña real (solo `root` vacía), y la exposición de la clave quedó documentada.

**Cómo se verificó.** Sintaxis de los archivos en PHP 7.4.33; lectura de las variables dentro del contenedor, sin mostrar sus valores; inicio de sesión en el navegador.

**Resultado:** cumplido, sin credenciales en archivos versionados. **Evidencia:** commit `3255d55`; captura del inicio de sesión.

**Desviación:** `base_url` también pasó a ser configurable, con aprobación del autor.

## 3. Registro de cambios y de defectos (subpaso 1.3)

**Respaldo:** Figura 1 de mantenimiento: «cada solicitud se registra como una issue de GitHub, se clasifica por categoría y severidad, se implementa en una rama y solo se despliega después de las pruebas de regresión, la revisión por pares y la firma SQA». También la Tabla 12 de mantenimiento: changelog, commits convencionales y gestión de solicitudes con etiquetas.

**Flujo que quedó establecido:**

> issue → clasificación (categoría y severidad) → rama → implementación → pruebas de regresión → revisión → firma SQA → integración en `main`

**Plantillas de issue** (las issues sin plantilla quedaron desactivadas):

| Plantilla | Campos |
|---|---|
| Defecto | Caso de prueba, módulo, rol con el que se ejecutó, pasos, resultado esperado, resultado obtenido, severidad, evidencia y dos verificaciones: el esperado sale de la matriz o del requisito, y la evidencia no tiene contraseñas ni datos reales |
| Mantenimiento | Categoría, prioridad o severidad, justificación, módulos afectados, tabla del informe que lo respalda y alcance (sin funcionalidad nueva; deuda técnica) |

**Plantilla de pull request.** Pide un resumen, la issue que cierra, la categoría, la tabla que lo respalda y el caso y requisito asociados. Incluye una lista de verificación de 8 puntos:
1. Caso de prueba enlazado.
2. Lista L1 revisada.
3. Pruebas de regresión ejecutadas.
4. CHANGELOG actualizado.
5. Pipeline en verde.
6. Sin secretos.
7. Sin funcionalidad nueva.
8. Commits convencionales.

También tiene un espacio para el color del semáforo y la casilla de firma SQA.

**Etiquetas (8):**

| Etiqueta | Significado |
|---|---|
| `tipo:correctivo` | Corrige un defecto detectado (Tabla 11 de mantenimiento) |
| `tipo:adaptativo` | Adapta al entorno: framework, PHP, MySQL |
| `tipo:perfectivo` | Mejora o amplía; fuera del alcance de la iteración 1 |
| `tipo:preventivo` | Reduce riesgo futuro o deuda técnica |
| `sev:critica` | Bloquea la integración; se corrige en máximo una iteración (Tablas 18 y 19) |
| `sev:alta` | Detiene la liberación (Tabla 9 de pruebas) |
| `sev:media` | Se registra con un plan de corrección |
| `sev:baja` | Se registra con un plan de corrección |

**Rama `main` protegida.** Exige pull request para integrar, impide el force-push y el borrado, y aplica las reglas también a los administradores.

**Registro de cambios.** `CHANGELOG.md` con el formato Keep a Changelog. Commits en formato Conventional Commits, en español.

**Resultado:** cumplido. **Evidencia:** commit `8c9bb46`; captura de *Issues → New issue*.

**Desviaciones:**
- Los formularios no aplican la etiqueta de severidad ni asignan responsable de forma automática.
- El historial remoto de `main`, que estaba vacío por borrados intencionales, se reemplazó por el historial local, con autorización del autor y con respaldo previo en la rama `respaldo/main-2026-11-13`.

## 4. Integración continua con semáforo (subpaso 1.4)

**Respaldo:** Tabla 18 de PHVA:

| Color | Condición | Acción |
|---|---|---|
| Verde | Las pruebas pasan y la cobertura no baja del umbral | El cambio se integra |
| Ámbar | Las pruebas pasan, pero hay advertencias o la cobertura baja | Se integra solo con aprobación y el riesgo registrado |
| Rojo | Una prueba falla o hay un defecto crítico | Se bloquea la integración hasta corregirlo |

**Qué se hizo.** Un workflow de GitHub Actions (`calidad.yml`) que corre en cada pull request y en cada cambio de `main`, con dos verificaciones:

| Verificación | Qué comprueba |
|---|---|
| `sintaxis` | Que todos los archivos PHP de `application/` sean válidos en PHP 7.4. En la prueba previa se revisaron 78 archivos, con 0 errores |
| `pruebas` | Levanta una base MySQL 5.7.44 y ejecuta PHPUnit cuando existe la suite. En esta fase informaba «Sin suite todavía» |

Las dos se configuraron como **obligatorias** en `main`, y se exige que la rama esté actualizada. Si una falla, GitHub bloquea la fusión, que es el rojo de la Tabla 18.

**Resultado:** verde demostrado en el PR #2 y en la ejecución sobre `main` (`f9a93de`). El rojo quedó pendiente de demostrar al cerrar la fase.

**Desviaciones:**
- El ámbar no se implementó: requiere análisis estático y un umbral de cobertura, que no existían en esta fase.
- El PR #2 se fusionó sin marcar la casilla de firma SQA, con autorización del autor.

## 5. Estructura documental (subpaso 1.5)

Complementa las cuatro condiciones, para que cada decisión y cada evidencia tengan un lugar fijo.

| Documento | Contenido |
|---|---|
| `docs/README.md` | Índice de la documentación |
| `docs/calidad/matriz-roles.md` | Matriz de roles idéntica a la Tabla 5, comprobada por comparación con la fuente; única fuente de verdad sobre permisos |
| `docs/arquitectura/despliegue.md` | Diagrama de despliegue del staging, coincidente con el compose; resuelve la observación O-05 (entorno XAMPP documentado) |
| `CHANGELOG.md` | Cambios de la fase, cada uno con su commit |
| `docs/ejecucion/bitacora.md` | Una entrada por subpaso: objetivo, procedimiento, resultado, evidencia, desviaciones, uso de IA y firma del autor |

**Resultado:** cumplido. **Evidencia:** PR #3, commit `1ea142d`.

**Desviación:** los cambios hechos directamente en GitHub (protección de rama, etiquetas, checks obligatorios) se registran en la bitácora y no en el CHANGELOG, por decisión del autor.

# Changelog

Todos los cambios relevantes de este proyecto se documentan en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/) y los mensajes de commit siguen [Conventional Commits](https://www.conventionalcommits.org/es/v1.0.0/).

## [Unreleased]

### Añadido

- Entorno de staging con Docker: servicio `app` (`php:7.4.33-apache` con `mod_rewrite`, `mysqli`, `pdo_mysql`, `pcov` y Composer 2.2) y servicio `db` (`mysql:5.7.44`, base `stock`, volumen persistente y carga inicial de `stock.sql`) (`0ef5d0d`).
- `.env.example` con las variables del entorno y `scripts/generar-env.sh` para generar `.env` con contraseñas aleatorias (`0ef5d0d`, `3255d55`).
- Extracto del capítulo de mantenimiento (Tablas 11, 12 y 13 y Figura 1) en `docs/calidad/fuente-informe.md` (`820dc94`).
- Plantillas de issue `defecto.yml` y `mantenimiento.yml`, `config.yml` que desactiva las issues sin plantilla, plantilla de pull request y `scripts/crear-etiquetas.sh` (`8c9bb46`).
- Workflow de integración continua `calidad.yml` con los jobs `sintaxis` y `pruebas` (semáforo de la Tabla 18) (`f9a93de`).
- Índice de documentación, matriz de roles (Tabla 5), diagrama de despliegue y este changelog (`1ea142d`).
- `docs/calidad/hallazgos-topdown.md`: confirmación de H-01 a H-06 en el código y hallazgos adicionales A-01 y A-04 (`fc5f3c1`).
- `docs/calidad/trazabilidad.md`: trazabilidad unificada RQ-01 a RQ-06 con CP-01 a CP-14, fichas de los casos y nota de renumeración.

### Cambiado

- `application/config/database.php` lee el host, el usuario, la contraseña y la base desde variables de entorno (`3255d55`).
- `application/config/config.php` lee `encryption_key` y `base_url` desde variables de entorno (`3255d55`).
- `composer.json`: nueva descripción del proyecto; se retira el campo `license` (`bef9e17`).
- `application/views/templates/footer.php`: se retiran la versión y el aviso de copyright del pie de página (`bef9e17`).

### Eliminado

- Archivos heredados del proyecto original: `README.md` (instrucciones de XAMPP), `LICENSE`, `CODE_OF_CONDUCT.md`, `contributing.md`, diagramas de Eraser, plantillas de issue en inglés y `.github/FUNDING.yml` (`bef9e17`).
- Restos de macOS (`__MACOSX/`) y `package-lock.json` vacío (`bef9e17`).

### Seguridad

- `.env` se agrega a `.gitignore` y `.dockerignore`; las credenciales salen del repositorio (`0ef5d0d`).
- La `encryption_key`, que era pública en el historial, se rota y pasa a `.env` (`3255d55`).
- La base de datos de staging no publica puertos y la aplicación solo escucha en `127.0.0.1` (`0ef5d0d`).

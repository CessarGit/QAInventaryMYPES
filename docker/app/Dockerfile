# Imagen de la aplicación para staging.
# Versiones según la Tabla 2 del informe: PHP 7.4.33 y Apache 2.4.
FROM php:7.4.33-apache

# uid del autor en el host; permite a Apache escribir en el bind mount
# (assets/images/product_image, application/logs).
ARG HOST_UID=1000
ARG HOST_GID=1000

# Debian 11 (bullseye) terminó su LTS en agosto de 2026 y sus paquetes pasaron
# a archive.debian.org; deb.debian.org devuelve 404 al descargarlos.
RUN sed -ri 's#http://(deb|security)\.debian\.org#http://archive.debian.org#g' /etc/apt/sources.list

# Herramientas que Composer necesita para descargar paquetes.
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip git \
    && rm -rf /var/lib/apt/lists/*

# Extensiones de acceso a datos y de cobertura (Tabla 19: cobertura >= 60 %).
RUN docker-php-ext-install mysqli pdo_mysql \
    && pecl install pcov-1.0.11 \
    && docker-php-ext-enable pcov

# URLs limpias de CodeIgniter (.htaccess de la raíz).
RUN a2enmod rewrite \
    && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

# www-data con el uid/gid del host.
RUN groupmod -o -g "${HOST_GID}" www-data \
    && usermod -o -u "${HOST_UID}" -g "${HOST_GID}" www-data

# Composer 2.2 LTS: última rama con soporte para PHP 7.x.
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

COPY php.ini /usr/local/etc/php/conf.d/zz-inventario.ini

WORKDIR /var/www/html/InventorySystem

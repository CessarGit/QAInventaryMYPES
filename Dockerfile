FROM php:7.4-apache

# Extensiones de PHP requeridas por CodeIgniter y MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Debian 11 (bullseye) ya terminó su soporte: sus paquetes están en archive.debian.org
RUN echo "deb [check-valid-until=no] http://archive.debian.org/debian bullseye main" > /etc/apt/sources.list \
    && echo "deb [check-valid-until=no] http://archive.debian.org/debian bullseye-updates main" >> /etc/apt/sources.list \
    && rm -f /etc/apt/sources.list.d/*.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends git \
    && rm -rf /var/lib/apt/lists/* \
    && git config --system --add safe.directory '*'

# pcov para medir cobertura (se instala con pecl, no necesita apt)
RUN pecl install pcov-1.0.11 && docker-php-ext-enable pcov

# Habilitar mod_rewrite de Apache para URLs limpias en CodeIgniter
RUN a2enmod rewrite

# Permitir que Apache lea las reglas del .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copiar el código fuente al directorio público de Apache
COPY . /var/www/html/

# Configurar permisos para el servidor web
RUN chown -R www-data:www-data /var/www/html
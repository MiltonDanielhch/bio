FROM unit:1.33.0-php8.2

# 1. Variables de entorno
ENV DEBIAN_FRONTEND=noninteractive
ENV COMPOSER_ALLOW_SUPERUSER=1

# 2. Instalar dependencias del sistema y extensiones de PHP
#     Se agrupa la instalación y limpieza para reducir el tamaño de la imagen.
RUN --mount=type=cache,target=/var/cache/apt \
    apt-get update && apt-get install -y --no-install-recommends \
    curl unzip git libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pcntl opcache pdo pdo_mysql intl zip gd exif ftp bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
    && rm -rf /var/lib/apt/lists/*

# 3. Copiar archivo de configuración de PHP personalizado
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# 4. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# 5. Establecer el directorio de trabajo
WORKDIR /var/www/html

# 6. Optimización de caché: Copiar solo los archivos de dependencias e instalarlas
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader

# 7. Copiar el resto del código de la aplicación
COPY . .

# 8. Optimizar autoloader y scripts de Laravel
RUN composer dump-autoload --optimize --no-dev
RUN php artisan storage:link

# 9. Establecer permisos para el directorio de la aplicación
RUN chown -R unit:unit /var/www/html && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 10. [RESTABLECIDO Y CRÍTICO] Creamos los directorios de UNIT en /var/lib/unit
#     y el directorio de RUN para el socket de control.
RUN mkdir -p /var/lib/unit/certs /var/lib/unit/scripts /var/run/unit \
    && chown -R unit:unit /var/lib/unit /var/run/unit

# 11. Copiar la configuración de NGINX Unit
COPY unit.json /docker-entrypoint.d/unit.json

# 11.5. Copiar y establecer el nuevo punto de entrada
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# 12. Cambiar al usuario sin privilegios para ejecutar la aplicación
USER unit

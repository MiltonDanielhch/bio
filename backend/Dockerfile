FROM unit:1.33.0-php8.2

# 1. Variables de entorno para la instalación no interactiva
ENV DEBIAN_FRONTEND=noninteractive

# 2. Instalar dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    curl unzip git libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pcntl opcache pdo pdo_mysql intl zip gd exif ftp bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# 3. Copiar archivo de configuración de PHP personalizado
#    (Debes crear un archivo 'php.ini' en tu carpeta 'backend')
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# 4. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# 5. Establecer el directorio de trabajo
WORKDIR /var/www/html

# 6. Optimización de caché: Copiar solo los archivos de dependencias e instalarlas
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --optimize-autoloader --no-dev --no-interaction --no-scripts

# 7. Copiar el resto del código de la aplicación
#    Los archivos se copian como 'root' por defecto.
COPY . .

# 8. Ejecutar scripts de Composer post-instalación (como la optimización de Laravel)
RUN composer run-script post-autoload-dump --no-dev

# 9. Crear el enlace simbólico de storage
RUN php artisan storage:link --force

# 10. Establecer permisos finales para TODA la aplicación
#     Cambiamos el propietario de todos los archivos de 'root' a 'unit'.
RUN chown -R unit:unit /var/www/html && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Copiar la configuración de NGINX Unit
COPY unit.json /docker-entrypoint.d/unit.json

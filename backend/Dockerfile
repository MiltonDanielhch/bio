FROM unit:1.33.0-php8.2

# 1. Instalar dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    curl unzip git libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pcntl opcache pdo pdo_mysql intl zip gd exif ftp bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Configurar PHP (opcache, memory limit, etc.)
RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.jit=tracing" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.jit_buffer_size=256M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "upload_max_filesize=64M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/custom.ini

# 3. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# 4. Establecer el directorio de trabajo (consistente con docker-compose.yml)
WORKDIR /var/www/html

# 5. Copiar el código de la aplicación e instalar dependencias de Composer
COPY . .
RUN composer install --prefer-dist --optimize-autoloader --no-dev --no-interaction
RUN php artisan key:generate

# 6. Establecer permisos correctos para el usuario 'unit'
RUN chown -R unit:unit storage bootstrap/cache && chmod -R 775 storage bootstrap/cache

# 7. Copiar la configuración de NGINX Unit
COPY unit.json /docker-entrypoint.d/unit.json

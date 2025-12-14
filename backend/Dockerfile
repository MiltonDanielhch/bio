# --- Application Build Stage ---
FROM unit:1.33.0-php8.2

# 1. Environment Variables
ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_ALLOW_SUPERUSER=1

# 2. Install System Dependencies & PHP Extensions
RUN --mount=type=cache,target=/var/cache/apt \
    apt-get update && apt-get install -y --no-install-recommends \
    curl unzip git libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pcntl opcache pdo pdo_mysql intl zip gd exif bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
    && rm -rf /var/lib/apt/lists/*

# 3. PHP Configuration
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# 4. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# 5. Application Directory
WORKDIR /var/www/html

# 6. Install PHP Dependencies (Cache Layer)
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader

# 7. Copy Application Code
COPY . .

# 8. Optimize Autoloader
RUN composer dump-autoload --optimize --no-dev
RUN touch .env

# 9. Create Storage Directories
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs \
    && touch storage/logs/laravel.log

# 10. Permissions
RUN chown -R unit:unit /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Unit Configuration
RUN mkdir -p /var/lib/unit /var/run/unit \
    && chown -R unit:unit /var/lib/unit /var/run/unit \
    && chmod -R 775 /var/lib/unit /var/run/unit

COPY unit.json /docker-entrypoint.d/
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 12. Entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
USER unit

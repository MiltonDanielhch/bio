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

# 8. Clean Stale Cache (Critical fix for "Class not found" errors)
#    Remove local cache files that might have been copied and refer to dev dependencies (like Collision).
RUN rm -rf bootstrap/cache/*.php

# 9. Prepare Environment (Create .env and directories BEFORE autoload optimization)
#    We need these paths and .env to exist for 'php artisan package:discover' to work.
RUN touch .env \
    && mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && touch storage/logs/laravel.log \
    && chmod -R 775 storage bootstrap/cache

# 9. Optimize Autoloader
#    Use --no-scripts to prevent 'php artisan package:discover' from running during build.
#    This avoids errors about missing .env or cache paths. Discovery will happen at runtime.
RUN composer dump-autoload --optimize --no-dev --no-scripts

# 10. Permissions
RUN chown -R unit:unit /var/www/html \
    && chown -R unit:unit storage bootstrap/cache \
    && chmod -R 755 public

# 11. Unit Configuration
#     Ensure the socket directory exists and is writable by the unit user.
RUN mkdir -p /var/lib/unit /var/run/unit \
    && chown -R unit:unit /var/lib/unit /var/run/unit \
    && chmod -R 775 /var/lib/unit /var/run/unit

COPY unit.json /docker-entrypoint.d/
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 12. Entrypoint
#     We stay as root here so entrypoint.sh can fix permissions if needed,
#     then it (or unitd) drops privileges.
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
# USER unit  <-- REMOVED: Let entrypoint handle user switching or run as root to start, unitd drops privs.

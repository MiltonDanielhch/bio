#!/bin/sh
set -e

# 1. Wait for Database
echo "Waiting for database to be ready..."
until php -r "try { new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); } catch (PDOException \$e) { exit(1); }"; do
    echo "Database is unavailable - sleeping"
    sleep 2
done
echo "Database is ready!"

# 2. Production Setup (Only run this broadly, specific commands will follow)
# We run this on every container start for simplicity in this setup, 
# ensuring env is always fresh. In high-scale, move migration to a release phase.
echo "Running setup..."

# 2. Setup & Optimization
echo "Running runtime setup..."

# Ensure we start clean
php artisan optimize:clear

# Run package discovery explicitly at runtime since we skipped it in build
php artisan package:discover --ansi

if [ ! -L "public/storage" ]; then
    php artisan storage:link
fi

# Cache configuration if in production
if [ "$APP_ENV" = "production" ]; then
    echo "Caching configuration..."
    php artisan config:cache
    php artisan event:cache
    php artisan route:cache
    php artisan view:cache
fi

# 3. Decision Logic
# If arguments are passed to the container (e.g. via 'command' in docker-compose), execute them.
# Otherwise, start the Nginx Unit web server.
if [ "$#" -gt 0 ]; then
    # If the first argument is a hyphen, assume it's a flag for unitd (unlikely here but standard practice)
    if [ "${1#-}" != "$1" ]; then
        set -- unitd "$@"
    fi
    
    echo "Executing command: $@"
    exec "$@"
else
    # Default behavior: Start Web Server
    # Run migrations ONLY here to avoid race conditions with workers
    echo "Running migrations..."
    php artisan migrate --force
    
    echo "Starting Unit daemon..."
    exec /usr/local/bin/docker-entrypoint.sh unitd --no-daemon --control unix:/var/run/unit/control.sock
fi

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

# Force clear cache files manually but KEEP the directory structure
rm -f /var/www/html/bootstrap/cache/packages.php
rm -f /var/www/html/bootstrap/cache/services.php
rm -f /var/www/html/bootstrap/cache/config.php
rm -f /var/www/html/bootstrap/cache/routes-v7.php
rm -f /var/www/html/bootstrap/cache/*.php

# Ensure cache directory exists and is writable (Crucial for "valid cache path" error)
mkdir -p /var/www/html/bootstrap/cache
chown -R unit:unit /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/bootstrap/cache

# Ensure storage directories exist (Crucial for Compiler.php error)
# Volumes might hide the build-time directories, so we recreate them at runtime.
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/logs
chown -R unit:unit /var/www/html/storage
chmod -R 775 /var/www/html/storage

# Run package discovery explicitly at runtime
php artisan package:discover --ansi

# --- PUBLICACIÓN DE ASSETS Y ENLACE DE STORAGE ---
# Esto es crucial y debe ejecutarse siempre, tanto en desarrollo como en producción.

# 1. Publica los assets de Voyager (CSS, JS, etc.) para que el servidor web los encuentre.
php artisan vendor:publish --provider="TCG\Voyager\VoyagerServiceProvider" --tag=public --force

# 2. Recrea el enlace simbólico para que los archivos subidos (imágenes, etc.) sean accesibles.
php artisan storage:link --force

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
    # 3.1. Establecer el comando ejecutable (gosu/su)
    if command -v gosu >/dev/null 2>&1; then
        EXEC_CMD="gosu unit"
    elif command -v su >/dev/null 2>&1; then
        EXEC_CMD="su -s /bin/sh unit -c"
    else
        EXEC_CMD="" # Fallback a root
    fi

    # 3.2. Ejecutar comandos esenciales como el usuario 'unit'
    # Generar llave si es necesario (ejecutado en cada despliegue, inofensivo si ya existe)
    # Nota: key:generate force sobrescribe la llave. Solo deberíamos ejecutarlo si no existe llave o si se desea rotar.
    # En muchos casos, APP_KEY viene por variable de entorno y este comando puede ser redundante o peligroso si se pierden sesiones.
    # Sin embargo, siguiendo la instrucción:
    # $EXEC_CMD php artisan key:generate --force  <-- Comentado por seguridad, usualmente APP_KEY se inyecta por ENV.

    echo "Running migrations logic..."

    # --- LÓGICA CONDICIONAL CRÍTICA DE MIGRACIÓN (Seguridad de Datos) ---
    # La variable RUN_FRESH_INSTALL se configura en Coolify
    if [ "$RUN_FRESH_INSTALL" = "true" ]; then
        echo "🚨 WARNING: Running DESTROY/RESEED (migrate:fresh) because RUN_FRESH_INSTALL=true"
        # Borrado y siembra para la instalación INICIAL
        $EXEC_CMD "php artisan migrate:fresh --seed --force"
    else
        echo "Running standard migration (migrate --force) to preserve data..."
        # Migración normal para RE-DEPLOYS
        $EXEC_CMD "php artisan migrate --force"
    fi
    # --- FIN DE LÓGICA CONDICIONAL ---

    # Start Unit Daemon
    echo "Starting Unit daemon..."

    # Fix permissions for Unit control socket (Crucial for "Permission denied" error)
    if [ -d "/var/run/unit" ]; then
        # We might not be root here, but try to fix it if possible or ensure it's writable
        chmod -R 775 /var/run/unit
    fi

    exec /usr/local/bin/docker-entrypoint.sh unitd --no-daemon --control unix:/var/run/unit/control.sock
fi

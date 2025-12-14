#!/bin/sh
set -e

# 1. Bucle de espera robusto para la base de datos.
#    Intenta conectarse a la base de datos cada 2 segundos hasta que tenga éxito.
echo "Waiting for database to be ready..."
until php -r "try { new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); } catch (PDOException \$e) { exit(1); }"; do
    echo "Database is unavailable - sleeping"
    sleep 2
done
echo "Database is ready!"

# 2. Ejecutar los comandos de Laravel para producción.
#    Estos comandos son idempotentes y seguros para ejecutarse en cada inicio.
echo "Running Laravel production setup..."

# Limpia cachés antiguas para evitar conflictos antes de crear las nuevas.
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Ejecuta migraciones y crea el enlace de almacenamiento de forma segura.
php artisan migrate --force # --force es necesario para entornos no interactivos.

# Crea el enlace simbólico solo si no existe para evitar errores.
if [ ! -L "public/storage" ]; then
    php artisan storage:link
fi

# Crea las cachés optimizadas para producción.
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Ceder el control al script de entrada original de la imagen base.
#    Este se encargará de iniciar Unit correctamente con la configuración de /docker-entrypoint.d/
echo "Starting Unit daemon..."
exec /usr/local/bin/docker-entrypoint.sh unitd --no-daemon --control unix:/var/run/unit/control.sock

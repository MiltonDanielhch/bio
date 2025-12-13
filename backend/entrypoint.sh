#!/bin/sh
set -e

# 1. Esperar a que la base de datos esté completamente lista.
#    Esto evita errores de conexión durante el inicio.
echo "Waiting 15 seconds for database to be ready..."
sleep 15

# 2. Ejecutar los comandos de Laravel para producción.
echo "Running Laravel production setup..."
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Ejecutar el script de entrada original de la imagen.
#    Este script se encargará de iniciar Unit correctamente en segundo plano
#    y cargar la configuración de la aplicación desde /docker-entrypoint.d/unit.json.
echo "Loading application configuration..."
exec /usr/local/bin/docker-entrypoint.sh unitd --control unix:/var/run/unit/control.sock

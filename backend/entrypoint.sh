#!/bin/sh
set -e

# 1. Iniciar el demonio de Unit en segundo plano con el socket de control correcto.
echo "Starting Unit daemon..."
/usr/sbin/unitd --no-daemon --control unix:/var/run/unit/control.sock

# 2. Esperar a que el socket de control esté listo.
while [ ! -S /var/run/unit/control.sock ]; do
    echo "Waiting for control socket..."
    sleep 1
done

# 3. Ejecutar las migraciones para verificar la conexión a la BD.
echo "Running Laravel migrations..."
php artisan migrate --force

# 4. Ejecutar el script de entrada original para cargar la configuración de la aplicación.
echo "Loading application configuration..."
/usr/local/bin/docker-entrypoint.sh unitd

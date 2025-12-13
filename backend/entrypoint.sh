#!/bin/bash
# Script para asegurar los permisos de /var/run y luego iniciar NGINX Unit

# Ejecutamos el cambio de propietario como root para evitar 'Permission denied'
# El usuario 'unit' necesita escribir el socket de control en /var/run
chown -R unit:unit /var/run

# Se inicia el proceso principal (el entrypoint original de la imagen Unit)
exec docker-entrypoint.sh "$@"

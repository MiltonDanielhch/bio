#!/bin/sh
# Iniciar el demonio de Unit con el socket de control en el directorio correcto
/usr/sbin/unitd --control unix:/var/run/unit/control.sock

# Ejecutar el script de entrada original de la imagen base
/usr/local/bin/docker-entrypoint.sh "$@"

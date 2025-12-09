# Usar la imagen oficial de Nginx
FROM nginx:alpine

# Eliminar la configuración por defecto de Nginx
RUN rm /etc/nginx/conf.d/default.conf

# Copiar nuestro archivo de configuración personalizado al contenedor
COPY default.conf /etc/nginx/conf.d/
# Usar la imagen base de Nginx
FROM nginx:alpine

# Eliminar la configuración por defecto de Nginx
RUN rm /etc/nginx/conf.d/default.conf

# Copiar nuestro archivo de configuración personalizado a la imagen
COPY default.conf /etc/nginx/conf.d/
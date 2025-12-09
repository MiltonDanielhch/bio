# Etapa 1: Construcción de dependencias
# Usamos una imagen base de Python ligera y la nombramos 'builder'
FROM python:3.11-slim

# 1. Establecer el directorio de trabajo
WORKDIR /app

# 2. Copiar el archivo de requerimientos primero para aprovechar el cache de Docker
COPY requirements.txt .

# 3. Instalar las dependencias del proyecto
RUN pip install --no-cache-dir -r requirements.txt

# 4. Copiar el resto del código de la aplicación
COPY . .

# 5. Crear un usuario y grupo no-root para mejorar la seguridad
# Esto evita que la aplicación se ejecute con privilegios de administrador.
RUN addgroup --system --gid 1001 nonroot && \
    adduser --system --uid 1001 --ingroup nonroot nonroot

# 6. Cambiar la propiedad de los archivos al nuevo usuario
RUN chown -R nonroot:nonroot /app

# 7. Cambiar al usuario no-root
USER nonroot

# 8. Exponer el puerto en el que correrá la aplicación
EXPOSE 8001

# 9. Comando para ejecutar la aplicación en modo producción (sin --reload)
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8001"]
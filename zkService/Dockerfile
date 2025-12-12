# --- Etapa de Construcción (Builder) ---
# Usa una imagen completa de Python para instalar dependencias.
FROM python:3.11-slim as builder

# 1. Variables de entorno
ENV PYTHONDONTWRITEBYTECODE 1
ENV PYTHONUNBUFFERED 1

# 3. Establecer directorio de trabajo y copiar archivo de requerimientos
WORKDIR /app
COPY requirements.txt .

# 4. Instalar dependencias de Python
RUN pip wheel --no-cache-dir --wheel-dir /app/wheels -r requirements.txt

# --- Etapa Final (Production) ---
# Usa una imagen mínima para la ejecución.
FROM python:3.11-slim

WORKDIR /app

# 1. Instalar curl para el healthcheck
RUN apt-get update && apt-get install -y --no-install-recommends curl && rm -rf /var/lib/apt/lists/*

# 2. Copiar las dependencias pre-compiladas y el código de la aplicación
COPY --from=builder /app/wheels /wheels
COPY . .

RUN pip install --no-cache-dir /wheels/*

# El comando para iniciar la aplicación se define en docker-compose.yml
EXPOSE 8001
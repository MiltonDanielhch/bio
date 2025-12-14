# Documentación de Despliegue y Entornos (DevOps)

Esta sección detalla la arquitectura de contenedores, el flujo de desarrollo local y el proceso de despliegue en producción utilizando Docker y Coolify.

---

## 1. Arquitectura de Contenedores

El sistema **Gobebio** sigue una arquitectura de microservicios contenerizada orquestada mediante Docker Compose.

### Servicios Principales

| Servicio | Imagen / Tecnología | Puerto Interno | Puerto Expuesto Host | Función |
| :--- | :--- | :--- | :--- | :--- |
| **backend** | `gobebio-backend` (Nginx Unit + PHP 8.2) | 80 | `8081` | Servidor Web, API Laravel y Panel Voyager. |
| **zkservice**| `gobebio-zkservice` (Python 3.11 + FastAPI) | 8001 | `8001` | Interfaz de comunicación biométrica (TCP/UDP) y WebSockets. |
| **mysql** | `mysql/mysql-server:8.0` | 3306 | `3306` | Persistencia de datos relacionales. |
| **redis** | `redis:alpine` | 6379 | `6379` | Caché, sesiones y colas de trabajo. |
| **worker** | `gobebio-backend` (Reutilizada) | N/A | N/A | Procesamiento asíncrono de colas (Queue Worker). |
| **scheduler**| `gobebio-backend` (Reutilizada) | N/A | N/A | Ejecución de tareas programadas (Laravel Schedule). |

---

## 2. Entorno de Desarrollo Local (Hot Reload)

Para el desarrollo diario, utilizamos una configuración que permite ver cambios en tiempo real sin reconstruir imágenes.

### Configuración
Utilizamos una técnica de sobreescritura de Docker Compose:
1.  **`docker-compose.yaml`**: Define la base (imágenes, redes, dependencias).
2.  **`docker-compose.dev.yaml`**: Inyecta configuraciones solo para desarrollo:
    *   Monta el código fuente local (`./backend`) dentro del contenedor.
    *   Aísla la carpeta `vendor` (Linux) del host (Windows) para evitar conflictos.
    *   Habilita el modo `--reload` en Uvicorn para Python.

### Comandos de Desarrollo

#### Iniciar Entorno (Modo Dev)
```powershell
docker-compose -f docker-compose.yaml -f docker-compose.dev.yaml up -d
```

#### Detener Entorno
```powershell
docker-compose down
```

#### Limpiar Vistas/Caché (Si los cambios no se ven)
A veces Laravel cachea las vistas compiladas. Ejecuta esto dentro del contenedor:
```powershell
docker exec gobebio-backend php artisan view:clear
```

### Resolución de Conflictos de Puerto
Si tienes Coolify instalado en la misma máquina local, sus contenedores (`backend-gggk...`) pueden chocar con los tuyos (`gobebio-backend`).
**Solución:** Detén los contenedores de Coolify antes de iniciar el modo desarrollo.

---

## 3. Despliegue en Producción (Coolify)

El despliegue está optimizado para la plataforma Coolify, automatizando la construcción y gestión del ciclo de vida.

### Archivos Críticos de Despliegue

1.  **`backend/Dockerfile`**: Constucción multi-etapa optimizada.
    *   Elimina caché de construcción.
    *   Configura Nginx Unit.
    *   **Fix Crítico**: Elimina `bootstrap/cache/*.php` después del COPY para evitar errores de clases (e.g. Collision) pre-cacheadas en local.

2.  **`backend/entrypoint.sh`**: Script de arranque inteligente.
    *   **Autoreparación de Permisos**: Asigna `chown unit:unit` a `storage` y `bootstrap/cache` en cada arranque.
    *   **Migraciones Condicionales**: Protege la base de datos de borrados accidentales.

### Variables de Entorno (Coolify)

Estas variables deben configurarse en el panel de Coolify para el servicio:

| Variable | Valor | Importancia |
| :--- | :--- | :--- |
| `APP_ENV` | `production` | **Crítica**. Activa caché de configuración y optimizaciones de Laravel. |
| `APP_KEY` | `base64:...` | Llave de encriptación (generar con `php artisan key:generate`). |
| `RUN_FRESH_INSTALL` | `true` / `false` | Controla el comportamiento de las migraciones (Ver abajo). |

#### Control de Migraciones (`RUN_FRESH_INSTALL`)

*   **`true` (Modo Instalación)**: Ejecuta `php artisan migrate:fresh --seed`.
    *   ⚠️ **PELIGRO**: Borra TODA la base de datos y carga datos de prueba. Usar solo la primera vez.
*   **`false` (Modo Despliegue)**: Ejecuta `php artisan migrate --force`.
    *   ✅ **SEGURO**: Mantiene los datos y solo aplica cambios estructurales nuevos.

### Flujo de Actualización
1.  Hacer **Push** de los cambios a la rama principal (GitHub/GitLab).
2.  Coolify detecta el commit (o activar "Redeploy" manualmente).
3.  Docker construye la nueva imagen.
4.  `entrypoint.sh` ejecuta migraciones seguras.
5.  Nginx Unit inicia el servicio sin tiempo de inactividad perceptible.

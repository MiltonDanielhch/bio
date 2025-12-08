# Documentación del Microservicio: Componentes Centrales y Arranque

Este documento describe los archivos que actúan como el núcleo de la aplicación: el punto de entrada (`main.py`), la gestión de la configuración (`config.py`) y las dependencias de seguridad (`dependencies.py`).

---

## Módulo: `main.py`

Este es el **punto de entrada principal** del microservicio. Es el archivo que el servidor `uvicorn` ejecuta para iniciar la aplicación.

### 1. Propósito del Módulo

- Inicializar la instancia de la aplicación FastAPI.
- Gestionar el ciclo de vida de la aplicación (arranque y apagado).
- Registrar todos los `routers` para exponer los endpoints de la API.

### 2. Componentes Clave

#### `lifespan(app: FastAPI)` (Gestor de Ciclo de Vida)
- **Propósito**: Ejecutar código de forma controlada cuando la aplicación se inicia y cuando se detiene.
- **Al arrancar**:
    1.  Muestra un log de inicio.
    2.  Llama a `start_background_tasks()` para crear una tarea en segundo plano que se encargará del monitoreo continuo de dispositivos.
- **Al apagar** (ej. al detener el contenedor Docker o presionar Ctrl+C):
    1.  Cancela la tarea de monitoreo para detener el bucle infinito.
    2.  Llama a `cleanup_devices()` del `zk_service` para forzar el cierre de cualquier conexión de red que pudiera haber quedado abierta.

#### `app = FastAPI(...)`
- Es la instancia principal de la aplicación. Aquí se definen los metadatos globales de la API, como el título, la descripción y la versión, que son visibles en la documentación de Swagger UI.
- Se le asigna la función `lifespan` para gestionar su ciclo de vida.

#### `app.include_router(...)`
- Estas líneas registran los `APIRouter` de los módulos `devices`, `health` y `ws`. Al hacerlo, todos los endpoints definidos en esos archivos se vuelven parte de la aplicación principal.

---

## Módulo: `config.py`

### 1. Propósito del Módulo

Centraliza toda la configuración de la aplicación. Utiliza la librería `pydantic-settings` para leer variables desde un archivo `.env` o desde el entorno del sistema, proporcionando validación de tipos y valores por defecto.

### 2. Componentes Clave

#### `class Settings(BaseSettings)`
- Define todas las variables de configuración como atributos de clase con tipos definidos (ej. `str`, `int`, `bool`).
- **`API_KEY`**: El token secreto que el cliente (Laravel) debe enviar para autenticarse.
- **`KNOWN_DEVICES`**: Una cadena de texto con las IPs de los dispositivos a monitorear, separadas por comas. El código posterior se encarga de convertirla en una lista de Python.
- **`DEVICE_CHECK_INTERVAL`**: El intervalo en segundos entre cada ciclo de verificación de estado de los dispositivos.

#### `settings = Settings()`
- Se crea una única instancia global de la configuración, que puede ser importada y utilizada en cualquier parte del microservicio.

---

## Módulo: `dependencies.py`

### 1. Propósito del Módulo

Define funciones de dependencia que FastAPI puede inyectar en los endpoints para realizar tareas comunes, como la autenticación.

### 2. Componentes Clave

#### `async def validate_api_key(...)`
- **Propósito**: Proteger los endpoints. Es la implementación de la seguridad de la API.
- **Funcionamiento**:
    1.  Exige que la petición entrante contenga una cabecera (`Header`) llamada `x-api-key`.
    2.  Compara el valor de esa cabecera con el `settings.API_KEY` definido en la configuración.
    3.  Si no coinciden, lanza una excepción `HTTPException` con código `401 Unauthorized`, deteniendo la ejecución de la petición.
    4.  Si coinciden, la ejecución continúa normalmente.
- **Uso**: Se inyecta en el `APIRouter` de `devices.py`, protegiendo así todos los endpoints de ese módulo de una sola vez.


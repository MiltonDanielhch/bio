# Documentación del Microservicio: Modelos de Datos (Schemas)

## Módulo: `models/schemas.py`

### 1. Propósito del Módulo

Este módulo define todas las estructuras de datos (o "schemas") que utiliza la aplicación FastAPI. Usando la librería **Pydantic**, estos modelos cumplen tres funciones críticas:

1.  **Validación de Datos**: Validan automáticamente los datos de las peticiones entrantes. Si una petición no cumple con la estructura definida (por ejemplo, falta un campo o tiene un tipo de dato incorrecto), FastAPI devuelve un error 422 claro y descriptivo.
2.  **Serialización de Datos**: Convierten los objetos de Python en respuestas JSON que cumplen con el formato definido, asegurando consistencia en la salida de la API.
3.  **Documentación Automática**: FastAPI utiliza estos modelos para generar el esquema de OpenAPI, que alimenta la documentación interactiva de Swagger UI y ReDoc.

### 2. Modelos de Datos Definidos

#### `HealthCheck`
- **Propósito**: Modelo de respuesta para el endpoint de verificación de estado (`/health`). Simplemente confirma que el servicio está en línea.

#### `User`
- **Propósito**: Representa la estructura de un único usuario que será enviado o leído desde un dispositivo biométrico.
- **Campos Clave**:
    - `uid`: El ID numérico interno y único del usuario *dentro del dispositivo*.
    - `user_id`: Un identificador de tipo string, que en nuestro sistema se mapea al ID del empleado en la base de datos de Laravel.
    - `name`: Nombre completo del usuario.
    - `privilege`: Nivel de privilegio en el dispositivo (ej. 'User', 'Admin').

#### `UserSyncRequest`
- **Propósito**: Define el cuerpo (`body`) de la petición para el endpoint de sincronización de usuarios (`POST /devices/{ip}/sync-users`).
- **Estructura**: Contiene un único campo, `users`, que es una lista de objetos `User`.

#### `AttendanceRecord`
- **Propósito**: Representa una única marcación de asistencia obtenida del dispositivo.
- **Campos Clave**:
    - `uid`: El ID numérico del usuario que realizó la marcación.
    - `user_id`: El ID de tipo string del usuario.
    - `timestamp`: La fecha y hora exactas de la marcación.
    - `status`: Un código numérico que representa el tipo de verificación (ej. 1 para huella, 2 para tarjeta, etc.).
    - `punch`: Un código numérico que representa el tipo de marcación (ej. 0 para entrada, 1 para salida, etc.).

#### `AttendanceResponse`
- **Propósito**: Define la estructura de la respuesta para el endpoint que devuelve los registros de asistencia (`GET /devices/{ip}/attendance`).
- **Estructura**: Contiene la IP del dispositivo, un conteo total de registros y una lista (`records`) de objetos `AttendanceRecord`.

#### `DeviceInfo`
- **Propósito**: Contiene información detallada sobre el hardware y firmware de un dispositivo, obtenida directamente del mismo.
- **Campos Clave**:
    - `firmware_version`: Versión del firmware.
    - `serial_number`: Número de serie del dispositivo.
    - `mac_address`: Dirección MAC.

#### `StatusReport`
- **Propósito**: Es el modelo utilizado para reportar el estado de un dispositivo en el endpoint de monitoreo (`/devices/status`). Este es el modelo del objeto que se almacena en el caché en memoria `device_status`.
- **Estructura**:
    - `status`: `'online'` o `'offline'`.
    - `info`: Un objeto `DeviceInfo` opcional, presente solo si el estado es `online`.
    - `error`: Un string opcional con el mensaje de error, presente solo si el estado es `offline`.
    - `last_checked`: La fecha y hora (en formato ISO) de la última vez que se verificó el estado.


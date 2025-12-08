# Documentación del Microservicio: Servicios Principales

## 1. Propósito de la Carpeta `services`

La carpeta `services` contiene la lógica de negocio principal y el código que interactúa directamente con recursos externos, como los dispositivos biométricos o las conexiones WebSocket. Abstrae la complejidad de estas interacciones, ofreciendo funciones y clases claras para ser consumidas por los `routers` de la API.

---

## Módulo: `services/zk_service.py`

Este es el módulo más crítico del microservicio. Contiene toda la lógica para conectarse, comunicarse y gestionar los dispositivos biométricos ZKTeco.

### Componente Principal: `DeviceConnection` (Clase)

Esta clase es un **gestor de contexto asíncrono** (`async with`) que encapsula el ciclo de vida completo de una conexión a un dispositivo.

- **Propósito**: Garantizar que la conexión con un dispositivo se establezca, se utilice y, lo más importante, se cierre correctamente, incluso si ocurren errores.
- **Funcionamiento Asíncrono**: La librería subyacente `pyzk` es síncrona (bloqueante). Para evitar que estas operaciones de red bloqueen todo el servidor FastAPI, `DeviceConnection` utiliza `asyncio.to_thread()` para ejecutar cada llamada a la librería en un hilo separado. Esto permite que el microservicio maneje múltiples peticiones concurrentemente de manera eficiente.
- **Ciclo de Vida de la Conexión**:
    1.  `__aenter__`: Al entrar en el bloque `async with`, se conecta al dispositivo, lo deshabilita temporalmente (una práctica recomendada por `pyzk` para realizar operaciones) y se registra a sí misma en una lista global de conexiones activas.
    2.  `__aexit__`: Al salir del bloque, se asegura de que el dispositivo se vuelva a habilitar y que la conexión se cierre, sin importar si hubo éxito o un error dentro del bloque. También se elimina de la lista de conexiones activas.

### Métodos de `DeviceConnection`

- `get_users()`: Obtiene la lista de usuarios del dispositivo y la transforma en una lista de modelos `User` de Pydantic.
- `get_attendance()`: Obtiene los registros de asistencia y los transforma en una lista de modelos `AttendanceRecord`.
- `get_device_info()`: Obtiene varios datos técnicos del dispositivo y los consolida en un modelo `DeviceInfo`.
- `clear_attendance()`: Borra todos los registros de asistencia del dispositivo.
- `set_users(users: List[User])`: Realiza la sincronización de usuarios. Es una operación **destructiva**: primero borra todos los usuarios existentes en el dispositivo y luego sube la nueva lista.

### Funciones de Servicio (Wrappers)

El módulo también expone funciones como `get_attendance_from_device`, `sync_users_to_device`, etc.

- **Propósito**: Actúan como una capa de servicio limpia que los `routers` pueden llamar.
- **Funcionamiento**: Cada una de estas funciones simplemente instancia y utiliza el gestor de contexto `DeviceConnection` para realizar una operación específica. También se encargan de capturar cualquier excepción y convertirla en una `HTTPException` apropiada que FastAPI pueda entender y devolver al cliente.

### Limpieza de Conexiones (`cleanup_devices`)

- **Propósito**: Es una función de seguridad que se llama cuando la aplicación FastAPI se está cerrando.
- **Funcionamiento**: Itera sobre cualquier conexión que pudiera haber quedado "colgada" en la lista `active_connections` y fuerza su cierre, asegurando una terminación limpia del servicio.

---

## Módulo: `services/ws_service.py`

Este servicio gestiona las conexiones WebSocket para la comunicación en tiempo real.

### Componente Principal: `ConnectionManager` (Clase)

- **Propósito**: Mantener un registro de todos los clientes (navegadores) que están actualmente conectados al servidor a través de WebSocket.
- **Gestión de Conexiones**:
    - `active_connections`: Una lista que almacena los objetos `WebSocket` de cada cliente conectado.
    - `_lock`: Un `asyncio.Lock` que previene condiciones de carrera. Asegura que solo una corutina pueda modificar la lista `active_connections` a la vez, lo cual es crucial en un entorno asíncrono donde múltiples clientes pueden conectarse o desconectarse simultáneamente.

### Métodos de `ConnectionManager`

- `connect(websocket)`: Acepta una nueva conexión WebSocket y la añade a la lista `active_connections` de forma segura.
- `disconnect(websocket)`: Elimina una conexión de la lista cuando un cliente se desconecta.
- `broadcast(data)`: Envía un mensaje (en formato JSON) a **todos** los clientes conectados. Incluye lógica para detectar y eliminar "conexiones muertas" (clientes que se desconectaron de forma abrupta).

### Instancia Global

- `manager = ConnectionManager()`: Se crea una única instancia global del gestor. Esto asegura que todos los endpoints y partes de la aplicación compartan el mismo estado de conexiones WebSocket.


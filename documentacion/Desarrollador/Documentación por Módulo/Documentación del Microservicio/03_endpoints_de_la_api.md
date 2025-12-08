# Documentación del Microservicio: Endpoints de la API (Routers)

### 1. Propósito de la Carpeta `routers`

Esta carpeta contiene los módulos que definen los endpoints de la API de FastAPI. Cada archivo agrupa un conjunto de rutas relacionadas lógicamente. FastAPI utiliza estos `APIRouter` para estructurar la aplicación, aplicar prefijos, etiquetas y dependencias a grupos de endpoints.

---

## Módulo: `routers/devices.py`

Este es el router más importante, ya que expone toda la funcionalidad relacionada con la interacción con los dispositivos biométricos.

- **Prefijo**: `/devices`. Todas las rutas en este módulo comenzarán con `/devices`.
- **Etiqueta**: `Devices`. En la documentación de Swagger UI, todos estos endpoints aparecerán agrupados bajo la etiqueta "Devices".
- **Dependencia Global**: `Depends(validate_api_key)`. **Todas las rutas** en este módulo están protegidas y requieren que se proporcione una API Key válida en la cabecera `x-api-key` de la petición.

### Endpoints Definidos:

#### `GET /status`
- **Propósito**: Obtener el estado en tiempo real de todos los dispositivos monitoreados.
- **Funcionamiento**: No se conecta a ningún dispositivo. Simplemente devuelve el contenido actual del diccionario en memoria `device_status`, que es actualizado en segundo plano por las tareas de monitoreo. Es una operación de lectura muy rápida.

#### `GET /{ip}/attendance`
- **Propósito**: Descargar todos los registros de asistencia (marcaciones) de un dispositivo específico.
- **Funcionamiento**: Delega la llamada a `zk_service.get_attendance_from_device`, que se conecta al dispositivo, descarga los logs y los devuelve.

#### `GET /{ip}/users`
- **Propósito**: Obtener la lista completa de usuarios registrados en la memoria de un dispositivo.
- **Funcionamiento**: Llama a `zk_service.get_users_from_device`.

#### `GET /{ip}/info`
- **Propósito**: Obtener información técnica del dispositivo (versión de firmware, número de serie, etc.).
- **Funcionamiento**: Llama a `zk_service.get_info_from_device`.

#### `POST /{ip}/test-voice`
- **Propósito**: Endpoint de utilidad para probar la conexión y hacer que el dispositivo emita un sonido de voz ("Gracias").
- **Funcionamiento**: Llama a `zk_service.test_voice_on_device`.

#### `DELETE /{ip}/attendance`
- **Propósito**: Borrar **todos** los registros de asistencia almacenados en la memoria del dispositivo.
- **Funcionamiento**: Llama a `zk_service.clear_attendance_from_device`. Devuelve un código de estado `204 No Content` si tiene éxito.

#### `POST /{ip}/sync-users`
- **Propósito**: Sincronizar una lista de usuarios desde el sistema Laravel hacia el dispositivo.
- **Funcionamiento**: Este es un endpoint crítico y destructivo. Recibe una lista de usuarios en el cuerpo de la petición y llama a `zk_service.sync_users_to_device`. El servicio primero **borra todos los usuarios existentes** en el dispositivo y luego sube la nueva lista.

---

## Módulo: `routers/health.py`

Expone un endpoint simple para verificar que el servicio está en línea.

### Endpoint Definido:

#### `GET /health`
- **Propósito**: Realizar una verificación de estado (Health Check).
- **Funcionamiento**: No tiene lógica compleja. Simplemente devuelve una respuesta JSON `{"status": "ok"}`. Es útil para sistemas de monitoreo o balanceadores de carga para confirmar que la aplicación está respondiendo.

---

## Módulo: `routers/ws.py`

Define un endpoint de WebSocket para comunicación en tiempo real.

### Endpoint Definido:

#### `WEBSOCKET /ws/{client_id}`
- **Propósito**: Establecer una conexión persistente de WebSocket con el servidor.
- **Funcionamiento**:
    1.  Utiliza una clase `ConnectionManager` para gestionar las conexiones activas.
    2.  Cuando un cliente se conecta, se añade a la lista de conexiones activas.
    3.  Actualmente, tiene una lógica de "eco": cualquier mensaje que el cliente envíe, el servidor se lo devuelve con el prefijo "You wrote:".
    4.  Si el cliente se desconecta, se elimina de la lista de conexiones.
- **Potencial a Futuro**: Este endpoint es la base para implementar notificaciones en tiempo real. Por ejemplo, el sistema podría enviar un mensaje a través de este WebSocket al frontend de Laravel cada vez que el estado de un dispositivo cambie de "online" a "offline".

```

<!--
[PROMPT_SUGGESTION]Ahora documenta el archivo `services/zk_service.py` que es el núcleo de la comunicación con los dispositivos.[/PROMPT_SUGGESTION]
[PROMPT_SUGGESTION]Crea la documentación para el archivo principal `main.py` de la API de FastAPI.[/PROMPT_SUGGESTION]

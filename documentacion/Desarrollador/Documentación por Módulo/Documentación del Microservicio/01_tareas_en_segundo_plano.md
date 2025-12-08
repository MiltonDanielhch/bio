# Documentación del Microservicio: Tareas en Segundo Plano

## Módulo: `background/tasks.py`

### 1. Propósito del Módulo

Este módulo es responsable de una de las características más importantes del microservicio `zkService`: el **monitoreo continuo y asíncrono del estado de los dispositivos biométricos**.

Su objetivo es verificar periódicamente si los dispositivos ZKTeco configurados están en línea y son accesibles, manteniendo un registro en memoria de su estado para que pueda ser consultado instantáneamente a través de un endpoint de la API, sin necesidad de establecer una nueva conexión al dispositivo en cada consulta.

### 2. Componentes Clave

#### `device_status = {}`
- **Descripción**: Un diccionario de Python que actúa como un **caché en memoria**.
- **Estructura**:
    - **Clave**: La dirección IP del dispositivo (ej. `'192.168.1.201'`).
    - **Valor**: Otro diccionario que contiene el estado actual del dispositivo (`status`, `info`, `error`, `last_checked`).

#### `async def check_device(ip: str)`
- **Propósito**: Es una corutina diseñada para verificar el estado de **un único dispositivo**.
- **Lógica de Funcionamiento**:
    1.  Intenta establecer una conexión con el dispositivo usando el gestor de contexto `DeviceConnection`.
    2.  **Si tiene éxito**: Obtiene la información del dispositivo (`get_device_info()`) y actualiza el diccionario `device_status` con el estado `"online"`, los datos del dispositivo y la hora de la verificación.
    3.  **Si falla (por conexión o error del dispositivo)**: Captura la excepción (`HTTPException` u otra) y actualiza `device_status` con el estado `"offline"`, el mensaje de error y la hora de la verificación.

#### `async def monitor_devices()`
- **Propósito**: Orquesta el proceso de monitoreo de **todos los dispositivos** de forma concurrente.
- **Lógica de Funcionamiento**:
    1.  Es una corutina que se ejecuta en un **bucle infinito** (`while True`).
    2.  En cada iteración, lee la lista de IPs del archivo de configuración (`settings.KNOWN_DEVICES`).
    3.  Utiliza `asyncio.gather()` para ejecutar la corutina `check_device()` para **todas las IPs en paralelo**. Esto es extremadamente eficiente, ya que no espera a que un dispositivo lento o desconectado responda para empezar a verificar el siguiente.
    4.  Una vez que todas las verificaciones han terminado, hace una pausa (`asyncio.sleep()`) durante el intervalo definido en `settings.DEVICE_CHECK_INTERVAL` antes de comenzar el siguiente ciclo de monitoreo.

#### `async def start_background_tasks()`
- **Propósito**: Es la función de arranque que inicia todo el proceso de monitoreo.
- **Lógica de Funcionamiento**:
    - Se llama una sola vez cuando la aplicación FastAPI se inicia.
    - Utiliza `asyncio.create_task(monitor_devices())` para registrar la corutina `monitor_devices` como una tarea en segundo plano en el bucle de eventos de `asyncio`. Esto permite que el monitoreo se ejecute de forma independiente sin bloquear el servidor web principal de FastAPI.

### 3. Flujo de Ejecución

1.  La aplicación FastAPI arranca.
2.  Se llama a `start_background_tasks()`.
3.  Se crea una tarea en segundo plano para `monitor_devices()`, que comienza a ejecutarse inmediatamente.
4.  `monitor_devices()` entra en su bucle infinito.
5.  En cada ciclo, lanza N tareas `check_device()` (una por cada IP) que se ejecutan concurrentemente.
6.  Cada tarea `check_device()` actualiza la entrada correspondiente en el diccionario global `device_status`.
7.  Mientras todo esto sucede, la API de FastAPI sigue disponible para recibir peticiones. Cuando llega una solicitud al endpoint `/devices/status`, simplemente lee y devuelve el contenido actual del diccionario `device_status`, proporcionando una respuesta instantánea.

Este diseño es robusto, eficiente y escalable, permitiendo monitorear una gran cantidad de dispositivos con un impacto mínimo en el rendimiento del servicio principal.


# Documentación para Desarrolladores: GobeBio

## 1. Arquitectura General y Visión del Sistema

### 1.1. Propósito

GobeBio es un sistema híbrido diseñado para la gestión centralizada de la asistencia de personal mediante dispositivos biométricos ZKTeco. El objetivo es proporcionar una plataforma robusta, escalable y desacoplada para manejar la comunicación con hardware externo, el procesamiento de datos y la generación de reportes.

### 1.2. Arquitectura Híbrida: Monolito Modular + Microservicio

El sistema se compone de dos componentes principales que trabajan en conjunto:

1.  **Aplicación Principal (Monolito Modular - Laravel):**
    *   **Responsabilidad:** Gestiona toda la lógica de negocio, la interfaz de usuario (Panel de Administración con Voyager), la autenticación, la gestión de empleados, horarios, reportes y la persistencia de datos en la base de datos principal (MySQL).
    *   **Justificación:** Laravel provee un ecosistema maduro (`Eloquent`, `Queues`, `Scheduler`) que acelera el desarrollo de las funcionalidades de negocio estándar. La estructura modular permite organizar el código de forma lógica y mantenible.

2.  **Microservicio Satélite (`zk_service` - Python/FastAPI):**
    *   **Responsabilidad:** Actúa como un **adaptador de hardware**. Su única función es exponer una API REST simple que traduce peticiones HTTP en comandos TCP/IP específicos para los dispositivos ZKTeco, utilizando la librería `pyzk`.
    *   **Justificación:** Aísla la complejidad y las dependencias de la comunicación con el hardware. Si en el futuro se necesita integrar dispositivos de otra marca, solo se modificaría o reemplazaría este microservicio, sin afectar la aplicación principal de Laravel.

### 1.3. Flujo de Comunicación y Ejecución Asíncrona

La comunicación entre el backend y los dispositivos **no es directa**. Se realiza de forma asíncrona para evitar que la interfaz de usuario se bloquee mientras espera una respuesta de un dispositivo físico (operación de I/O bloqueante).

1.  **Petición del Usuario:** Un administrador presiona un botón en el panel (ej. "Descargar Asistencias").
2.  **Despacho del Job:** El controlador de Laravel no intenta conectarse al dispositivo. En su lugar, despacha un `Job` (ej. `SyncAttendanceJob`) a la cola (Queue).
3.  **Procesamiento en Segundo Plano:** Un proceso `worker` de Laravel, que corre en un contenedor separado, toma el `Job` de la cola.
4.  **Llamada al Microservicio:** El `Job` realiza una petición HTTP (usando el cliente `Http` de Laravel) al endpoint correspondiente del microservicio FastAPI (ej. `GET /devices/{ip}/attendance`).
5.  **Comunicación con Hardware:** El microservicio FastAPI utiliza la librería `pyzk` para conectarse al dispositivo ZKTeco vía TCP/IP, ejecutar el comando y obtener los datos.
6.  **Respuesta y Persistencia:** El microservicio devuelve los datos crudos (ej. un JSON con las marcaciones) al `Job` de Laravel. El `Job` procesa estos datos y los guarda en la base de datos MySQL.

*(Diagrama de flujo conceptual)*
`[Usuario] -> [Panel Laravel] -> [Controller] --(Dispatch Job)--> [Cola (Redis/DB)]`
`[Worker] --(Consume Job)--> [Job Logic] --(HTTP API Call)--> [Microservicio Python] --(TCP/IP)--> [Reloj ZKTeco]`

---

## 2. Stack Tecnológico y Entorno de Desarrollo

### 2.1. Tecnologías Principales

*   **Backend:** Laravel 10/11 (PHP 8.2)
*   **Panel de Administración:** TCG/Voyager
*   **Microservicio:** Python 3.x con FastAPI
*   **Comunicación Hardware:** `pyzk` (librería Python)
*   **Base de Datos:** MySQL 8.0
*   **Colas (Queues):** Driver `database` por defecto, con `Redis` disponible y configurado.
*   **Contenerización:** Docker y Docker Compose.

### 2.2. Estructura de Contenedores (`docker-compose.yml`)

El entorno de desarrollo y producción está completamente contenerizado para garantizar la portabilidad y consistencia.

*   `backend`
    *   **Descripción:** Contenedor principal que ejecuta la aplicación Laravel con el servidor `Unit`.
    *   **Puerto expuesto:** `8000`.

*   `worker`
    *   **Descripción:** Contenedor dedicado a procesar las colas de Laravel (`php artisan queue:work`). Es fundamental para todas las operaciones asíncronas.

*   `zkservice`
    *   **Descripción:** Ejecuta el microservicio FastAPI con `uvicorn`. No expone puertos al exterior, solo es accesible desde la red interna de Docker (`gobebio-net`), principalmente por el `worker`.

*   `mysql`
    *   **Descripción:** Contenedor de la base de datos MySQL 8.0.
    *   **Puerto expuesto:** `3307` (mapeado al `3306` interno) para evitar conflictos con instalaciones locales de MySQL.

*   `redis`
    *   **Descripción:** Contenedor de Redis, disponible para ser usado como driver de caché o de colas para un mayor rendimiento.

### 2.3. Puesta en Marcha del Entorno

1.  Asegúrese de tener Docker y Docker Compose instalados.
2.  Cree un archivo `.env` en la raíz del proyecto a partir de `.env.example` y configure las variables de base de datos.
3.  Ejecute el comando `docker-compose up -d --build` desde la raíz del proyecto.
4.  Acceda al contenedor del backend para ejecutar las migraciones:
    ```bash
    docker-compose exec backend php artisan migrate --seed
    ```
5.  La aplicación estará disponible en `http://localhost:8000`.

---

Esta sección proporciona la base para entender cómo funciona y cómo se estructura el sistema. Las siguientes secciones detallarán los flujos de datos y los componentes de código más importantes.

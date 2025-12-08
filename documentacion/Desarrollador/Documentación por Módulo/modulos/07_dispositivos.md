# Módulo 07: Gestión de Dispositivos (`Dispositivos`)

## 1. Propósito del Módulo

El módulo de **Dispositivos** es el componente central que gestiona la interacción entre el sistema GobeBio y el hardware de control de asistencia (lectores de huella, faciales, etc., de marca ZKTeco).

Sus responsabilidades principales son:
- **Administrar el inventario de dispositivos**: Registrar, configurar (IP, puerto) y organizar los dispositivos por sucursal.
- **Gestionar la comunicación**: Probar la conexión y despachar trabajos en segundo plano para sincronizar datos.
- **Asignar personal**: Vincular qué empleados deben estar registrados en cada dispositivo.

Este módulo es el corazón de la recolección de datos biométricos y de asistencia.

## 2. Componentes del Módulo

### 2.1. Modelo (`App\Models\Dispositivo.php`)

Representa la tabla `dispositivos` y define las relaciones y lógicas de acceso a los datos del dispositivo.

- **Atributos `fillable`**: Incluye campos de configuración como `sucursal_id`, `nombre_dispositivo`, `direccion_ip`, `puerto`, `password`, y campos de estado como `ultima_conexion` y `version_firmware`.
- **Traits**: `HasFactory`, `SoftDeletes`.
- **Casts**: `ultima_conexion` a `datetime`, y campos numéricos a `integer`.
- **Relaciones**:
    - `sucursal()`: `belongsTo(Sucursal)`.
    - `creador()`: `belongsTo(User)`.
    - `empleados()`: Una relación `belongsToMany` con `Empleado` a través de la tabla `dispositivo_empleado`. Es crucial el uso de `withPivot('zk_user_id', 'privilegio')` para poder acceder al ID de usuario específico del dispositivo y su nivel de permiso.
    - `asistencias()`: `hasMany(RegistroAsistencia)`. Permite acceder a todos los registros de asistencia que se originaron en este dispositivo.
- **Helper**:
    - `endpointZk()`: Un método de conveniencia que construye el string de conexión `tcp://...` usado por el servicio de ZKTeco.

### 2.2. Controlador (`App\Http\Controllers\Admin\DispositivoController.php`)

Este controlador es más complejo que los CRUD anteriores, ya que orquesta no solo la gestión de datos, sino también la interacción con los dispositivos físicos a través de Jobs y Servicios.

- **Trait `ManagesCrud`**: Hereda la funcionalidad de listado AJAX.
- **Métodos CRUD Estándar**: `show`, `create`, `store`, `edit`, `update`, `destroy` siguen el patrón establecido.
- **Métodos de Interacción con Dispositivos**:
    - `testConnection(Dispositivo $dispositivo, ZkService $zkService)`: Inyecta el `ZkService` para intentar obtener la información del dispositivo físico. Proporciona feedback inmediato al usuario sobre el estado de la conexión.
    - `syncNow(Dispositivo $dispositivo)`: Despacha el `SyncAttendanceJob` a la cola de trabajos de Laravel. Esto permite que la sincronización de asistencias (una tarea potencialmente larga) se ejecute en segundo plano sin bloquear la interfaz de usuario.
    - `syncUsers(Dispositivo $dispositivo)`: Despacha el `SyncUsersToDeviceJob`, que se encarga de enviar la lista de empleados asignados desde la base de datos del sistema hacia el dispositivo físico.
- **Métodos de Asignación de Empleados**:
    - `assignEmployees(Dispositivo $dispositivo)`: Muestra una vista dedicada para gestionar qué empleados están asignados a un dispositivo.
    - `storeEmployees(Request $request, Dispositivo $dispositivo)`: Contiene una lógica de negocio importante. Al guardar las asignaciones, no se limita a un simple `sync()`. Itera sobre los empleados seleccionados y:
        - Si un empleado ya estaba asignado, **mantiene su `zk_user_id` existente** para no romper la consistencia con el dispositivo.
        - Si un empleado es nuevo, le asigna un **nuevo `zk_user_id` incremental**, basándose en el valor máximo actual para ese dispositivo.
        - Marca las nuevas asignaciones con `estado_sincronizacion = 'pendiente'` para que un proceso posterior las envíe al dispositivo.

### 2.3. Requests de Formulario

#### `App\Http\Requests\StoreDispositivoRequest.php` y `UpdateDispositivoRequest.php`
- **`authorize()`**: Actualmente devuelven `true`, con un comentario indicando que la autorización debe implementarse a través de una Policy.
- **`rules()`**: Definen reglas de validación claras, asegurando que `numero_serie` y `direccion_ip` sean únicos para evitar conflictos.

### 2.4. Política de Acceso (`App\Policies\DispositivoPolicy.php`)

Estructura de Policy estándar que verifica los permisos del usuario (`browse_dispositivos`, `read_dispositivos`, etc.) para autorizar las acciones del CRUD.

### 2.5. Migración y Seeder

- **Migración**: Define la tabla `dispositivos` con campos para la configuración de red, tipo, estado y metadatos de sincronización (`ultima_conexion`, `ultimo_user_id`).
- **Seeder**: Utiliza `firstOrCreate` para poblar la base de datos con dispositivos iniciales, incluyendo uno de desarrollo con una IP local.

### 2.6. Vistas (Blade)

- **`browse.blade.php` y `list.blade.php`**: La vista de listado es el centro de operaciones de este módulo. Además de las acciones CRUD, incluye botones específicos para:
    - Asignar Empleados.
    - Sincronizar Asistencias (Descargar datos del dispositivo).
    - Sincronizar Usuarios (Subir datos al dispositivo).
- **`edit-add.blade.php`**: Formulario estándar para la configuración del dispositivo.
- **`read.blade.php`**: Una vista de detalle muy completa que no solo muestra los datos del dispositivo, sino que también ofrece botones para probar la conexión y ejecutar sincronizaciones. Además, incluye una tabla con los **últimos 10 registros de asistencia** de ese dispositivo, ofreciendo un feedback visual muy útil.
- **`assign_employees.blade.php`**: Vista dedicada con un selector múltiple (`select2`) para facilitar la asignación masiva de empleados a un dispositivo.

## 3. Flujo de Datos y Procesos Asíncronos

1.  **Asignación de Empleados**: El administrador selecciona empleados en la vista `assign_employees`. El método `storeEmployees` calcula los nuevos `zk_user_id` y actualiza la tabla pivote `dispositivo_empleado`, marcando los nuevos como `pendiente`.
2.  **Sincronización de Usuarios (Sistema -> Dispositivo)**: Al pulsar "Sincronizar Usuarios", se encola el `SyncUsersToDeviceJob`. Este job, ejecutado por un *worker*, leerá los empleados con estado `pendiente` para ese dispositivo y utilizará el `ZkService` para enviarlos al hardware.
3.  **Sincronización de Asistencias (Dispositivo -> Sistema)**: Al pulsar "Sincronizar Asistencias", se encola el `SyncAttendanceJob`. El *worker* se conectará al dispositivo, descargará los nuevos registros de marcación y los guardará en la tabla `registros_asistencia`.

## 4. Buenas Prácticas y Puntos de Mejora

### Buenas Prácticas Implementadas

- **Procesamiento Asíncrono**: El uso de Jobs (`SyncUsersToDeviceJob`, `SyncAttendanceJob`) para las tareas de sincronización es una práctica excelente. Evita que la interfaz de usuario se congele durante operaciones de red que pueden ser lentas y propensas a fallos.
- **Inyección de Dependencias**: El controlador inyecta el `ZkService` en el método `testConnection`, lo que facilita las pruebas y la inversión de control.
- **Lógica de Negocio en el Controlador**: La lógica para asignar incrementalmente los `zk_user_id` en `storeEmployees` está bien implementada y es crucial para el funcionamiento del sistema.
- **Feedback al Usuario**: Las vistas `read` y `list` proporcionan botones y datos (como las últimas asistencias) que dan al usuario una visión clara del estado y las operaciones disponibles para cada dispositivo.

### Puntos de Mejora Potenciales

1.  **Autorización en Form Requests**: Los `FormRequest` (`StoreDispositivoRequest`, `UpdateDispositivoRequest`) tienen la autorización comentada.
    **Sugerencia**: Mover la lógica de autorización del controlador a los métodos `authorize()` de los `FormRequest` para una mejor separación de responsabilidades, como se ha sugerido en módulos anteriores.
    ```php
    // En UpdateDispositivoRequest.php
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('dispositivo'));
    }
    ```

2.  **Refactorización de Consultas**: La consulta para obtener las sucursales activas se repite en los métodos `create()` y `edit()`.
    **Sugerencia**: Se puede refactorizar a un método privado `getFormData()` para evitar la duplicación.

3.  **Manejo de Estado de Sincronización**: El método `storeEmployees` establece `estado_sincronizacion` a `'pendiente'`, pero este campo no parece estar en la migración de la tabla `dispositivo_empleado`.
    **Sugerencia**: Es necesario añadir la columna `estado_sincronizacion` a la tabla `dispositivo_empleado` para que la lógica de sincronización de usuarios funcione correctamente.

```

He finalizado la documentación para el módulo de Dispositivos. Este es un módulo muy completo que combina un CRUD tradicional con lógica de negocio compleja y procesos en segundo plano.

<!--
[PROMPT_SUGGESTION]Ahora documenta el Job `SyncUsersToDeviceJob`.
[/PROMPT_SUGGESTION]
[PROMPT_SUGGESTION]Crea la documentación para el `ZkService` que interactúa con los dispositivos.[/PROMPT_SUGGESTION]

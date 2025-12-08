# 5. Rutas y Controladores Principales

El archivo `routes/web.php` es el mapa de todas las URLs accesibles a través del navegador en la aplicación GobeBio. Define los endpoints, los asocia a métodos de controladores específicos y aplica los middlewares de seguridad necesarios.

## 5.1. Estructura General

Casi todas las rutas del sistema están agrupadas bajo el prefijo `/admin` y protegidas por un grupo de middlewares:

```php
Route::prefix('admin')->middleware(['loggin', 'system'])->group(function () {
    // ... Todas las rutas de la aplicación
});
```

*   **`prefix('admin')`**: Asegura que todas las URLs comiencen con `/admin`, estandarizando el acceso al panel.
*   **`middleware(['loggin', 'system'])`**: Aplica middlewares personalizados. `loggin` probablemente verifica la autenticación del usuario, mientras que `system` podría cargar configuraciones globales o realizar otras validaciones a nivel de sistema.

## 5.2. Patrones de Rutas Comunes

Se observa un patrón consistente para la mayoría de los módulos CRUD, lo que facilita la mantenibilidad.

### Rutas de Recursos (CRUD)

La mayoría de las entidades del sistema utilizan `Route::resource`. Esto crea automáticamente las rutas estándar para las operaciones CRUD (Create, Read, Update, Delete).

```php
// Ejemplo para Horarios
Route::resource('horarios', HorarioController::class)->names('admin.horarios');
```

### Listado Asíncrono (AJAX)

Cada `Route::resource` está acompañado por una ruta `.../ajax/list`.

```php
// Ejemplo para Horarios
Route::get('horarios/ajax/list', [HorarioController::class, 'list'])->name('admin.horarios.ajax.list');
```

Esta ruta es fundamental para la interactividad del frontend. Las tablas de datos (listados) utilizan peticiones AJAX a este endpoint para obtener la lista de registros de forma paginada y filtrada, sin necesidad de recargar la página completa. El controlador devuelve una vista parcial que se inyecta en la tabla.

## 5.3. Rutas Críticas y Especializadas

Además de los CRUDs estándar, existen rutas clave para las funcionalidades más complejas del sistema.

### Gestión de Dispositivos

El `DispositivoController` concentra la lógica de interacción con los dispositivos biométricos. Estas rutas son las que inician los flujos asíncronos de sincronización.

*   `POST dispositivos/{dispositivo}/test-connection`: Endpoint para verificar la conectividad con el microservicio y, por extensión, con el dispositivo físico.
*   `POST dispositivos/{dispositivo}/sync-now`: **(Descargar Asistencias)**. Dispara el `SyncAttendanceJob` para descargar las marcaciones desde un dispositivo específico.
*   `POST dispositivos/{dispositivo}/sync-users`: **(Sincronizar Empleados)**. Dispara el `SyncUsersToDeviceJob` para enviar la lista de empleados asignados hacia el dispositivo.
*   `GET dispositivos/{dispositivo}/assign-employees`: Muestra la interfaz para asignar o desasignar empleados a un dispositivo.
*   `POST dispositivos/{dispositivo}/assign-employees`: Procesa el formulario de asignación de empleados.

### Reportes de Asistencia

*   `POST reportes-asistencia`: Inicia la generación de un nuevo reporte, probablemente despachando un Job para procesarlo en segundo plano.
*   `GET reportes-asistencia/{reporte}/download`: Permite descargar el archivo (PDF, Excel) de un reporte que ya ha sido completado.

## 5.4. Controladores Destacados

*   **`Admin\DispositivoController`**: Orquesta toda la comunicación con los dispositivos. No se comunica directamente con el hardware, sino que despacha Jobs a la cola.
*   **`Admin\DispositivoEmpleadoController`**: Gestiona la tabla pivote `dispositivo_empleado`, que es la "Piedra Rosetta" para mapear usuarios del sistema con usuarios en los dispositivos.
*   **`HorarioController`, `EmpleadoController`, `SucursalController`, etc.**: Son los controladores de recursos estándar. Siguen un patrón similar, probablemente utilizando un Trait (`ManagesCrud`) para reutilizar la lógica de los listados AJAX.
*   **`ReporteAsistenciaController`**: Maneja la lógica de creación y descarga de reportes, interactuando con el sistema de colas y el almacenamiento de archivos.
*   **`Admin\DashboardController`**: Controlador personalizado que reemplaza al dashboard por defecto de Voyager, permitiendo mostrar información más relevante para GobeBio.

## 5.5. Rutas de Utilidad

*   `GET /clear-cache`: Un endpoint práctico para que los administradores puedan limpiar la caché de la aplicación directamente desde el panel, sin necesidad de acceso a la terminal.

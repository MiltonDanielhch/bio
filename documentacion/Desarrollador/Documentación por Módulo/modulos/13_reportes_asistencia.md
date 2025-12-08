# Módulo 13: Reportes de Asistencia (`ReporteAsistencia`)

## 1. Propósito del Módulo

El módulo de **Reportes de Asistencia** es una herramienta administrativa diseñada para consolidar y exportar la información de asistencia de los empleados. Su función principal es permitir a los administradores generar, de forma asíncrona, archivos de reporte (por ejemplo, en formato Excel) que contienen el detalle de las horas trabajadas, tardanzas, faltas y otras incidencias de un grupo de empleados dentro de un rango de fechas específico.

La generación de reportes se ejecuta como un trabajo en segundo plano (`Job`) para no bloquear la interfaz del usuario, permitiendo procesar grandes volúmenes de datos sin afectar la experiencia de uso del sistema.

## 2. Componentes del Módulo

### 2.1. Modelo (`App\Models\ReporteAsistencia.php`)

Representa un registro de un reporte generado en la tabla `reportes_asistencia`.

- **Atributos `fillable`**: `nombre_reporte`, `fecha_inicio`, `fecha_fin`, `generado_por_id`, `estado`, `ruta_archivo`.
- **Casts**: `fecha_inicio` y `fecha_fin` a `date`.
- **Relaciones**:
    - `generador()`: `belongsTo(User, 'generado_por_id')`. Relación con el usuario que solicitó la generación del reporte.

### 2.2. Controlador (`App\Http\Controllers\ReporteAsistenciaController.php`)

Gestiona las solicitudes del usuario para generar, listar y descargar reportes.

- **Trait `ManagesCrud`**: Hereda la funcionalidad para el listado AJAX.
- **Métodos Principales**:
    - `applySearch(...)`: Permite buscar reportes por su nombre.
    - `store()`: No crea el reporte directamente. Valida los parámetros (rango de fechas, empleados, etc.) y despacha un `Job` (ej. `GenerarReporteAsistenciaJob`) para que se encargue del procesamiento en segundo plano. Crea una entrada inicial en la tabla `reportes_asistencia` con estado "procesando".
    - `show()`: Muestra los detalles de un reporte, como el estado actual, el período y quién lo generó.
    - `download()`: Verifica si el reporte tiene estado "completado" y si el archivo existe. Si es así, proporciona la descarga del archivo al usuario.
    - `destroy()`: Elimina el registro del reporte de la base de datos y también el archivo físico asociado del almacenamiento.

### 2.3. Job (`App\Jobs\GenerarReporteAsistenciaJob.php`)

Es el núcleo funcional del módulo. Se encarga de la lógica pesada de la generación del reporte.

- **Constructor**: Recibe el ID del reporte y los parámetros necesarios (fechas, empleados).
- **`handle()`**:
    1.  Actualiza el estado del reporte a "procesando".
    2.  Realiza las consultas a la base de datos para obtener todos los registros de asistencia e incidencias necesarios.
    3.  Procesa los datos para calcular totales, tardanzas, etc.
    4.  Utiliza una librería como `Laravel Excel` para crear el archivo (ej. `.xlsx`).
    5.  Guarda el archivo en el disco de almacenamiento (`storage`).
    6.  Actualiza el registro del reporte en la base de datos con el estado "completado" y la `ruta_archivo`.
    7.  En caso de error, captura la excepción, actualiza el estado a "error" y registra el fallo.

### 2.4. Política de Acceso (`App\Policies\ReporteAsistenciaPolicy.php`)

Controla quién puede generar, ver, descargar o eliminar reportes, basado en permisos como `browse_reportes_asistencia`, `add_reportes_asistencia`, etc.

### 2.5. Migración

- Define la estructura de la tabla `reportes_asistencia` con campos como `nombre_reporte`, `fecha_inicio`, `fecha_fin`, `estado` (enum: 'procesando', 'completado', 'error'), `ruta_archivo` y la clave foránea `generado_por_id`.

### 2.6. Vistas (Blade)

- **`browse.blade.php`**: Contiene el formulario para solicitar un nuevo reporte (con selectores de fecha y empleados) y el listado AJAX de los reportes ya generados.
- **`list.blade.php`**: Renderiza la tabla de reportes. Muestra el estado de cada uno con etiquetas de colores para una fácil identificación visual. El botón "Descargar" solo se muestra si el estado es "completado".
- **`read.blade.php`**: Muestra una vista detallada del reporte, incluyendo el progreso o el resultado final.

## 3. Flujo de Datos

1.  El usuario completa el formulario en la vista `browse` y lo envía a `store()`.
2.  El controlador valida los datos, crea un registro de reporte con estado "pendiente" o "procesando" y despacha el `GenerarReporteAsistenciaJob` a la cola de trabajos.
3.  El sistema de colas de Laravel ejecuta el Job en segundo plano.
4.  Mientras tanto, la vista de listado puede actualizarse (manual o automáticamente) para mostrar el estado "procesando".
5.  Una vez que el Job finaliza, actualiza el estado del reporte a "completado" o "error".
6.  Cuando el estado es "completado", el botón de descarga se vuelve visible en la lista, permitiendo al usuario obtener el archivo.

## 4. Buenas Prácticas y Puntos Clave

- **Procesamiento Asíncrono**: El uso de Jobs y colas es fundamental para no degradar la experiencia del usuario. La generación de reportes puede ser un proceso muy largo y no debe ejecutarse en el mismo hilo que la petición HTTP.
- **Gestión de Estados**: El campo `estado` es crucial para informar al usuario sobre el progreso del reporte y para controlar las acciones disponibles (como la descarga).
- **Seguridad en Descargas**: El método `download()` debe validar que el usuario tiene permisos y que la ruta del archivo es segura para evitar vulnerabilidades de tipo "Path Traversal".
- **Limpieza de Archivos**: El método `destroy()` debe encargarse de eliminar tanto el registro en la base de datos como el archivo físico para no dejar archivos huérfanos en el servidor.
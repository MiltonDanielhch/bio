# Módulo 12: Gestión de Incidencias (`Incidencia`)

## 1. Propósito del Módulo

El módulo de **Gestión de Incidencias** permite a los administradores o supervisores registrar formalmente eventos que afectan la jornada laboral de un empleado, como faltas, tardanzas, permisos o vacaciones.

Este módulo vincula a un **Empleado** con un **Tipo de Incidencia** en una fecha específica, proporcionando un registro auditable que puede ser utilizado para justificar ausencias, procesar descuentos o simplemente llevar un control del historial del personal.

## 2. Componentes del Módulo

### 2.1. Modelo (`App\Models\Incidencia.php`)

Representa un registro en la tabla `incidencias`.

- **Atributos `fillable`**: `empleado_id`, `tipo_incidencia_id`, `fecha_incidencia`, `motivo`, `documento_adjunto`, `estado`, `creado_por`.
- **Casts**: `fecha_incidencia` a `date`.
- **Relaciones**:
    - `empleado()`: `belongsTo(Empleado)`.
    - `tipoIncidencia()`: `belongsTo(TipoIncidencia)`.
    - `creador()`: `belongsTo(User)`.

### 2.2. Controlador (`App\Http\Controllers\IncidenciaController.php`)

Orquesta la lógica del CRUD para las incidencias.

- **Trait `ManagesCrud`**: Hereda la funcionalidad estándar para el listado AJAX.
- **Propiedades**: Define las vistas, el modelo y las relaciones a cargar con Eager Loading (`$with = ['empleado', 'tipoIncidencia']`) para optimizar el rendimiento del listado.
- **Métodos Principales**:
    - `applySearch(...)`: Implementa una búsqueda relacional. Utiliza `whereHas('empleado', ...)` para permitir buscar incidencias por el nombre, apellido o código del empleado, además de por el motivo de la incidencia.
    - `create()` y `edit()`: Preparan los datos necesarios para los formularios, cargando las listas de empleados y tipos de incidencia para los menús desplegables.
    - `store()` y `update()`: Utilizan los `FormRequest` correspondientes para validar los datos antes de persistirlos. Incluyen lógica para manejar la subida de un archivo adjunto (ej. un certificado médico).
    - `destroy()`: Realiza el borrado del registro.

### 2.3. Requests de Formulario

Centralizan la validación y autorización.

#### `App\Http\Requests\StoreIncidenciaRequest.php` y `UpdateIncidenciaRequest.php`
- **`authorize()`**: Delegan la comprobación de permisos a la `IncidenciaPolicy`.
- **`rules()`**: Definen las reglas de validación para los campos del formulario, como la obligatoriedad del empleado, el tipo de incidencia y la fecha. También validan el archivo adjunto si se proporciona.

### 2.4. Política de Acceso (`App\Policies\IncidenciaPolicy.php`)

Define los permisos para cada acción del CRUD (`browse_incidencias`, `add_incidencias`, etc.), asegurando que solo los usuarios autorizados puedan gestionar las incidencias.

### 2.5. Migración y Seeder

- **Migración**: Define la estructura de la tabla `incidencias`.
    - **Claves Foráneas**: `empleado_id` y `tipo_incidencia_id` con borrado en cascada (`cascadeOnDelete`).
    - **Campos**: Incluye un campo `documento_adjunto` (nulable) para almacenar la ruta a un archivo de respaldo y un campo `estado` (ej. 'pendiente', 'aprobado', 'rechazado').
- **Seeder**: Genera datos de prueba, creando algunas incidencias de ejemplo para diferentes empleados.

### 2.6. Vistas (Blade)

- **`browse.blade.php`**: La vista principal del listado. Contiene el campo de búsqueda y el contenedor `#list-container` para la carga de la tabla vía AJAX.
- **`list.blade.php`**: Plantilla parcial que renderiza la tabla de resultados. Muestra la información clave de cada incidencia: empleado, tipo, fecha y estado, junto con los botones de acción.
- **`edit-add.blade.php`**: Formulario unificado para registrar y editar incidencias. Utiliza selectores `select2` para una selección amigable de empleados y tipos de incidencia, y un campo de tipo `file` para el documento adjunto.
- **`read.blade.php`**: Vista de solo lectura que presenta de forma clara todos los detalles de una incidencia, incluyendo un enlace para descargar el documento adjunto si existe.

## 3. Flujo de Datos

El flujo de datos sigue el patrón CRUD estándar del sistema:

1.  **Listado AJAX**: La vista `browse` carga la tabla de `list` de forma asíncrona. La búsqueda y paginación se realizan sin recargar la página.
2.  **Creación/Edición**: El controlador prepara los datos (empleados, tipos de incidencia) para el formulario `edit-add`. Al enviar, el `FormRequest` valida la entrada. El controlador procesa la subida del archivo adjunto (si existe) antes de guardar el registro.
3.  **Búsqueda Relacional**: La función `applySearch` del controlador permite una búsqueda potente que no se limita a los campos de la tabla `incidencias`, sino que se extiende a los datos de la tabla `empleados` relacionada.

## 4. Buenas Prácticas y Puntos Clave

- **Consistencia Arquitectónica**: El módulo se adhiere perfectamente a la arquitectura establecida en el resto del proyecto, lo que facilita su mantenimiento.
- **Búsqueda Relacional con `whereHas`**: La implementación de la búsqueda a través de relaciones es una práctica avanzada de Eloquent que mejora enormemente la usabilidad del listado.
- **Optimización de Consultas**: El uso de `with(['empleado', 'tipoIncidencia'])` en el controlador es crucial para evitar el problema N+1 y mantener un buen rendimiento en la vista de lista.
- **Manejo de Archivos**: La lógica para gestionar el `documento_adjunto` (guardar, actualizar, eliminar) está encapsulada en los métodos `store` y `update` del controlador.

He creado el archivo `documentacion/Desarrollador/Documentación por Módulo/modulos/12_incidencias.md` con esta información.

<!--
[PROMPT_SUGGESTION]Ahora que todos los módulos CRUD están documentados, genera un `README.md` para la carpeta de documentación con un índice de todos los módulos.[/PROMPT_SUGGESTION]
[PROMPT_SUGGESTION]Documenta el proceso de generación de reportes de asistencia.[/PROMPT_SUGGESTION]

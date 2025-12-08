# Módulo 11: Tipos de Incidencia (`TipoIncidencia`)

## 1. Propósito del Módulo

El módulo de **Tipos de Incidencia** es un CRUD de soporte fundamental para el sistema. Su propósito es gestionar el catálogo de clasificaciones que se pueden asignar a una incidencia de un empleado (por ejemplo, "Tardanza", "Falta injustificada", "Permiso con goce", etc.).

Este catálogo es esencial para estandarizar el registro de incidencias y facilitar la generación de reportes y estadísticas consistentes sobre el comportamiento y las ausencias del personal.

## 2. Componentes del Módulo

### 2.1. Modelo (`App\Models\TipoIncidencia.php`)

Representa una categoría de incidencia en la tabla `tipos_incidencia`.

- **Atributos `fillable`**: `nombre`, `descripcion`.
- **Relaciones**:
    - `incidencias()`: `hasMany(Incidencia)`. Un tipo de incidencia puede estar asociado a muchas incidencias registradas.

### 2.2. Controlador (`App\Http\Controllers\TipoIncidenciaController.php`)

Gestiona la lógica del CRUD para los tipos de incidencia. A diferencia de otros módulos más complejos, este controlador no utiliza `FormRequests` y maneja la validación directamente en los métodos `store` y `update`.

- **Trait `ManagesCrud`**: Hereda la funcionalidad estándar para el listado AJAX.
- **Propiedades**: Define las vistas (`browseView`, `listView`, `readView`), el modelo y las relaciones a cargar.
- **Métodos Principales**:
    - `applySearch(...)`: Permite buscar por `nombre` o `descripcion`.
    - `store()` y `update()`: Contienen la lógica de validación.
        - **Reglas**: `nombre` es requerido y único en la tabla `tipos_incidencia`. `descripcion` es opcional. Se utiliza `Rule::unique()->ignore()` en la actualización para permitir guardar cambios sin que el nombre del propio registro cause un conflicto de unicidad.
    - `destroy()`: Elimina un registro. Incluye un bloque `try-catch` para manejar excepciones de la base de datos, como los errores de restricción de clave foránea. Si un tipo de incidencia ya está en uso, se evita la eliminación y se muestra un mensaje de error amigable al usuario.

### 2.3. Política de Acceso (`App\Policies\TipoIncidenciaPolicy.php`)

Define los permisos necesarios para cada acción del CRUD, asegurando que solo los usuarios con los roles adecuados puedan gestionar el catálogo.

- `viewAny`: `browse_tipos_incidencia`
- `view`: `read_tipos_incidencia`
- `create`: `add_tipos_incidencia`
- `update`: `edit_tipos_incidencia`
- `delete`: `delete_tipos_incidencia`

### 2.4. Migración y Seeder

- **Migración**: Define la estructura de la tabla `tipos_incidencia` con los campos `id`, `nombre` (único), `descripcion` (texto, nulable) y `timestamps`.
- **Seeder (`TiposIncidenciaSeeder.php`)**: Es crucial para la configuración inicial del sistema. Puebla la tabla con un conjunto predefinido y estandarizado de tipos de incidencia, como "Tardanza", "Falta justificada", "Vacaciones", etc. Esto asegura que el sistema sea funcional desde el primer momento.

### 2.5. Vistas (Blade)

Las vistas siguen el patrón de los otros módulos del sistema, con carga de datos asíncrona.

- **`browse.blade.php`**: La vista principal. Contiene el campo de búsqueda, el botón "Agregar Nuevo" y el contenedor `#list-container` donde se inyecta la tabla de resultados. El JavaScript maneja las llamadas AJAX para la búsqueda, paginación y la apertura del modal de eliminación.
- **`list.blade.php`**: Plantilla parcial que renderiza la tabla de resultados (`@forelse`). Muestra los datos de cada tipo de incidencia y los botones de acción (`Editar`, `Borrar`).
- **`edit-add.blade.php`**: Formulario unificado para crear y editar. Muestra los campos `nombre` y `descripcion` y presenta los errores de validación si los hubiera.
- **`read.blade.php`**: Vista de solo lectura que muestra todos los detalles de un tipo de incidencia, incluyendo las fechas de creación y actualización.

## 3. Flujo de Datos

El flujo es un CRUD estándar con mejoras de UX mediante AJAX:

1.  El usuario navega a la sección "Tipos de Incidencia".
2.  La función `list()` en `browse.blade.php` se ejecuta y realiza una petición AJAX a `admin.tipos-incidencia.ajax.list`.
3.  El controlador procesa la petición, obtiene los datos paginados y devuelve la vista `list.blade.php` renderizada.
4.  El JavaScript en `browse.blade.php` recibe el HTML y lo inyecta en `#list-container`.
5.  La búsqueda y la paginación repiten los pasos 2-4 con los parámetros correspondientes.
6.  Al crear o editar, se envía el formulario de manera síncrona. Si la validación en el controlador falla, Laravel redirige al usuario de vuelta con los errores. Si tiene éxito, guarda los datos y redirige al listado con un mensaje de éxito.

## 4. Buenas Prácticas y Puntos Clave

- **Manejo de Errores en Eliminación**: El uso de `try-catch` en el método `destroy` es una excelente práctica para proporcionar feedback claro al usuario cuando no se puede eliminar un registro por estar en uso, en lugar de mostrar un error genérico del sistema.
- **Validación en el Controlador**: Para CRUDs sencillos como este, realizar la validación directamente en el controlador es una alternativa viable y más rápida que crear `FormRequests` dedicados.
- **Seeder Esencial**: El `TiposIncidenciaSeeder` es un componente clave, ya que proporciona la configuración inicial necesaria para que otros módulos, como "Gestión de Incidencias", funcionen correctamente.
# Módulo 04: Gestión de Empleados (`Empleados`)

## 1. Propósito del Módulo

El módulo de **Empleados** es una de las entidades centrales del sistema GobeBio. Su propósito es administrar el ciclo de vida completo de los empleados dentro de la organización, desde su registro hasta su desvinculación. Permite gestionar su información personal, datos de contacto, detalles contractuales, y su asignación a una empresa y un departamento.

Este módulo es fundamental, ya que el empleado es la entidad sobre la cual se registran las asistencias, se asocian datos biométricos (huellas, rostros) y se gestionan horarios e incidencias.

## 2. Componentes del Módulo

### 2.1. Modelo (`App\Models\Empleado.php`)

Representa la tabla `empleados` y es un modelo con una gran cantidad de relaciones, lo que refleja su importancia central.

- **Atributos `fillable`**: Contiene una lista extensa de campos que cubren datos personales, contractuales y de sistema (`user_id`, `creado_por`).
- **Casts**: Asegura que los campos de fecha (`fecha_nacimiento`, `fecha_contratacion`) sean tratados como objetos `Carbon`.
- **Traits**: `HasFactory`, `SoftDeletes`.
- **Accessor**:
    - `getFullNameAttribute()`: Un accesor muy útil que concatena `nombres` y `apellidos` para devolver el nombre completo, simplificando su uso en las vistas.
- **Relaciones**:
    - `empresa()`: `belongsTo(Empresa)`.
    - `departamento()`: `belongsTo(Departamento)`.
    - `sucursal()`: `belongsTo(Sucursal)`.
    - `usuario()`: `belongsTo(User)`. Asocia un empleado a una cuenta de usuario del sistema.
    - `creador()`: `belongsTo(User)`.
    - `huellas()`: `hasMany(Huella)`.
    - `rostros()`: `hasMany(Rostro)`.
    - `registrosAsistencia()`: `hasMany(RegistroAsistencia)`.
    - `incidencias()`: `hasMany(Incidencia)`.
    - `asignacionesHorario()`: `hasMany(AsignacionHorario)`.
    - `dispositivos()`: `belongsToMany(Dispositivo)`. Una relación muchos a muchos que indica en qué dispositivos de marcación está registrado un empleado.

### 2.2. Controlador (`App\Http\Controllers\EmpleadoController.php`)

Gestiona toda la lógica del CRUD de empleados, incluyendo el manejo de archivos.

- **Trait `ManagesCrud`**: Hereda la funcionalidad de listado AJAX.
- **Propiedades**: `$model`, `$browseView`, `$listView`, `$with` (`['empresa', 'departamento']`).
- **Métodos Principales**:
    - `applySearch(...)`: Implementa una búsqueda más compleja que los módulos anteriores, permitiendo buscar por `nombres`, `apellidos`, `dni` o `codigo_empleado`.
    - `create()` y `edit()`: Preparan los datos para los formularios, obteniendo las listas de empresas y departamentos activos.
    - `store(...)` y `update(...)`: Contienen la lógica para manejar la subida de la foto de perfil (`foto_perfil`). Utilizan el Facade `Storage` para guardar el archivo en `storage/app/public/empleados/fotos`. En la actualización, se elimina la foto anterior si existe una nueva.
    - `destroy(...)`: Además del borrado lógico, elimina la foto de perfil del almacenamiento para liberar espacio. Incluye un bloque `try-catch` para registrar cualquier error durante la eliminación, lo que aumenta la robustez del proceso.

### 2.3. Requests de Formulario

Validan los datos de entrada y definen mensajes de error personalizados.

#### `App\Http\Requests\StoreEmpleadoRequest.php`
- **`authorize()`**: Devuelve `true`. La autorización se delega explícitamente al controlador mediante `$this->authorize()`.
- **`rules()`**: Define un conjunto completo de reglas. Utiliza `unique:empleados` para campos como `codigo_empleado`, `dni` y `email`. Valida el tipo de contrato con `Rule::in([...])` y el tipo de archivo de imagen.
- **`messages()`**: Proporciona mensajes de error en español, mejorando la experiencia del usuario.

#### `App\Http\Requests\UpdateEmpleadoRequest.php`
- **`rules()`**: Similar a la creación, pero utiliza `Rule::unique('empleados')->ignore($empleadoId)` para los campos únicos. Esto es crucial para permitir que el empleado actual conserve sus propios datos únicos al actualizar otros campos.

### 2.4. Política de Acceso (`App\Policies\EmpleadoPolicy.php`)

Define los permisos para las acciones del CRUD sobre los empleados, delegando la lógica a la función `hasPermission()` del usuario.

### 2.5. Migración de Base de Datos

Define la tabla `empleados` con una estructura detallada.

- **Campos**: Incluye campos para información personal, laboral y biométrica.
- **Claves Foráneas**: `empresa_id`, `user_id`, `creado_por`. Al igual que en módulos anteriores, la FK para `departamento_id` está comentada.
- **Índices y Unicidad**:
    - `unique(['empresa_id', 'codigo_empleado'])` y `unique(['empresa_id', 'dni'])`: Aseguran que el código de empleado y el DNI sean únicos *dentro de la misma empresa*, lo cual es una lógica de negocio muy importante y bien implementada.
    - Múltiples índices (`index([...])`) para optimizar las consultas comunes.

### 2.6. Vistas (Blade)

- **`browse.blade.php` y `list.blade.php`**: Siguen el patrón establecido. La vista de lista está adaptada para mostrar la foto de perfil, el nombre completo y los datos de identificación del empleado.
- **`edit-add.blade.php`**: Un formulario completo que incluye todos los campos necesarios para crear o editar un empleado. Utiliza un campo de tipo `file` para la foto de perfil y selectores (con `select2`) para las relaciones.
- **`read.blade.php`**: (Creada en esta etapa) Muestra de forma clara y organizada toda la información de un empleado, incluyendo su foto y datos relacionados.

## 3. Flujo de Datos

El flujo sigue el patrón CRUD estándar, con la particularidad del manejo de archivos:

1.  **Creación/Actualización con Foto**:
    - El usuario envía el formulario con un archivo de imagen.
    - El `FormRequest` valida que el archivo sea una imagen y cumpla con el tamaño máximo.
    - El controlador verifica si el archivo está presente con `$request->hasFile('foto_perfil')`.
    - Si es una actualización, se elimina el archivo antiguo (`Storage::disk('public')->delete(...)`).
    - Se guarda el nuevo archivo con `$request->file('...')->store(...)`, que genera un nombre único y devuelve la ruta.
    - Esta ruta se guarda en la base de datos en el campo `foto_perfil`.
2.  **Eliminación**:
    - El controlador es invocado.
    - Se elimina el archivo de imagen del disco.
    - Se ejecuta el `soft delete` en el registro del empleado.

## 4. Buenas Prácticas y Puntos de Mejora

### Buenas Prácticas Implementadas

- **Manejo de Archivos Robusto**: La lógica para almacenar, actualizar y eliminar la foto de perfil es correcta y segura, previniendo archivos huérfanos.
- **Validación Avanzada**: El uso de `Rule::unique()->ignore()` es la forma canónica y correcta de manejar la validación de unicidad en actualizaciones.
- **Restricciones de Unicidad Compuestas**: La definición de unicidad a nivel de base de datos para `(empresa_id, dni)` es un excelente ejemplo de cómo implementar reglas de negocio complejas directamente en el esquema de la base de datos.
- **Manejo de Errores**: El bloque `try-catch` en el método `destroy` es una buena práctica para la resiliencia de la aplicación.

### Puntos de Mejora Potenciales

1.  **Refactorización de Consultas**: Al igual que en los controladores anteriores, las consultas para obtener `empresas` y `departamentos` están duplicadas en `create()` y `edit()`. Se puede aplicar la misma sugerencia de refactorización a un método privado.

2.  **Autorización en Form Requests**: El método `authorize()` en `StoreEmpleadoRequest` y `UpdateEmpleadoRequest` actualmente devuelve `true`. La autorización se está realizando en el controlador.
    **Sugerencia**: Para una mejor separación de responsabilidades, la lógica de autorización debería moverse al método `authorize()` de los `Form Requests`. Esto centraliza la validación y la autorización en una sola clase por acción.
    ```php
    // En UpdateEmpleadoRequest.php
    public function authorize()
    {
        // El modelo 'empleado' se obtiene de la ruta
        return $this->user()->can('update', $this->route('empleado'));
    }

    // En StoreEmpleadoRequest.php
    public function authorize()
    {
        return $this->user()->can('create', \App\Models\Empleado::class);
    }
    ```
    Después de hacer esto, las llamadas a `$this->authorize()` en los métodos `store()` y `update()` del controlador se volverían redundantes y podrían eliminarse.

3.  **Optimización de Imágenes**: Actualmente, las imágenes se guardan tal como se suben.
    **Sugerencia**: Para optimizar el almacenamiento y el tiempo de carga, se podría integrar una librería como `intervention/image` para redimensionar y comprimir las fotos de perfil a un tamaño estándar (ej. 200x200px) antes de guardarlas.

# Módulo 03: Gestión de Departamentos (`Departamentos`)

## 1. Propósito del Módulo

El módulo de **Departamentos** se encarga de la administración de las unidades funcionales o áreas de trabajo dentro de una sucursal específica. Permite a los usuarios autorizados crear, visualizar, editar y eliminar departamentos, así como asignarles un jefe de departamento a partir de la lista de empleados existentes.

Este módulo es un eslabón clave en la estructura organizativa del sistema, situándose jerárquicamente por debajo de las **Sucursales** y agrupando a los **Empleados**.

## 2. Componentes del Módulo

A continuación, se detallan los archivos y clases que componen el módulo de Departamentos.

### 2.1. Modelo (`App\Models\Departamento.php`)

Representa la tabla `departamentos` en la base de datos.

- **Atributos `fillable`**: `sucursal_id`, `nombre_departamento`, `descripcion`, `jefe_empleado_id`, `creado_por`, `estado`.
- **Traits**:
    - `HasFactory`: Para la creación de datos de prueba.
    - `SoftDeletes`: Habilita el borrado lógico.
- **Relaciones**:
    - `sucursal()`: Relación `belongsTo` con `Sucursal`. Un departamento pertenece a una única sucursal.
    - `jefe()`: Relación `belongsTo` con `Empleado`. Define quién es el jefe del departamento.
    - `creador()`: Relación `belongsTo` con `User`. Indica el usuario que creó el registro.
    - `empleados()`: Relación `hasMany` con `Empleado`. Un departamento puede tener muchos empleados.

### 2.2. Controlador (`App\Http\Controllers\DepartamentoController.php`)

Orquesta la lógica de negocio para el CRUD de departamentos.

- **Trait `ManagesCrud`**: Al igual que en módulos anteriores, hereda la funcionalidad estándar para el listado y la carga AJAX, promoviendo la reutilización de código.
- **Propiedades Protegidas**:
    - `$model`: `Departamento::class`.
    - `$browseView` y `$listView`: Vistas para el listado (`admin.departamentos.browse` y `admin.departamentos.list`).
    - `$with`: `['sucursal.empresa', 'jefe']`. Carga anticipada (Eager Loading) de relaciones anidadas para optimizar las consultas en la vista de listado.
- **Métodos Principales**:
    - `__construct()`: Aplica el middleware `auth`.
    - `applySearch(...)`: Define la lógica de búsqueda por el campo `nombre_departamento`.
    - `show(Departamento $departamento)`: Muestra la vista de detalles.
    - `create()`: Prepara y muestra el formulario de creación. Carga las sucursales y empleados activos para los menús desplegables.
    - `store(StoreDepartamentoRequest $request)`: Valida y guarda un nuevo departamento.
    - `edit(Departamento $departamento)`: Muestra el formulario de edición con los datos del departamento y las listas de sucursales y empleados.
    - `update(UpdateDepartamentoRequest $request, Departamento $departamento)`: Valida y actualiza un departamento existente.
    - `destroy(Departamento $departamento)`: Realiza el borrado lógico.
- **Autorización**: El controlador hace un uso correcto de `$this->authorize()` en cada método del CRUD para verificar los permisos del usuario antes de ejecutar la acción, delegando la lógica a `DepartamentoPolicy`.

### 2.3. Requests de Formulario

Gestionan la autorización y validación de los datos de entrada para las operaciones de `store` y `update`.

#### `App\Http\Requests\StoreDepartamentoRequest.php`
- **`authorize()`**: Usa el `Gate` para verificar si el usuario tiene el permiso `create` sobre el modelo `Departamento`.
- **`rules()`**: Define las reglas de validación. `sucursal_id` y `nombre_departamento` son obligatorios. `jefe_empleado_id` debe existir en la tabla `empleados` si se proporciona.

#### `App\Http\Requests\UpdateDepartamentoRequest.php`
- **`authorize()`**: Verifica el permiso `update` sobre la instancia específica del departamento que se está editando.
- **`rules()`**: Las reglas son idénticas a las de `StoreDepartamentoRequest`.

### 2.4. Política de Acceso (`App\Policies\DepartamentoPolicy.php`)

Centraliza las reglas de autorización para el módulo.

- **Métodos**: `viewAny`, `view`, `create`, `update`, `delete`.
- **Lógica**: Cada método delega la comprobación al método `hasPermission()` del modelo `User`, verificando permisos como `browse_departamentos`, `read_departamentos`, etc.

### 2.5. Migración de Base de Datos

Define la estructura de la tabla `departamentos`.

- **Campos**: `id`, `sucursal_id`, `nombre_departamento`, `descripcion`, `estado`, `jefe_empleado_id`, `creado_por`, `timestamps`, `deleted_at`.
- **Claves Foráneas**:
    - `sucursal_id` referencia a `sucursales` con borrado en cascada (`cascadeOnDelete`).
    - `creado_por` referencia a `users` con borrado nulo (`nullOnDelete`).
- **Observación**: La clave foránea para `jefe_empleado_id` está comentada en el código proporcionado (`// $table->foreignId('jefe_empleado_id')->nullable()->constrained('empleados')->nullOnDelete();`). Esto puede ser intencional para evitar problemas de restricciones circulares si la tabla `empleados` se crea después o si tiene una dependencia con `departamentos`. Aunque la relación a nivel de Eloquent funciona, la integridad referencial a nivel de base de datos no está siendo forzada para este campo.

### 2.6. Vistas (Blade)

Las vistas del módulo se encuentran en `resources/views/admin/departamentos/`.

- **`browse.blade.php`**: Plantilla principal del listado, que incluye los controles de búsqueda y paginación y el contenedor `#list-container` para la carga AJAX.
- **`list.blade.php`**: Vista parcial que renderiza la tabla de departamentos. Muestra información clave como el nombre, la sucursal (y la empresa a la que pertenece) y el jefe asignado. Las acciones están protegidas por directivas `@can`.
- **`edit-add.blade.php`**: Formulario unificado para crear y editar. Contiene selectores para la sucursal y el jefe de departamento, poblados con los datos cargados desde el controlador.
- **`read.blade.php`**: Vista de solo lectura que muestra todos los atributos de un departamento, incluyendo la información de su sucursal y el nombre completo del jefe.

## 3. Flujo de Datos

El flujo de datos es consistente con los módulos anteriores, siguiendo un patrón CRUD estándar enriquecido con AJAX.

1.  **Listado**: El usuario accede a la ruta de `index`, se carga la vista `browse.blade.php` y, mediante AJAX, se obtiene el contenido de `list.blade.php` para mostrar la tabla de datos.
2.  **Creación**: El controlador (`create`) obtiene las colecciones de sucursales y empleados y las pasa a la vista `edit-add.blade.php`. Tras el envío, el `FormRequest` valida los datos y el método `store` persiste el nuevo departamento.
3.  **Actualización**: Similar a la creación, pero el método `edit` pasa a la vista un objeto `Departamento` existente. El método `update` se encarga de actualizar el registro.
4.  **Eliminación**: Se realiza mediante una petición `DELETE` que es manejada por el método `destroy`, el cual ejecuta un borrado lógico sobre el modelo.

## 4. Buenas Prácticas y Puntos de Mejora

### Buenas Prácticas Implementadas

- **Consistencia Arquitectónica**: El módulo sigue el mismo patrón de diseño que los módulos anteriores (Controller + Trait, Form Requests, Policies, Vistas AJAX), lo que facilita enormemente el mantenimiento y la comprensión del código.
- **Optimización de Consultas**: El uso de Eager Loading anidado (`with(['sucursal.empresa', ...])`) es una excelente práctica que evita múltiples consultas a la base de datos al renderizar la lista de departamentos.
- **Código Limpio en Vistas**: El uso del helper `optional()` en las vistas Blade (`optional($d->sucursal->empresa)->nombre_empresa`) previene errores si una relación no está cargada o es nula, haciendo el código más robusto.

### Puntos de Mejora Potenciales

1.  **Duplicación de Consultas en Controlador**: Al igual que en `SucursalController`, las consultas para obtener sucursales y empleados se repiten en los métodos `create()` y `edit()`.
    ```php
    $sucursales = Sucursal::with('empresa')->where('estado', 'activo')->get();
    $empleados  = Empleado::where('estado', 'activo')->get();
    ```
    **Sugerencia**: Se puede refactorizar esta lógica a un método privado, como `getFormData()`, que devuelva un array con ambas colecciones.
    ```php
    // En DepartamentoController
    private function getFormData(): array
    {
        return [
            'sucursales' => Sucursal::with('empresa')->where('estado', 'activo')->get(),
            'empleados'  => Empleado::where('estado', 'activo')->get(),
        ];
    }

    // En create()
    return view('admin.departamentos.edit-add', array_merge(
        ['departamento' => new Departamento()],
        $this->getFormData()
    ));

    // En edit()
    return view('admin.departamentos.edit-add', array_merge(
        compact('departamento'),
        $this->getFormData()
    ));
    ```

2.  **Autorización Duplicada**: En el controlador, la autorización se invoca tanto en el método del controlador (`store`, `update`) como en el `FormRequest` correspondiente (`authorize`).
    **Sugerencia**: Dado que el `FormRequest` ya maneja la autorización antes de que se ejecute el método del controlador, las llamadas a `$this->authorize()` en `store()` y `update()` son redundantes y pueden eliminarse para simplificar el código. El framework ejecuta `authorize()` del FormRequest automáticamente.

3.  **Integridad Referencial**: Como se mencionó, la clave foránea `jefe_empleado_id` no está definida a nivel de base de datos.
    **Sugerencia**: Si es posible, se debería añadir la restricción de clave foránea en la migración para garantizar la integridad de los datos. Si hay un problema de orden de migración, se puede crear una migración separada exclusivamente para añadir las claves foráneas después de que todas las tablas principales hayan sido creadas.

```

He generado el archivo `modulos/03_departamentos.md` y lo he añadido a nuestro contexto. El análisis sigue la misma línea, destacando la consistencia del código y señalando puntos específicos de mejora, como la duplicación de consultas y la autorización redundante.

Estoy listo para el siguiente paso.

<!--
[PROMPT_SUGGESTION]Ahora, analiza el módulo de "Empleados" y genera su documentación.[/PROMPT_SUGGESTION]
[PROMPT_SUGGESTION]Aplica la sugerencia de refactorizar las consultas duplicadas en el `DepartamentoController`.[/PROMPT_SUGGESTION]

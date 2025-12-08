# Módulo de Gestión de Empresas

**Ruta principal:** `/admin/empresas`

## 1. Propósito

El módulo de Empresas es el pilar de la estructura organizacional en GobeBio. Su función principal es administrar las diferentes entidades o compañías que utilizarán el sistema. Cada empresa funciona como un contenedor aislado para sus propios recursos, como sucursales, departamentos, empleados y horarios.

Este módulo implementa un CRUD (Crear, Leer, Actualizar, Borrar) completo y sigue un patrón de diseño estandarizado que se reutiliza en otras partes del sistema.

## 2. Componentes Clave

### 2.1. Modelo (`App\Models\Empresa`)

Representa la tabla `empresas` en la base de datos.

*   **`$fillable`**: Define los campos que se pueden asignar masivamente, como `nombre_empresa`, `ruc`, `logo`, etc.
*   **`SoftDeletes`**: Utiliza borrado lógico, lo que significa que los registros no se eliminan permanentemente de la base de datos, permitiendo conservar la integridad de los datos históricos.
*   **Relaciones**:
    *   `creador()`: Relación `belongsTo` con el modelo `User` para identificar quién creó el registro.
    *   `sucursales()`, `empleados()`, `horarios()`: Relaciones `hasMany` que conectan la empresa con sus recursos dependientes.

### 2.2. Controlador (`App\Http\Controllers\EmpresaController`)

Orquesta toda la lógica del módulo.

*   **`use ManagesCrud`**: Importa un Trait personalizado que estandariza el listado y la paginación vía AJAX, promoviendo el código DRY (Don't Repeat Yourself).
*   **Propiedades**:
    *   `$model`: `Empresa::class`. Define el modelo principal que gestionará el controlador.
    *   `$browseView` y `$listView`: Especifican las vistas Blade para la página de listado principal y la vista parcial que se carga con AJAX.
    *   `$with`: `['creador']`. Indica que la relación `creador` debe cargarse de forma anticipada (Eager Loading) para optimizar las consultas en el listado.
*   **Métodos CRUD**:
    *   `index()` y `list()`: Gestionan la visualización del listado.
    *   `create()` y `store()`: Manejan la creación de nuevas empresas. `store()` procesa la subida del logo y asigna el `creado_por`.
    *   `show()`: Muestra los detalles de una empresa.
    *   `edit()` y `update()`: Gestionan la edición. `update()` incluye lógica para eliminar el logo antiguo si se sube uno nuevo.
    *   `destroy()`: Realiza el borrado lógico de la empresa y elimina su archivo de logo del almacenamiento.
*   **`applySearch()`**: Define la lógica de búsqueda específica para este módulo, permitiendo buscar por `nombre_empresa` y `ruc`.

### 2.3. Requests de Formulario

Centralizan la validación y la autorización para las operaciones de creación y actualización.

*   **`StoreEmpresaRequest`**:
    *   `authorize()`: Verifica que el usuario tenga el permiso `add_empresas` a través de `EmpresaPolicy`.
    *   `rules()`: Define las reglas de validación para crear una empresa, como `required` para el nombre y `unique` para el RUC.
*   **`UpdateEmpresaRequest`**:
    *   `authorize()`: Verifica que el usuario tenga el permiso `edit_empresas`.
    *   `rules()`: Similar al `Store`, pero ajusta la regla `unique` del RUC para ignorar el registro que se está actualizando.

### 2.4. Política de Acceso (`App\Policies\EmpresaPolicy`)

Define quién puede realizar cada acción en el módulo. Cada método (`viewAny`, `create`, `update`, `delete`) se mapea a un permiso específico de Voyager (ej: `browse_empresas`, `add_empresas`). Esto permite un control de acceso granular basado en roles.

### 2.5. Vistas

*   **`browse.blade.php`**: La vista principal del listado. Contiene la estructura de la página, el título, el botón "Nueva Empresa" y los controles de búsqueda y paginación. Incluye un contenedor (`#list-container`) donde se carga dinámicamente la tabla de registros.
*   **`list.blade.php`**: Una vista parcial que contiene únicamente la tabla `<table>` con los datos de las empresas y los enlaces de paginación. Es la respuesta que devuelve el método `list()` del controlador.
*   **`edit-add.blade.php`**: El formulario para crear y editar empresas. Es una vista única que se adapta según si se está creando un nuevo registro o editando uno existente (`$empresa->exists`).
*   **`read.blade.php`**: La vista para mostrar los detalles de una empresa en modo de solo lectura.

### 2.6. Migración y Seeder

*   **`create_empresas_table`**: Define la estructura de la tabla `empresas` en la base de datos, incluyendo columnas, tipos de datos, valores por defecto, claves foráneas e índices para optimizar las búsquedas.
*   **`EmpresasTableSeeder`**: Inserta un registro inicial para la "Gobernación del Beni", asegurando que el sistema tenga datos base para funcionar.

## 3. Flujo de Datos

### 3.1. Listado de Empresas (Flujo AJAX)

1.  El usuario navega a `/admin/empresas`. El método `EmpresaController@index` devuelve la vista `browse.blade.php`.
2.  Un script de JavaScript (`list-browse-script`) realiza una petición AJAX a la ruta `admin.empresas.ajax.list`.
3.  El método `EmpresaController@list` se ejecuta:
    *   Autoriza la acción usando `EmpresaPolicy`.
    *   Construye una consulta sobre el modelo `Empresa`.
    *   Aplica la búsqueda por nombre o RUC si el usuario ha escrito en el campo de búsqueda.
    *   Pagina los resultados.
4.  El controlador devuelve la vista `list.blade.php` renderizada con los datos paginados.
5.  El script de JavaScript recibe el HTML y lo inyecta en el div `#list-container` de la vista principal.

Este enfoque evita recargar la página completa al buscar o cambiar de página, mejorando la experiencia de usuario.

### 3.2. Creación de una Empresa

1.  El usuario hace clic en "Nueva Empresa", que apunta a `admin.empresas.create`.
2.  `EmpresaController@create` autoriza la acción y muestra el formulario `edit-add.blade.php`.
3.  El usuario rellena el formulario y lo envía a la ruta `admin.empresas.store` (POST).
4.  `StoreEmpresaRequest` se ejecuta primero, validando todos los campos. Si la validación falla, redirige al usuario de vuelta con los errores.
5.  Si la validación es exitosa, `EmpresaController@store` se ejecuta:
    *   Obtiene los datos validados.
    *   Si se subió un logo, lo guarda en `storage/app/public/logos` y almacena la ruta en la base de datos.
    *   Añade el ID del usuario autenticado al campo `creado_por`.
    *   Crea el registro en la base de datos usando `Empresa::create()`.
6.  Redirige al usuario al listado (`admin.empresas.index`) con un mensaje de éxito.

## 4. Puntos de Interés y Buenas Prácticas

*   **Reutilización de Lógica**: El uso del trait `ManagesCrud` es un excelente ejemplo de cómo abstraer lógica común para mantener los controladores limpios y consistentes.
*   **Seguridad**: La autorización está centralizada en `EmpresaPolicy`, lo que facilita la gestión de permisos. Las acciones en el controlador están protegidas con llamadas a `$this->authorize()`.
*   **Validación**: El uso de `Form Requests` separa la lógica de validación de los controladores, haciéndolos más legibles y manteniendo un único punto de verdad para las reglas de negocio.
*   **Experiencia de Usuario**: El listado con AJAX, la previsualización del logo en el formulario de edición y los mensajes de feedback (`alert-type`) contribuyen a una interfaz más moderna y amigable.
*   **Gestión de Archivos**: La lógica para guardar y eliminar el logo del disco está correctamente implementada en los métodos `store`, `update` y `destroy`.

<!--
[PROMPT_SUGGESTION]Crea la documentación para el módulo de Sucursales, siguiendo la misma estructura que el de Empresas.[/PROMPT_SUGGESTION]
[PROMPT_SUGGESTION]Genera el código para un `Observer` de Laravel que se encargue de registrar en la tabla `logs_sistema` cada vez que se crea, actualiza o elimina una `Empresa`.[/PROMPT_SUGGESTION]

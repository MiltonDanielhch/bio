# Módulo 02: Gestión de Sucursales (`Sucursales`)

## 1. Propósito del Módulo

El módulo de **Sucursales** es responsable de la administración de las diferentes sedes, oficinas o ubicaciones físicas asociadas a una empresa registrada en el sistema. Permite crear, visualizar, editar y eliminar sucursales, asignando a cada una su información de contacto, ubicación geográfica y estado.

Este módulo es fundamental para la organización jerárquica del sistema, ya que las sucursales actúan como contenedores para otros elementos como **Departamentos** y **Dispositivos**.

## 2. Componentes del Módulo

A continuación, se detallan los archivos y clases que componen el módulo de Sucursales.

### 2.1. Modelo (`App\Models\Sucursal.php`)

Representa la tabla `sucursales` en la base de datos.

- **Atributos `fillable`**: `empresa_id`, `nombre_sucursal`, `direccion`, `ciudad`, `pais`, `zona_horaria`, `latitud`, `longitud`, `estado`, `creado_por`.
- **Casts**: Convierte `latitud` y `longitud` a `float` para asegurar la precisión numérica.
- **Traits**:
    - `HasFactory`: Para la creación de datos de prueba.
    - `SoftDeletes`: Habilita el borrado lógico, manteniendo los registros en la base de datos con una marca `deleted_at`.
- **Relaciones**:
    - `empresa()`: Relación `belongsTo` con `Empresa`. Una sucursal pertenece a una única empresa.
    - `creador()`: Relación `belongsTo` con `User`. Indica qué usuario creó el registro.
    - `departamentos()`: Relación `hasMany` con `Departamento`. Una sucursal puede tener múltiples departamentos.
    - `dispositivos()`: Relación `hasMany` con `Dispositivo`. Una sucursal puede tener múltiples dispositivos de marcación.

### 2.2. Controlador (`App\Http\Controllers\SucursalController.php`)

Gestiona la lógica de negocio para el CRUD de sucursales.

- **Trait `ManagesCrud`**: Hereda la funcionalidad estándar para el listado (`index`) y la carga de datos vía AJAX (`ajaxList`), lo que reduce significativamente el código repetitivo.
- **Propiedades Protegidas**:
    - `$model`: `Sucursal::class`. Define el modelo principal del controlador.
    - `$browseView` y `$listView`: Definen las vistas para la página de listado y la lista parcial AJAX.
    - `$with`: `['empresa', 'creador']`. Indica que las relaciones `empresa` y `creador` deben ser cargadas mediante Eager Loading para optimizar las consultas.
- **Métodos Principales**:
    - `__construct()`: Aplica el middleware `auth` a todas las rutas del controlador.
    - `applySearch(Builder $query, string $search)`: Sobrescribe el método del trait para aplicar una lógica de búsqueda específica por el campo `nombre_sucursal`.
    - `show(Sucursal $sucursal)`: Muestra la vista de detalles de una sucursal.
    - `create()`: Muestra el formulario de creación. Carga previamente las empresas activas para el selector.
    - `store(StoreSucursalRequest $request)`: Valida y guarda una nueva sucursal, asignando el ID del usuario autenticado como creador.
    - `edit(Sucursal $sucursal)`: Muestra el formulario de edición con los datos de la sucursal y la lista de empresas activas.
    - `update(UpdateSucursalRequest $request, Sucursal $sucursal)`: Valida y actualiza los datos de una sucursal existente.
    - `destroy(Sucursal $sucursal)`: Realiza el borrado lógico de la sucursal.

### 2.3. Requests de Formulario

Centralizan la lógica de autorización y validación para las operaciones de creación y actualización.

#### `App\Http\Requests\StoreSucursalRequest.php`
- **`authorize()`**: Verifica si el usuario tiene el permiso `create` para el modelo `Sucursal` a través del `Gate`.
- **`rules()`**: Define las reglas de validación para los campos del formulario. Campos como `empresa_id` y `nombre_sucursal` son obligatorios. Las coordenadas (`latitud`, `longitud`) tienen reglas numéricas y de rango.

#### `App\Http\Requests\UpdateSucursalRequest.php`
- **`authorize()`**: Verifica si el usuario tiene el permiso `update` sobre la instancia específica de la sucursal que se está editando.
- **`rules()`**: Las reglas de validación son idénticas a las de `StoreSucursalRequest`.

### 2.4. Política de Acceso (`App\Policies\SucursalPolicy.php`)

Define las reglas de autorización para determinar qué acciones puede realizar un usuario sobre el módulo de sucursales.

- **Métodos**:
    - `viewAny`: Permiso `browse_sucursales`.
    - `view`: Permiso `read_sucursales`.
    - `create`: Permiso `add_sucursales`.
    - `update`: Permiso `edit_sucursales`.
    - `delete`: Permiso `delete_sucursales`.

Cada método verifica si el usuario tiene el permiso correspondiente a través del método `hasPermission()`, que probablemente está definido en el modelo `User`.

### 2.5. Migración de Base de Datos

El archivo de migración define la estructura de la tabla `sucursales`.

- **Campos**: `id`, `empresa_id`, `nombre_sucursal`, `direccion`, `ciudad`, `pais`, `zona_horaria`, `latitud`, `longitud`, `estado`, `creado_por`, `timestamps`, `deleted_at`.
- **Claves Foráneas**:
    - `empresa_id` referencia a `empresas` con borrado en cascada (`cascadeOnDelete`).
    - `creado_por` referencia a `users` con borrado nulo (`nullOnDelete`).
- **Índices**: Se han creado índices en `(empresa_id, estado)` y `(ciudad, estado)` para optimizar las consultas que filtran por estas combinaciones.
- **Valores por Defecto**: `zona_horaria` se establece en `America/Lima` y `estado` en `activo`.

### 2.6. Vistas (Blade)

Las vistas utilizan la plantilla `voyager::master` y están ubicadas en `resources/views/admin/sucursales/`.

- **`browse.blade.php`**:
    - Es la vista principal del listado.
    - Contiene el encabezado, el botón "Nueva Sucursal" (protegido por `@can`), y los controles de paginación y búsqueda.
    - Incluye el script `admin.partials.list-browse-script`, que contiene la lógica JavaScript reutilizable para realizar las peticiones AJAX que cargan la lista de datos en el contenedor `#list-container`.
    - Define un modal de confirmación para la eliminación de registros.

- **`list.blade.php`**:
    - Es una vista parcial que renderiza la tabla con los registros de las sucursales.
    - Muestra los campos principales y las acciones (Ver, Editar, Borrar) protegidas por directivas `@can`.
    - Incluye la paginación y el contador de registros.
    - Contiene un script para manejar los clics en los enlaces de paginación, asegurando que la navegación se realice vía AJAX.

- **`edit-add.blade.php`**:
    - Formulario unificado para crear y editar sucursales.
    - Detecta si se está creando o editando una sucursal mediante la propiedad `$sucursal->exists`.
    - Muestra los errores de validación en la parte superior.
    - Utiliza el helper `old()` para preservar los datos del formulario en caso de un error de validación.
    - Incluye JavaScript para validaciones básicas en el lado del cliente y para auto-recortar espacios en blanco en los campos de texto.

- **`read.blade.php`**:
    - Muestra todos los detalles de una sucursal en una tabla descriptiva.
    - Si existen coordenadas de latitud y longitud, incrusta un mapa de Google Maps para visualizar la ubicación.
    - Proporciona botones para volver al listado o para editar el registro (si el usuario tiene permiso).

## 3. Flujo de Datos

1.  **Listado (Index)**:
    - El usuario navega a `admin/sucursales`.
    - `SucursalController@index` (heredado del trait) renderiza la vista `browse.blade.php`.
    - El script `list-browse-script` realiza una petición AJAX a `admin/sucursales/ajax/list`.
    - `SucursalController@ajaxList` (heredado) procesa la petición, aplicando búsqueda y paginación.
    - Se renderiza la vista `list.blade.php` con los datos y se devuelve como respuesta HTML.
    - El JavaScript inserta el HTML en el contenedor `#list-container`.

2.  **Creación (Create/Store)**:
    - El usuario hace clic en "Nueva Sucursal".
    - `SucursalController@create` obtiene las empresas activas y muestra el formulario `edit-add.blade.php`.
    - El usuario rellena el formulario y lo envía (POST a `admin/sucursales`).
    - `StoreSucursalRequest` valida los datos. Si falla, redirige de vuelta con los errores.
    - `SucursalController@store` recibe los datos validados, añade el `creado_por` y crea el registro.
    - Se redirige al usuario al listado con un mensaje de éxito.

3.  **Actualización (Edit/Update)**:
    - El usuario hace clic en "Editar" en una sucursal.
    - `SucursalController@edit` obtiene los datos de la sucursal y las empresas activas, y muestra el formulario `edit-add.blade.php` con los datos precargados.
    - El usuario modifica el formulario y lo envía (PUT a `admin/sucursales/{sucursal}`).
    - `UpdateSucursalRequest` valida los datos.
    - `SucursalController@update` actualiza el registro en la base de datos.
    - Se redirige al listado con un mensaje de éxito.

4.  **Eliminación (Destroy)**:
    - El usuario hace clic en "Borrar".
    - JavaScript abre un modal de confirmación y configura la acción del formulario del modal con la ruta `admin/sucursales/{sucursal}` y el método DELETE.
    - Al confirmar, se envía la petición.
    - `SucursalController@destroy` autoriza la acción y ejecuta `delete()` (soft delete) en el modelo.
    - Se redirige al listado con un mensaje de éxito.

## 4. Buenas Prácticas y Puntos de Mejora

### Buenas Prácticas Implementadas

- **Reutilización de Código**: El uso del trait `ManagesCrud` y del script parcial `list-browse-script` demuestra una excelente práctica para evitar la duplicación de la lógica de listado.
- **Separación de Responsabilidades**:
    - **Form Requests** (`StoreSucursalRequest`, `UpdateSucursalRequest`) para validación y autorización a nivel de petición.
    - **Policies** (`SucursalPolicy`) para una lógica de autorización granular y centralizada.
    - **Controlador** enfocado en el flujo de datos y la orquestación.
- **Optimización de Consultas**: El uso de `with(['empresa', 'creador'])` (Eager Loading) previene el problema N+1 en la vista de listado.
- **Experiencia de Usuario (UX)**: La carga de listas vía AJAX, los mensajes de sesión y los modales de confirmación mejoran la interacción del usuario con la interfaz.
- **Seguridad**: El uso de `SoftDeletes` previene la pérdida accidental de datos y mantiene la integridad referencial.

### Puntos de Mejora Potenciales

1.  **Duplicación de Consulta en Controlador**: La consulta para obtener las empresas activas se repite en los métodos `create()` y `edit()`:
    ```php
    $empresas = Empresa::where('estado', 'activo')->orderBy('nombre_empresa')->get();
    ```
    **Sugerencia**: Se podría refactorizar esto a un método privado dentro del controlador o, mejor aún, utilizar un **View Composer** para vincular `$empresas` automáticamente a la vista `edit-add.blade.php` cada vez que se renderice.

2.  **JavaScript en Vistas Blade**: El JavaScript para el manejo de los clics de paginación AJAX está dentro de `list.blade.php`, y el de validación y auto-trim está en `edit-add.blade.php`.
    **Sugerencia**: Para una mejor organización y mantenimiento, este código JavaScript podría moverse a archivos `.js` dedicados y ser incluido a través del asset pipeline de Laravel (Vite/Mix). Esto también permitiría la minificación y versionado de los scripts.

3.  **Valores por Defecto en el Formulario**: En el formulario `edit-add.blade.php`, los valores por defecto para 'País' y 'Zona Horaria' se aplican en la vista con el operador `??`.
    ```php
    value="{{ old('pais', optional($sucursal)->pais ?? 'Bolivia') }}"
    ```
    **Sugerencia**: Una alternativa más limpia es establecer estos valores por defecto en el método `create()` del controlador, asignándolos al nuevo objeto `Sucursal` antes de pasarlo a la vista. Esto mantiene la lógica de negocio fuera de la capa de presentación.
    ```php
    // En SucursalController@create
    $sucursal = new Sucursal([
        'pais' => 'Bolivia',
        'zona_horaria' => 'America/La_Paz'
    ]);
    return view('admin.sucursales.edit-add', compact('sucursal', 'empresas'));
    ```


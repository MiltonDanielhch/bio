# Módulo 10: Registros de Asistencia (`RegistroAsistencia`)

## 1. Propósito del Módulo

El módulo de **Registros de Asistencia** es el núcleo del sistema para el seguimiento de la jornada laboral de los empleados. Su función principal es almacenar cada evento de marcaje (entrada, salida, etc.), ya sea que provenga de un dispositivo biométrico o que sea ingresado manualmente por un administrador.

Este módulo proporciona un CRUD completo que permite a los administradores:
- Visualizar, filtrar y buscar todos los registros de asistencia del sistema.
- Crear nuevos registros de asistencia de forma manual para corregir olvidos o errores.
- Editar los detalles de un registro existente (por ejemplo, corregir un tipo de marcaje).
- Eliminar registros duplicados o incorrectos.

Además de la gestión manual, esta tabla es el destino final de los datos descargados desde los dispositivos físicos, los cuales son procesados posteriormente para calcular horas trabajadas, retrasos e incidencias.

## 2. Componentes del Módulo

### 2.1. Modelo (`App\Models\RegistroAsistencia.php`)

Representa un único evento de marcaje en la tabla `registros_asistencia`.

- **Atributos `fillable`**: Incluye `empleado_id`, `dispositivo_id`, `tipo_marcaje`, `fecha_hora`, y campos adicionales como `latitud`, `longitud` para marcajes móviles, y `observaciones`.
- **Casts**: Realiza conversiones de tipos de datos para `fecha_hora` (a `datetime`), `procesado` (a `boolean`) y campos numéricos (a `decimal`), asegurando la consistencia de los datos.
- **Relaciones**:
    - `empleado()`: `belongsTo(Empleado)`.
    - `dispositivo()`: `belongsTo(Dispositivo)`.
    - `incidencia()`: `belongsTo(Incidencia)`.
- **Query Scopes**: Ofrece métodos rápidos para consultas comunes:
    - `scopePendientes()`: Registros cuyo estado de validación es 'pendiente'.
    - `scopeNoProcesados()`: Registros que aún no han sido analizados por el sistema de cálculo de horas.
    - `scopeDelDia()` y `scopePorEmpleado()`: Para filtrar registros por fecha o por empleado.

### 2.2. Controlador (`App\Http\Controllers\RegistroAsistenciaController.php`)

Gestiona la lógica del CRUD para los registros de asistencia.

- **Trait `ManagesCrud`**: Hereda la funcionalidad estándar para el listado AJAX, paginación y ordenamiento.
- **Propiedades**: Define las vistas (`browseView`, `listView`, `readView`), el modelo, las relaciones a cargar (`with`) y el orden por defecto.
- **Métodos Principales**:
    - `applySearch(...)`: Implementa una búsqueda avanzada que permite buscar por nombre, apellido o código del empleado, así como por el nombre del dispositivo, utilizando `whereHas` para consultar las relaciones.
    - `create()` y `edit()`: Preparan los datos necesarios para los formularios (listas de empleados y dispositivos activos).
    - `store()` y `update()`: Utilizan los `FormRequest` para validar los datos antes de crear o actualizar el registro.
    - `show()`: Muestra la vista de solo lectura (`read.blade.php`) con los detalles completos de un registro.
    - `destroy()`: Elimina un registro, con manejo de errores y logging.

### 2.3. Requests de Formulario

Estos archivos centralizan la lógica de autorización y validación, manteniendo el controlador limpio.

#### `App\Http\Requests\StoreRegistroAsistenciaRequest.php` y `UpdateRegistroAsistenciaRequest.php`
- **`authorize()`**: Verifica si el usuario tiene los permisos adecuados (`create` o `update`) utilizando la `RegistroAsistenciaPolicy`.
- **`rules()`**: Define las reglas de validación para cada campo del formulario.
    - **Validación de Hora Flexible**: La regla para `hora_local` es particularmente robusta: `['required', 'regex:/.../']`. Utiliza una expresión regular para aceptar formatos de hora tanto con segundos (`HH:mm:ss`) como sin ellos (`HH:mm`), solucionando inconsistencias entre navegadores.
- **`getValidatedData()`**: Un método helper muy útil que no solo devuelve los datos validados, sino que también procesa y añade un nuevo campo. Combina `fecha_local` y `hora_local` en un único campo `fecha_hora` de tipo timestamp, que es el que finalmente se almacena en la base de datos.

### 2.4. Política de Acceso (`App\Policies\RegistroAsistenciaPolicy.php`)

Asegura que solo los usuarios con los roles y permisos correctos puedan realizar acciones en el módulo. Cada método (`viewAny`, `view`, `create`, `update`, `delete`) se mapea a un permiso específico (ej. `browse_registros_asistencia`).

### 2.5. Migración y Seeder

- **Migración**: Define la estructura de la tabla `registros_asistencia`.
    - **Tipos de Datos**: Utiliza tipos `enum` para campos como `tipo_marcaje` y `tipo_verificacion`, lo que garantiza la integridad de los datos a nivel de base de datos.
    - **Campos Nulos**: Campos como `latitud`, `longitud` y `incidencia_id` son `nullable` para acomodar diferentes tipos de marcajes.
    - **Índices**: Se definen múltiples índices en campos clave (`empleado_id`, `fecha_hora`, `estado_validacion`, etc.) para optimizar el rendimiento de las consultas, que pueden llegar a ser muy pesadas en una tabla con millones de registros.
- **Seeder (`RegistrosAsistenciaSeeder.php`)**: Genera datos de prueba de manera inteligente. Crea 4 marcajes por día (jornada completa) para cada empleado activo durante los últimos 7 días, asignándolos al dispositivo de su sucursal correspondiente.

### 2.6. Vistas (Blade)

- **`browse.blade.php`**: La vista principal que contiene el campo de búsqueda y el contenedor donde se carga la lista de registros mediante AJAX.
- **`list.blade.php`**: Plantilla parcial que renderiza la tabla de resultados. Muestra los datos clave de cada registro y los botones de acción (`Ver`, `Editar`, `Borrar`).
- **`edit-add.blade.php`**: Formulario unificado para crear y editar registros.
    - **Componentes UI**: Utiliza `select2` para una selección amigable de empleados y dispositivos.
    - **Manejo de Errores**: Incluye un bloque para mostrar los errores de validación devueltos por el `FormRequest`, mejorando la experiencia de usuario al depurar fallos en el envío.
- **`read.blade.php`**: Vista de solo lectura que presenta de forma clara y organizada todos los detalles de un registro de asistencia, incluyendo datos del empleado y del dispositivo.

## 3. Flujo de Datos

El flujo de datos para la gestión manual de registros es un ciclo CRUD estándar, mejorado con carga asíncrona:

1.  El usuario accede a la página de "Registros de Asistencia".
2.  Una llamada AJAX inicial carga la primera página de resultados en la vista `list.blade.php`.
3.  El usuario puede buscar o paginar, lo que desencadena nuevas llamadas AJAX que actualizan la lista sin recargar la página.
4.  Al hacer clic en "Añadir" o "Editar", se muestra el formulario `edit-add.blade.php`.
5.  Al enviar el formulario, el `FormRequest` correspondiente intercepta la petición, la autoriza y la valida.
6.  Si la validación falla, Laravel redirige al usuario de vuelta al formulario, mostrando los errores.
7.  Si la validación es exitosa, el método `store()` o `update()` del controlador procesa los datos (usando `getValidatedData()` para crear `fecha_hora`), guarda el registro y redirige al listado con un mensaje de éxito.

## 4. Buenas Prácticas y Puntos Clave

- **Experiencia de Usuario (UX)**: El listado AJAX con búsqueda y paginación asíncrona proporciona una experiencia fluida y moderna.
- **Robustez en la Validación**: El uso de una expresión regular para el campo de la hora es una solución excelente y robusta para manejar las inconsistencias de los navegadores.
- **Código Limpio y Organizado**: La lógica está bien separada: el controlador orquesta, los Form Requests validan, la Policy autoriza y el Trait `ManagesCrud` maneja la lógica de listado repetitiva.
- **Preparación de Datos**: El método `getValidatedData()` en los `FormRequest` es una implementación inteligente para transformar los datos del formulario (`fecha_local`, `hora_local`) al formato requerido por la base de datos (`fecha_hora`) antes de que lleguen al controlador.
- **Base de Datos Optimizada**: La gran cantidad de índices en la migración demuestra una planificación cuidadosa para el rendimiento a largo plazo del sistema.
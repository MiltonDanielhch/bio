# Módulo 06: Asignación de Horarios (`AsignacionHorario`)

## 1. Propósito del Módulo

El módulo de **Asignación de Horarios** es el nexo de unión entre los **Empleados** y los **Horarios**. Su función es definir qué horario de trabajo sigue un empleado específico y durante qué período de tiempo (rango de fechas).

Este módulo es fundamental para el motor de cálculo de asistencia, ya que permite al sistema saber qué horario aplicar a un empleado en una fecha determinada para poder procesar sus registros de entrada y salida, calcular retrasos, ausencias, etc.

## 2. Componentes del Módulo

### 2.1. Modelo (`App\Models\AsignacionHorario.php`)

Representa la tabla `asignacion_horarios`, que funciona como una tabla pivote con información adicional.

- **Atributos `fillable`**: `empleado_id`, `horario_id`, `fecha_inicio`, `fecha_fin`, `activo`.
- **Casts**:
    - `fecha_inicio` y `fecha_fin` a `date` (objetos Carbon).
    - `activo` a `boolean`.
- **Relaciones**:
    - `empleado()`: `belongsTo(Empleado)`.
    - `horario()`: `belongsTo(Horario)`.
- **Query Scopes**: El modelo hace un excelente uso de los scopes para encapsular lógica de consulta reutilizable:
    - `scopeVigentes($query)`: Devuelve las asignaciones activas y cuya fecha de finalización no ha pasado.
    - `scopeParaEmpleado($query, int $empleadoId)`: Filtra las asignaciones para un empleado específico.
    - `scopeEnFecha($query, string $fecha)`: Busca la asignación que estaba activa en una fecha concreta.

### 2.2. Controlador (`App\Http\Controllers\AsignacionHorarioController.php`)

Orquesta la lógica del CRUD para las asignaciones.

- **Trait `ManagesCrud`**: Reutiliza la lógica de listado AJAX.
- **Propiedades**: `$with = ['empleado.empresa', 'horario']` para optimizar las consultas del listado.
- **Métodos Principales**:
    - `applySearch(...)`: Implementa una búsqueda relacional. Utiliza `whereHas('empleado', ...)` para buscar asignaciones a través del nombre, apellido o código del empleado asociado.
    - `create()` y `edit()`: Obtienen las listas de empleados y horarios activos para poblar los selectores del formulario.
    - `store()`, `update()`, `destroy()`: Métodos estándar del CRUD que delegan la validación a los Form Requests.

### 2.3. Requests de Formulario

Estos componentes son particularmente importantes en este módulo debido a la lógica de negocio que contienen.

#### `App\Http\Requests\StoreAsignacionHorarioRequest.php`
- **`authorize()`**: Delega la autorización al `Gate`.
- **`rules()`**: Define las reglas básicas (campos requeridos, fechas válidas, etc.).
- **`withValidator($validator)`**: Aquí reside la lógica de negocio más crítica. Este método añade una regla de validación personalizada que se ejecuta *después* de las reglas básicas.
    - **Propósito**: Evitar que un empleado tenga dos horarios activos que se solapen en el tiempo.
    - **Lógica**: Construye una consulta que busca si ya existe una asignación para el mismo empleado (`empleado_id`) que esté activa y cuyo rango de fechas (`fecha_inicio`, `fecha_fin`) entre en conflicto con el nuevo rango que se está intentando crear. Si encuentra un conflicto (`->exists()`), añade un error de validación al campo `fecha_inicio`.

#### `App\Http\Requests\UpdateAsignacionHorarioRequest.php`
- **`authorize()`**: Delega la autorización al `Gate` para la instancia que se está actualizando.
- **`rules()`**: Define las mismas reglas básicas que el `StoreAsignacionHorarioRequest`.
- **Observación Crítica**: Este request **carece de la validación de solapamiento** (`withValidator`) presente en el `StoreAsignacionHorarioRequest`. Esto representa un **bug potencial**, ya que permitiría al usuario editar una asignación y crear un conflicto de horarios que no podría haber creado desde cero.

### 2.4. Política de Acceso (`App\Policies\AsignacionHorarioPolicy.php`)

Define los permisos del CRUD (`browse_asignacion_horarios`, `read_asignacion_horarios`, etc.) de manera estándar.

### 2.5. Migración de Base de Datos

Define la tabla `asignacion_horarios`.

- **Claves Foráneas**: `empleado_id` y `horario_id` con borrado en cascada (`cascadeOnDelete`), lo que significa que si se elimina un empleado o un horario, sus asignaciones asociadas también se eliminarán.
- **Campos de Fecha**: `fecha_inicio` (obligatorio) y `fecha_fin` (nulable, para asignaciones indefinidas).
- **Índices**: Se crean índices en `(empleado_id, fecha_inicio)` y `activo` para acelerar las búsquedas.

### 2.6. Vistas (Blade)

- **`browse.blade.php` y `list.blade.php`**: Vistas de listado estándar. La tabla muestra claramente el empleado, el horario y el rango de fechas de la asignación.
- **`edit-add.blade.php`**: Formulario de creación/edición con selectores para empleado y horario, y campos de fecha para el rango de vigencia.
- **`read.blade.php`**: Vista de detalle que muestra toda la información de la asignación, incluyendo los días laborales extraídos del horario relacionado.

## 3. Flujo de Datos

1.  **Creación de Asignación**:
    - El usuario rellena el formulario.
    - Al enviar, `StoreAsignacionHorarioRequest` se activa.
    - Primero, valida las reglas básicas (existencia de empleado/horario, formato de fechas).
    - Segundo, ejecuta la lógica en `withValidator` para consultar la base de datos y asegurar que no hay solapamiento de horarios para ese empleado.
    - Si todo es correcto, el método `store` del controlador persiste el registro.
2.  **Búsqueda**:
    - El usuario escribe en el campo de búsqueda.
    - La petición AJAX llega al método `ajaxList` del trait `ManagesCrud`.
    - Este a su vez llama a `applySearch` del controlador, que ejecuta la consulta `whereHas` para filtrar las asignaciones basándose en los datos del empleado relacionado.

## 4. Buenas Prácticas y Puntos de Mejora

### Buenas Prácticas Implementadas

- **Validación de Negocio Compleja**: Implementar la validación de solapamiento de fechas en el `FormRequest` (`withValidator`) es una excelente práctica. Mantiene el controlador limpio y la lógica de negocio encapsulada en el lugar correcto.
- **Query Scopes**: El uso de `scopeVigentes`, `scopeParaEmpleado` y `scopeEnFecha` en el modelo `AsignacionHorario` es un gran acierto. Hace que las consultas desde otras partes de la aplicación (ej. al procesar la asistencia) sean mucho más legibles y mantenibles.
- **Búsqueda Relacional**: El uso de `whereHas` en el controlador para la búsqueda demuestra un buen dominio de Eloquent para consultas complejas.

### Puntos de Mejora Potenciales

1.  **Bug Crítico en Validación de Actualización**: Como se mencionó, `UpdateAsignacionHorarioRequest` no valida el solapamiento de horarios.
    **Sugerencia**: Se debe copiar el método `withValidator` de `StoreAsignacionHorarioRequest` a `UpdateAsignacionHorarioRequest` y modificar la consulta para que excluya la propia asignación que se está editando.
    ```php
    // En UpdateAsignacionHorarioRequest.php
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $empleadoId = $this->empleado_id;
            $fechaInicio = $this->fecha_inicio;
            $fechaFin = $this->fecha_fin;
            $asignacionId = $this->route('asignacionHorario')->id; // Obtener ID actual

            $conflict = \App\Models\AsignacionHorario::where('empleado_id', $empleadoId)
                ->where('id', '!=', $asignacionId) // Excluir el registro actual
                ->where('activo', true)
                ->where(function ($q) use ($fechaInicio, $fechaFin) {
                    $q->whereNull('fecha_fin')
                      ->orWhere('fecha_fin', '>=', $fechaInicio);
                })
                ->when($fechaFin, fn($q) => $q->where('fecha_inicio', '<=', $fechaFin))
                ->exists();

            if ($conflict) {
                $validator->errors()->add('fecha_inicio', 'El empleado ya tiene otro horario activo que se solapa con este rango de fechas.');
            }
        });
    }
    ```

2.  **Error en Consulta de Horarios**: En el controlador, la consulta para obtener los horarios tiene un error.
    ```php
    // En AsignacionHorarioController.php
    $horarios = Horario::where('estado', 'activo')->orderBy('nombre_horario')->get();
    ```
    El modelo `Horario` no tiene una columna `estado` y el nombre del horario es `nombre`, no `nombre_horario`.
    **Sugerencia**: La consulta debería ser simplemente:
    ```php
    $horarios = Horario::orderBy('nombre')->get();
    ```

3.  **Refactorización de Consultas**: Las consultas para obtener `empleados` y `horarios` se repiten en los métodos `create()` y `edit()`.
    **Sugerencia**: Aplicar el mismo patrón de refactorización sugerido en módulos anteriores, moviendo estas consultas a un método privado `getFormData()` para evitar la duplicación de código.

```

He finalizado el análisis de este módulo. La lógica de validación es un punto fuerte, pero he identificado un bug importante en la actualización y un error menor en una consulta que deberían ser atendidos.

Estoy listo para el siguiente módulo.

<!--
[PROMPT_SUGGESTION]Ahora documenta el módulo de "Dispositivos".[/PROMPT_SUGGESTION]
[PROMPT_SUGGESTION]Aplica la corrección del bug de validación en `UpdateAsignacionHorarioRequest`.[/PROMPT_SUGGESTION]

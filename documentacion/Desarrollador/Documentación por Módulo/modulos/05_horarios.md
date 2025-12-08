# Módulo 05: Gestión de Horarios (`Horarios`)

## 1. Propósito del Módulo

El módulo de **Horarios** proporciona la funcionalidad para crear y administrar plantillas de horarios de trabajo reutilizables. Cada horario define una jornada laboral (hora de entrada y salida), una tolerancia para la llegada tardía y los días de la semana en que se aplica.

Estas plantillas de horarios son un componente esencial para el sistema de control de asistencia, ya que posteriormente se asignan a los empleados para determinar sus turnos y calcular la puntualidad.

## 2. Componentes del Módulo

### 2.1. Modelo (`App\Models\Horario.php`)

Representa la tabla `horarios` en la base de datos.

- **Atributos `fillable`**: `nombre`, `hora_entrada`, `hora_salida`, `tolerancia_minutos`, `dias_laborales`, `creado_por`.
- **Traits**: `HasFactory`, `SoftDeletes`.
- **Casts**:
    - `dias_laborales` => `'array'`: Este es un punto clave. El modelo convierte automáticamente la columna `dias_laborales` (que es de tipo `JSON` en la base de datos) a un array de PHP y viceversa. Esto simplifica enormemente la manipulación de los días laborales en el código.
- **Relaciones**:
    - `creador()`: `belongsTo(User)`.
    - `asignacionesHorario()`: `hasMany(AsignacionHorario)`. Indica que un horario puede estar asignado a muchos empleados a través de la tabla pivote de asignaciones.
- **Métodos Helper**:
    - `esDiaLaboral(string $nombreDiaEnIngles)`: Un método de utilidad que verifica si un día específico (pasado como string) está incluido en el array de `dias_laborales` del horario.

### 2.2. Controlador (`App\Http\Controllers\HorarioController.php`)

Gestiona la lógica del CRUD para los horarios.

- **Trait `ManagesCrud`**: Hereda la funcionalidad estándar de listado AJAX.
- **Simplicidad**: A diferencia de otros controladores, los métodos `create()` y `edit()` son muy sencillos, ya que no necesitan cargar datos de otros modelos (como empresas o departamentos) para pasarlos al formulario.
- **Autorización**: Utiliza `$this->authorize()` en cada método para validar los permisos a través de `HorarioPolicy`.
- **Métodos Principales**:
    - `applySearch(...)`: Define la búsqueda por el campo `nombre` del horario.
    - `store(...)`: Valida los datos y los persiste, asignando el `creado_por` con el ID del usuario autenticado.
    - `update(...)`: Valida y actualiza un horario existente.
    - `destroy(...)`: Realiza el borrado lógico del horario.

### 2.3. Requests de Formulario

Centralizan la validación y autorización para las operaciones de `store` y `update`.

#### `App\Http\Requests\StoreHorarioRequest.php` y `UpdateHorarioRequest.php`
- **`authorize()`**: Delegan la autorización al `Gate`, que a su vez utiliza la `HorarioPolicy`.
- **`rules()`**: Definen reglas de validación robustas y específicas:
    - `nombre`: Debe ser único en la tabla `horarios`. `UpdateHorarioRequest` utiliza `Rule::unique()->ignore(...)` para permitir la actualización del registro actual.
    - `hora_entrada` y `hora_salida`: Deben tener el formato `H:i`.
    - `hora_salida`: Debe ser posterior a `hora_entrada`.
    - `dias_laborales`: Debe ser un `array` con al menos un elemento.
    - `dias_laborales.*`: Cada elemento dentro del array `dias_laborales` debe ser uno de los días de la semana permitidos.

### 2.4. Política de Acceso (`App\Policies\HorarioPolicy.php`)

Define los permisos del CRUD (`browse_horarios`, `read_horarios`, `add_horarios`, etc.) y los asocia a los métodos correspondientes, que son verificados desde el controlador.

### 2.5. Migración de Base de Datos

Define la estructura de la tabla `horarios`.

- **Campos Clave**:
    - `nombre`: Definido como `unique()` para evitar horarios duplicados.
    - `hora_entrada`, `hora_salida`: Tipo `time`.
    - `dias_laborales`: Tipo `json`. Esta es una elección de diseño moderna y eficiente para almacenar una lista de valores estructurados, como los días de la semana.

### 2.6. Vistas (Blade)

Las vistas del módulo se encuentran en `resources/views/admin/horarios/`.

- **`browse.blade.php` y `list.blade.php`**: Siguen el patrón de listado AJAX. La vista de lista (`list.blade.php`) itera sobre el array `dias_laborales` para mostrar pequeñas insignias (badges) con las iniciales de cada día laboral, ofreciendo una visualización compacta y clara.
- **`edit-add.blade.php`**: Formulario para crear/editar.
    - Utiliza inputs de tipo `time` para una mejor experiencia de usuario al seleccionar las horas.
    - Para los `dias_laborales`, presenta un grupo de `checkboxes`. La lógica para marcar los checkboxes correctos al editar (`in_array($dia, $oldDias)`) está bien implementada, considerando tanto los datos antiguos de un error de validación (`old()`) como los datos del modelo.
- **`read.blade.php`**: Muestra todos los detalles de un horario en un formato de solo lectura, usando `labels` para resaltar los días laborales.

## 3. Flujo de Datos

El flujo de datos es un CRUD estándar y muy limpio:

1.  **Listado**: El usuario accede a la ruta de `index`, se carga la vista `browse.blade.php` y, mediante AJAX, se obtiene el contenido de `list.blade.php`.
2.  **Creación/Actualización**:
    - El usuario rellena el formulario `edit-add.blade.php`.
    - Al enviar, el `FormRequest` correspondiente valida todos los campos, incluyendo el formato de hora y que los días laborales sean un array válido.
    - El controlador recibe los datos validados. El array `dias_laborales` proveniente de los checkboxes es gestionado automáticamente por Laravel.
    - El modelo, gracias al cast `->cast('array')`, se encarga de serializar el array de días a formato JSON antes de guardarlo en la base de datos.
3.  **Lectura**: Al leer un horario de la base de datos, el cast del modelo se encarga de deserializar la columna JSON de nuevo a un array de PHP, por lo que en las vistas (`list`, `read`) se puede iterar sobre `$horario->dias_laborales` directamente.

## 4. Buenas Prácticas y Puntos de Mejora

### Buenas Prácticas Implementadas

- **Uso de Casts y Columnas JSON**: El uso del cast `'array'` en el modelo junto con una columna `JSON` en la base de datos es la mejor práctica para manejar este tipo de datos estructurados pero simples. Es mucho más limpio y eficiente que usar una tabla pivote o un string delimitado por comas.
- **Validación Detallada**: Las reglas de validación en los `Form Requests` son muy completas, cubriendo formatos, rangos y validación de elementos dentro de un array (`dias_laborales.*`).
- **Código de Controlador Limpio**: La lógica del controlador es muy concisa y fácil de seguir, gracias a la delegación de responsabilidades a los Form Requests, Policies y el Trait `ManagesCrud`.
- **Interfaz de Usuario Clara**: El uso de `checkboxes` para la selección de días y `badges` para su visualización es intuitivo para el usuario final.

### Puntos de Mejora Potenciales

1.  **Autorización Duplicada**: Al igual que en módulos anteriores, la autorización se invoca tanto en el método del controlador (`store`, `update`) como en el `FormRequest`.
    **Sugerencia**: Se puede eliminar la llamada a `$this->authorize()` en los métodos `store()` y `update()` del `HorarioController`, ya que el `FormRequest` ya realiza esta comprobación. Esto evita la redundancia.

2.  **Valores por Defecto en Formulario**: En `edit-add.blade.php`, los días laborales por defecto para un nuevo horario están definidos directamente en la vista:
    ```php
    $oldDias = old('dias_laborales', is_array(optional($horario)->dias_laborales) ? optional($horario)->dias_laborales : ['lunes','martes','miercoles','jueves','viernes']);
    ```
    **Sugerencia**: Para mantener la lógica de negocio fuera de la vista, sería más limpio establecer estos valores por defecto en el método `create()` del controlador.
    ```php
    // En HorarioController@create
    public function create()
    {
        $this->authorize('create', Horario::class);
        $horario = new Horario([
            'dias_laborales' => ['lunes', 'martes', 'miercoles', 'jueves', 'viernes']
        ]);
        return view('admin.horarios.edit-add', compact('horario'));
    }
    ```
    La vista se simplificaría a `old('dias_laborales', $horario->dias_laborales ?? [])`.

```

He finalizado la documentación para el módulo de Horarios. La estructura del proyecto sigue siendo muy consistente y robusta.

Estoy listo para continuar cuando lo desees.

<!--
[PROMPT_SUGGESTION]Ahora documenta el módulo de "Asignación de Horarios".[/PROMPT_SUGGESTION]
[PROMPT_SUGGESTION]Basado en el análisis, ¿puedes crear la documentación para la API de sincronización de dispositivos?[/PROMPT_SUGGESTION]

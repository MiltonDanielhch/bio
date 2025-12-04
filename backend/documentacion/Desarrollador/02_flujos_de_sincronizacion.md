# 3. Flujos de Datos Críticos: Sincronización

La comunicación con el hardware es el aspecto más complejo y propenso a errores del sistema. Para manejar esto de manera robusta, se han implementado dos flujos de datos asíncronos principales, orquestados por Jobs de Laravel.

## 3.1. La Tabla Pivot: `dispositivo_empleado`

Antes de detallar los flujos, es vital entender el rol de la tabla `dispositivo_empleado`. Esta tabla es la "Piedra Rosetta" que conecta el mundo de Laravel con el mundo del dispositivo ZKTeco.

*   `empleado_id`: Foreign key a la tabla `empleados` de Laravel.
*   `dispositivo_id`: Foreign key a la tabla `dispositivos` de Laravel.
*   `zk_user_id`: **El ID interno (`uid`) que el dispositivo ZKTeco asigna al usuario.** Este es un entero y es el identificador más fiable para cruzar datos.
*   `zk_privilege`: El nivel de privilegio del usuario en el dispositivo (ej. usuario normal o administrador).

El objetivo principal de los flujos de sincronización es mantener esta tabla consistente y actualizada.

---

## 3.2. Flujo 1: Sincronización de Usuarios (Laravel ➡ Dispositivo)

Este flujo se encarga de enviar la información de los empleados desde la base de datos de GobeBio hacia la memoria de un reloj biométrico.

*   **Job Responsable:** `App\Jobs\SyncUsersToDeviceJob`
*   **Disparador:** Se invoca desde `DispositivoController@syncUsers` después de que un administrador asigna empleados a un dispositivo a través de la interfaz.

### Lógica del Flujo:

1.  El `SyncUsersToDeviceJob` recibe una instancia del modelo `Dispositivo`.
2.  Recupera todos los empleados asignados a ese dispositivo a través de la relación `belongsToMany`.
3.  Construye un array (`$payload`) con los datos de los usuarios a sincronizar. Cada usuario incluye `uid` (que es el `zk_user_id` de la tabla pivot), `name`, `user_id` (que se mapea al DNI o código de empleado), y `privilege`.
4.  Realiza una petición `POST` al endpoint del microservicio Python: `POST /devices/{ip}/sync-users` con el `$payload`.
5.  **Lógica en el Microservicio (`zk_service.py`):**
    *   Recibe la lista de usuarios.
    *   Se conecta al dispositivo ZKTeco.
    *   **Importante:** Primero borra todos los usuarios existentes en el dispositivo para garantizar un estado limpio (`delete_user` en un bucle).
    *   Itera sobre la lista de usuarios recibida y los crea uno por uno en el dispositivo usando el método `set_user` de la librería `pyzk`.

---

## 3.3. Flujo 2: Sincronización de Asistencias (Dispositivo ➡ Laravel)

Este es el flujo más crítico y frecuente. Se encarga de descargar las marcaciones (logs) desde el reloj y guardarlas en la base de datos de GobeBio.

*   **Job Responsable:** `App\Jobs\SyncAttendanceJob`
*   **Disparador:**
    *   **Manual:** A través del botón "Asistencias" (🔵) en la lista de dispositivos, que llama a `DispositivoController@syncNow`.
    *   **Automático:** Mediante el Scheduler de Laravel, que ejecuta este job periódicamente (ver sección de Automatización).

### Lógica del Flujo y Mecanismo de "Auto-Corrección":

1.  El `SyncAttendanceJob` recibe una instancia del modelo `Dispositivo`.
2.  Realiza una petición `GET` al endpoint del microservicio: `GET /devices/{ip}/attendance`.
3.  **Lógica en el Microservicio (`zk_service.py`):**
    *   Se conecta al dispositivo.
    *   Obtiene todos los registros de asistencia (`get_attendance`).
    *   Devuelve los registros como un array de objetos JSON. Cada objeto contiene `uid` (el ID numérico interno), `user_id` (el string que suele ser el DNI) y `timestamp`.
4.  El `Job` en Laravel recibe la lista de marcaciones crudas.
5.  Itera sobre cada marcación (`$rawRecord`).
6.  **Intento de Búsqueda Principal:** Intenta encontrar al empleado en la tabla pivot `dispositivo_empleado` usando el `uid` del dispositivo, que debería corresponder a nuestro `zk_user_id`.
7.  **Lógica de "Auto-Corrección" (Fallback):**
    *   **Problema Detectado:** A veces, el `uid` en el dispositivo no coincide con lo que se guardó inicialmente, o el mapeo es incorrecto.
    *   **Solución:** Si la búsqueda principal por `uid` falla, el sistema no se rinde. Activa una lógica de fallback:
        *   Busca en la tabla `empleados` un registro cuyo `codigo_empleado` O `dni` coincida con el campo `user_id` que viene del dispositivo.
        *   Esta búsqueda se limita a los empleados asignados a ese dispositivo en particular.
    *   **Auto-Heal:** Si el fallback tiene éxito y encuentra al empleado correcto, el sistema "se cura a sí mismo": actualiza la tabla `dispositivo_empleado` para ese empleado, guardando el `uid` correcto del dispositivo en la columna `zk_user_id`.
    *   La próxima vez que se descarguen asistencias para este usuario, la búsqueda principal (paso 6) funcionará directamente, haciendo el proceso más rápido y eficiente.

```php
// Snippet de la lógica de auto-corrección en SyncAttendanceJob.php
if (!$empleadoId) {
    $empleado = DB::table('empleados')
        ->join('dispositivo_empleado', 'empleados.id', '=', 'dispositivo_empleado.empleado_id')
        ->where('dispositivo_empleado.dispositivo_id', $this->dispositivo->id)
        ->where(function($query) use ($rawRecord) {
            $query->where('empleados.codigo_empleado', $rawRecord['user_id'])
                  ->orWhere('empleados.dni', $rawRecord['user_id']);
        })
        ->select('empleados.id', 'dispositivo_empleado.id as pivot_id')
        ->first();

    if ($empleado) {
        // Auto-heal: Actualizamos el mapeo para que la próxima vez sea rápido
        DB::table('dispositivo_empleado')
            ->where('id', $empleado->pivot_id)
            ->update(['zk_user_id' => $rawRecord['uid']]);
        $empleadoId = $empleado->id;
    }
}
```
8.  Finalmente, si se encontró un `empleadoId` (ya sea por la vía rápida o por el auto-fix), se inserta el nuevo registro en la tabla `registros_asistencia`, asegurándose de no crear duplicados.

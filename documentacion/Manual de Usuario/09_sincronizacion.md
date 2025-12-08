# 4.3. Sincronización de Datos (Usuarios y Asistencias)

La sincronización es el proceso de intercambiar información entre el sistema GobeBio y la memoria del reloj biométrico. Hay dos tipos de sincronización que son fundamentales para la operación diaria.

Estas acciones se realizan desde los botones en la lista de **Gestión de Dispositivos**.

*(Insertar captura de pantalla de la lista de dispositivos con los botones de acción resaltados)*

---

## 🟢 Sincronizar Usuarios (Subir Empleados al Reloj)

Esta acción toma la lista de empleados que usted asignó a un dispositivo y los envía a la memoria del reloj físico.

1.  Haga clic en el botón **Usuarios** (🟢) del dispositivo correspondiente.
2.  El sistema se conectará al reloj y creará o actualizará los datos de los empleados asignados.

**¿Cuándo debe usar esta acción?**
*   **Siempre** después de asignar nuevos empleados a un dispositivo.
*   Si ha cambiado datos importantes de un empleado ya asignado (como su nombre) y necesita que se refleje en la pantalla del reloj.

---

## 🔵 Sincronizar Asistencias (Bajar Marcaciones del Reloj)

Esta es la operación más común. Permite descargar todos los registros de entradas y salidas (marcaciones) desde el reloj y guardarlos de forma segura en la base de datos de GobeBio.

1.  Haga clic en el botón **Asistencias** (🔵) del dispositivo deseado.
2.  El sistema se conectará, descargará todos los registros nuevos y los procesará.

Una vez finalizado, podrá ver las nuevas marcaciones en el módulo **Operaciones > Registros de Asistencia** y estarán listas para ser incluidas en los reportes.

### Nota sobre la Automatización

Aunque puede realizar esta acción manualmente en cualquier momento, el sistema GobeBio está diseñado para que la sincronización de asistencias se ejecute de forma automática cada ciertos minutos. Esta tarea en segundo plano asegura que los datos de marcación estén siempre actualizados sin necesidad de intervención manual.

---

¡Felicidades! Con estos pasos, tiene el control total sobre el flujo de información entre GobeBio y los relojes biométricos. El siguiente paso es aprender a consultar y explotar los datos que ha recopilado.
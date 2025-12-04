# 5. Gestión de Dispositivos Biométricos

Este módulo es el puente entre el sistema GobeBio y los relojes de marcación físicos (hardware ZKTeco). Aquí es donde se configuran los dispositivos y se gestiona la comunicación con ellos.

Para acceder, haga clic en **Gestión de Dispositivos > Dispositivos** en el menú de la izquierda.

## 5.1. Agregar y Configurar un Dispositivo

Antes de poder sincronizar cualquier dato, debe registrar cada reloj biométrico en el sistema.

### Cómo Agregar un Nuevo Dispositivo

1.  En la pantalla de listado de dispositivos, haga clic en el botón **"Añadir Nuevo"**.
2.  Complete el formulario con la información del reloj.

*(Insertar captura de pantalla del formulario para añadir un dispositivo: `admin/dispositivos/create`)*

**Campos del Formulario:**

*   **Nombre:** Un nombre descriptivo y fácil de recordar para el dispositivo (ej. "Reloj Entrada Principal", "Recursos Humanos").
*   **Dirección IP:** La dirección IP que tiene el dispositivo en la red local. Este dato es **fundamental** para que el sistema pueda conectarse.
*   **Puerto:** Generalmente es `4370`. No lo cambie a menos que esté seguro.

3.  Haga clic en **"Guardar"**.

### Probar la Conexión

Una vez guardado, puede verificar si el sistema puede comunicarse con el dispositivo. En la lista de dispositivos, haga clic en el botón **Ver** (el ojo) y dentro de la vista de detalles, encontrará un botón **"Probar Conexión"**. Al presionarlo, el sistema intentará establecer comunicación con el reloj y le notificará si la conexión fue exitosa o si hubo un error.

*(Insertar captura de pantalla de la vista de detalles del dispositivo con el botón "Probar Conexión")*

## 5.2. Acciones Principales y Sincronización

La lista de dispositivos muestra una serie de botones de acción rápida para cada reloj. Estos botones son el centro de la operación diaria.

*(Insertar captura de pantalla de la lista de dispositivos con los botones de acción resaltados)*

### ⚫ Botón "Empleados": Asignar Personal al Reloj

Para que un funcionario pueda marcar en un reloj, primero debe estar "asignado" a él. Este paso crea el vínculo entre el empleado en GobeBio y un ID de usuario en el dispositivo físico.

1.  Haga clic en el botón **Empleados** (⚫) del dispositivo deseado.
2.  Verá una interfaz para seleccionar uno o varios empleados de una lista. Puede buscar por nombre o DNI.
3.  Seleccione todos los empleados que deben tener acceso a este reloj.
4.  Haga clic en **"Guardar Asignaciones"**.

*(Insertar captura de pantalla de la vista de asignación de empleados: `admin/dispositivos/{id}/assign-employees`)*

### 🟢 Botón "Usuarios": Sincronizar Empleados hacia el Dispositivo

Después de asignar empleados a un reloj, debe enviar esa información al dispositivo físico.

1.  Haga clic en el botón **Usuarios** (🟢).
2.  El sistema tomará la lista de empleados que asignó en el paso anterior y los creará o actualizará en la memoria del reloj biométrico.

**¿Cuándo debe usar este botón?**
*   Después de asignar nuevos empleados a un dispositivo.
*   Si ha cambiado datos importantes de un empleado ya asignado (como su nombre).

### 🔵 Botón "Asistencias": Descargar Marcaciones desde el Dispositivo

Esta es la operación más común. Permite descargar todos los registros de entradas y salidas (marcaciones) desde el reloj y guardarlos en la base de datos de GobeBio.

1.  Haga clic en el botón **Asistencias** (🔵).
2.  El sistema se conectará al dispositivo, descargará todos los registros de asistencia que no hayan sido descargados previamente y los procesará.

Una vez finalizado, podrá ver los nuevos registros en el módulo **Operaciones > Registros de Asistencia**.

**Nota sobre la Automatización:** Aunque puede realizar esta acción manualmente, el sistema está preparado para que esta tarea se ejecute de forma automática cada ciertos minutos. Consulte con el administrador del sistema sobre la configuración de esta tarea.

---

Con estos tres botones, tiene el control total sobre el flujo de información entre GobeBio y los relojes biométricos. El siguiente paso es aprender a consultar y explotar los datos que hemos recopilado.

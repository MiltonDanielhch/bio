# 4.1. Añadir y Configurar Dispositivos

Este módulo es el puente entre el sistema GobeBio y los relojes de marcación físicos (hardware ZKTeco). Aquí es donde se configuran los dispositivos y se gestiona la comunicación con ellos.

Para acceder, haga clic en **Gestión de Dispositivos** en el menú de la izquierda.

## Agregar un Nuevo Dispositivo

Antes de poder sincronizar cualquier dato, debe registrar cada reloj biométrico en el sistema.

1.  En la pantalla de listado de dispositivos, haga clic en el botón **"Añadir Nuevo"**.
2.  Complete el formulario con la información del reloj.

*(Insertar captura de pantalla del formulario para añadir un dispositivo: `admin/dispositivos/create`)*

**Campos del Formulario:**

*   **Nombre:** Un nombre descriptivo y fácil de recordar para el dispositivo (ej. "Reloj Entrada Principal", "Recursos Humanos").
*   **Dirección IP:** La dirección IP que tiene el dispositivo en la red local. Este dato es **fundamental** para que el sistema pueda establecer conexión.
*   **Puerto:** Generalmente es `4370`. No lo cambie a menos que esté seguro.

3.  Haga clic en **"Guardar"**.

## Probar la Conexión

Una vez guardado, es muy importante verificar si el sistema puede comunicarse con el dispositivo.

1.  En la lista de dispositivos, haga clic en el botón **Ver** (el icono del ojo) en la fila del dispositivo que acaba de agregar.
2.  Dentro de la vista de detalles, encontrará un botón **"Probar Conexión"**.
3.  Al presionarlo, el sistema intentará establecer comunicación con el reloj y le mostrará una notificación indicando si la conexión fue exitosa o si hubo un error.

*(Insertar captura de pantalla de la vista de detalles del dispositivo con el botón "Probar Conexión")*

---

Una vez que el dispositivo está registrado y la conexión ha sido verificada, el siguiente paso es **asignarle los empleados** que marcarán su asistencia en él.

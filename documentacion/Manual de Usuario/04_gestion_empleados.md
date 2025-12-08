# 3.1. Gestión de Empleados

Una vez definida la estructura de la organización, el siguiente paso es registrar a los funcionarios. Este módulo es el corazón del sistema, ya que aquí se administra la información de todo el personal.
 
Para acceder a esta sección, haga clic en **Gestión de Personal > Empleados** en el menú de navegación de la izquierda.
 
## Visualizar la Lista de Empleados
 
Al ingresar al módulo, verá una tabla con todos los empleados registrados en el sistema.

*(Insertar captura de pantalla de la lista de empleados: `admin/empleados`)*

*   **Buscar:** Puede usar la barra de búsqueda en la parte superior derecha de la tabla para encontrar rápidamente a un empleado por su nombre, apellido, DNI o código.
*   **Acciones:** Cada fila tiene botones para `Ver`, `Editar` y `Eliminar` un empleado.

### Cómo Agregar un Nuevo Empleado
 
1.  Haga clic en el botón **"Añadir Nuevo"** en la esquina superior derecha.
2.  Se abrirá un formulario donde deberá completar la información del funcionario.

*(Insertar captura de pantalla del formulario para añadir un empleado)*

**Campos Clave:**

*   **Departamento:** Seleccione el departamento al que pertenece el empleado. Es crucial para la organización.
*   **Código de Empleado:** Un identificador único interno para el empleado (ej. `BEN-001`).
*   **DNI:** El número de cédula de identidad. **Este campo es muy importante**, ya que el sistema lo usa como un método secundario para identificar al empleado si el ID del dispositivo falla, asegurando que las marcaciones siempre se asignen a la persona correcta.
*   **Nombres y Apellidos:** Ingrese los nombres y apellidos completos tal como aparecerán en los reportes.
*   **Estado:** Por defecto estará como "activo". Si un empleado es dado de baja, puede cambiar su estado a "inactivo".

3.  Una vez completados los datos, haga clic en el botón **"Guardar"**.

### Cómo Editar la Información de un Empleado

1.  En la tabla de empleados, busque al funcionario que desea modificar.
2.  Haga clic en el icono de **Editar** (el lápiz) en la fila correspondiente.
3.  Se abrirá el mismo formulario de antes, pero con los datos del empleado ya cargados.
4.  Realice los cambios necesarios y haga clic en **"Guardar"**.

### Asignar un Empleado a un Dispositivo

**¡Importante!** Crear un empleado en el sistema no lo agrega automáticamente al reloj biométrico. Para que un empleado pueda marcar su asistencia, debe ser **asignado a uno o más dispositivos**.

Este proceso se realiza en el módulo de **Gestión de Dispositivos** y se explicará en detalle en la sección **"4.2. Asignar Empleados a un Dispositivo"** de este manual.

---

El siguiente paso en la gestión de personal es definir los horarios de trabajo y asignarlos, lo cual veremos en las próximas secciones.

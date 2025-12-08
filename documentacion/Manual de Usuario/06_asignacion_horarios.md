# 3.3. Asignación de Horarios a Empleados

Una vez que ha creado las plantillas de horarios de trabajo (como se explicó en la sección anterior), el siguiente paso crucial es asignarlas a los empleados. Este proceso le dice al sistema qué horario debe usar para calcular la asistencia de un funcionario en un período de tiempo determinado.

Un mismo empleado puede tener diferentes horarios a lo largo del tiempo, y el sistema GobeBio gestiona estos cambios de forma inteligente.

Para acceder, haga clic en **Gestión de Horarios > Asignación de Horarios** en el menú.

## Cómo Asignar un Horario a un Empleado
 
1.  Haga clic en el botón **"Añadir Nuevo"**.
2.  Se abrirá el formulario de asignación.

*(Insertar captura de pantalla del formulario de asignación de horario: `admin/asignacion-horarios/create`)*

**Campos del Formulario:**

*   **Empleado:** Seleccione al funcionario de la lista al que le asignará el horario.
*   **Horario:** Seleccione una de las plantillas de horario que creó en el paso anterior.
*   **Fecha de Inicio:** La fecha a partir de la cual este horario entra en vigencia para el empleado.
*   **Fecha de Fin (Opcional):** Si el horario es temporal (ej. por un proyecto o temporada), puede especificar una fecha de finalización. Si deja este campo en blanco, el horario se considerará permanente hasta que se le asigne uno nuevo.

3.  Haga clic en **"Guardar"**.

### Ejemplo Práctico

> Un funcionario trabaja de 8:00 a 16:00 hasta el 31 de diciembre. A partir del 1 de enero, cambia al horario continuo de 8:00 a 14:00.
>
> *   **Asignación 1:** Empleado: [Nombre], Horario: "Oficina 8-16", Fecha Inicio: [Fecha], Fecha Fin: `31-12-2025`.
> *   **Asignación 2:** Empleado: [Nombre], Horario: "Continuo 8-14", Fecha Inicio: `01-01-2026`, Fecha Fin: (vacío).

El sistema GobeBio es lo suficientemente inteligente como para saber qué horario aplicar al momento de generar un reporte, basándose en la fecha.
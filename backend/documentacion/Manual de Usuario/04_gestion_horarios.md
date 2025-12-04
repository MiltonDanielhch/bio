# 4. Gestión de Horarios

Una de las funciones más potentes de GobeBio es su capacidad para calcular automáticamente los atrasos y las faltas. Para que esto funcione correctamente, primero debemos definir los horarios de trabajo y luego asignarlos a cada empleado.

Este módulo se divide en dos partes: **Horarios** (las plantillas) y **Asignación de Horarios** (la vinculación con el empleado).

## 4.1. Horarios

Aquí se crean las plantillas generales de horarios que se usarán en la institución. Por ejemplo: "Turno Mañana", "Horario Continuo", "Turno Tarde".

Para acceder, haga clic en **Gestión de Horarios > Horarios** en el menú de la izquierda.

### Cómo Crear un Nuevo Horario

1.  Haga clic en el botón **"Añadir Nuevo"**.
2.  Complete el formulario con los detalles del horario.

*(Insertar captura de pantalla del formulario para crear un horario: `admin/horarios/create`)*

**Campos del Formulario:**

*   **Nombre:** Un nombre descriptivo para el horario (ej. "Horario de Oficina 8:00-16:00").
*   **Hora de Entrada / Hora de Salida:** La hora oficial de inicio y fin de la jornada laboral.
*   **Días Laborales:** Marque las casillas correspondientes a los días que aplica este horario (ej. Lunes a Viernes).
*   **Minutos de Tolerancia:** Ingrese el número de minutos de gracia que tiene un empleado para marcar su entrada después de la hora oficial sin que se considere un atraso.

3.  Haga clic en **"Guardar"**.

## 4.2. Asignación de Horarios

Una vez creadas las plantillas de horarios, el siguiente paso es asignarlas a los empleados. Un mismo empleado puede tener diferentes horarios a lo largo del tiempo.

Para acceder, haga clic en **Gestión de Horarios > Asignación de Horarios** en el menú.

### Cómo Asignar un Horario a un Empleado

1.  Haga clic en el botón **"Añadir Nuevo"**.
2.  Se abrirá el formulario de asignación.

*(Insertar captura de pantalla del formulario de asignación de horario: `admin/asignacion-horarios/create`)*

**Campos del Formulario:**

*   **Empleado:** Seleccione al funcionario de la lista al que le asignará el horario.
*   **Horario:** Seleccione una de las plantillas de horario que creó en el paso anterior.
*   **Fecha de Inicio:** La fecha a partir de la cual este horario entra en vigencia para el empleado.
*   **Fecha de Fin (Opcional):** Si el horario es temporal, puede especificar una fecha de finalización. Si deja este campo en blanco, el horario se considerará permanente hasta que se le asigne uno nuevo.

3.  Haga clic en **"Guardar"**.

### Ejemplo Práctico

> Un funcionario trabaja de 8:00 a 16:00 hasta el 31 de diciembre. A partir del 1 de enero, cambia al horario continuo de 8:00 a 14:00.
>
> *   **Asignación 1:** Empleado: [Nombre], Horario: "Oficina 8-16", Fecha Inicio: [Fecha], Fecha Fin: `31-12-2025`.
> *   **Asignación 2:** Empleado: [Nombre], Horario: "Continuo 8-14", Fecha Inicio: `01-01-2026`, Fecha Fin: (vacío).

El sistema GobeBio es lo suficientemente inteligente como para saber qué horario aplicar al momento de generar un reporte, basándose en la fecha.

---

¡Excelente! Con los empleados registrados y sus horarios asignados, el sistema ya tiene toda la información necesaria para empezar a controlar la asistencia de forma precisa. El siguiente paso es gestionar los dispositivos físicos.

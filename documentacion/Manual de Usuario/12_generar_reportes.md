# 6.1. Generación de Reportes de Asistencia

Este es el módulo final y el más importante del sistema. Aquí es donde toda la información que ha configurado (empleados, horarios, marcaciones e incidencias) se une para generar reportes detallados y consolidados sobre la puntualidad y asistencia del personal.

Para acceder, haga clic en **Reportes > Reporte de Asistencia** en el menú de la izquierda.

## Cómo Generar un Reporte

Al ingresar al módulo, verá un formulario que le permitirá filtrar y generar un reporte específico.

*(Insertar captura de pantalla del formulario de generación de reportes: `admin/reportes/asistencia`)*

**Pasos para generar el reporte:**_

1.  **Seleccionar Empleado(s):** Puede elegir generar un reporte para un solo empleado o para todos.
2.  **Rango de Fechas:** Especifique la **Fecha de Inicio** y la **Fecha de Fin** para el período que desea analizar.
3.  **Generar Reporte:** Haga clic en el botón **"Generar Reporte"**.

El sistema procesará la solicitud y mostrará los resultados en una tabla detallada.

## Interpretar el Reporte

El reporte generado le mostrará un resumen día por día de la asistencia de los empleados dentro del rango de fechas seleccionado.

*(Insertar captura de pantalla de un reporte ya generado)*

**Columnas del Reporte:**

*   **Día / Fecha:** El día y la fecha que se está analizando.
*   **Horario Aplicado:** El nombre del horario que el empleado tenía asignado para esa fecha.
*   **Entrada / Salida:** La primera y la última marcación del empleado en ese día.
*   **Estado:** Esta es la columna más importante. Indica la situación del empleado para ese día, basado en su horario y en las incidencias registradas.
*   **Detalles:** Proporciona información adicional sobre el estado.

### Significado de los Estados:

*   **🟢 PUNTUAL:** El empleado marcó su entrada dentro de la hora oficial y el período de tolerancia.
*   **⚠️ ATRASO:** El empleado marcó su entrada después de la hora permitida (hora de entrada + minutos de tolerancia). La columna "Detalles" mostrará exactamente cuántos minutos de atraso tuvo (ej. "Atraso (15 min)").
*   **🔴 FALTA INJUSTIFICADA:** El día era un día laboral según el horario del empleado, no se encontró ningún registro de marcación y **no existe una incidencia** que justifique la ausencia.
*   **⚪ LIBRE:** El día no era un día laboral según el horario asignado (ej. un sábado o domingo), o el empleado no tenía un horario asignado para esa fecha.
*   **🔵 FALTA JUSTIFICADA:** No se encontraron marcaciones, pero existe una incidencia registrada (ej. "Baja Médica") que justifica la ausencia. No se cuenta como una falta negativa.
*   **🔵 PERMISO:** El empleado tenía un permiso registrado para ese día o para un rango de horas específico.
*   **🔵 VACACIONES:** El empleado se encontraba en su período de vacaciones según lo registrado en el módulo de incidencias.

## Exportar el Reporte

Una vez que el reporte esté generado en pantalla, tendrá la opción de exportarlo a formatos como **PDF** o **Excel** para su archivo, impresión o para compartirlo. Busque los botones de exportación usualmente ubicados en la parte superior de la tabla de resultados.

---

**¡Felicitaciones!** Ha completado el recorrido por todas las funcionalidades principales del sistema GobeBio. Con estos conocimientos, está completamente capacitado para gestionar la asistencia del personal de manera eficiente y precisa.

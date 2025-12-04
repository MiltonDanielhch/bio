# 7. Reportes de Asistencia

Este es el módulo final y uno de los más importantes del sistema. Aquí es donde toda la información recopilada y configurada (empleados, horarios, asignaciones y registros) se une para generar reportes detallados sobre la puntualidad y asistencia del personal.

Para acceder, haga clic en **Reportes > Reporte de Asistencia** en el menú de la izquierda.

## 7.1. Cómo Generar un Reporte

Al ingresar al módulo, verá un formulario que le permitirá filtrar y generar un reporte específico.

*(Insertar captura de pantalla del formulario de generación de reportes: `admin/reportes/asistencia`)*

**Pasos para generar el reporte:**

1.  **Seleccionar Empleado(s):** Puede elegir generar un reporte para un solo empleado o para todos.
2.  **Rango de Fechas:** Especifique la **Fecha de Inicio** y la **Fecha de Fin** para el período que desea analizar.
3.  **Generar Reporte:** Haga clic en el botón **"Generar Reporte"**.

El sistema procesará la solicitud y mostrará los resultados en una tabla detallada.

## 7.2. Interpretar el Reporte

El reporte generado le mostrará un resumen día por día de la asistencia del empleado dentro del rango de fechas seleccionado.

*(Insertar captura de pantalla de un reporte ya generado)*

**Columnas del Reporte:**

*   **Día / Fecha:** El día y la fecha que se está analizando.
*   **Horario Aplicado:** El nombre del horario que el empleado tenía asignado para esa fecha.
*   **Entrada / Salida:** La primera y la última marcación del empleado en ese día.
*   **Estado:** Esta es la columna más importante. Indica la situación del empleado para ese día, basado en su horario.
*   **Detalles:** Proporciona información adicional sobre el estado.

### Significado de los Estados:

*   **🟢 PUNTUAL:** El empleado marcó su entrada dentro de la hora oficial y el período de tolerancia.
*   **⚠️ ATRASO:** El empleado marcó su entrada después de la hora permitida (hora de entrada + minutos de tolerancia). La columna "Detalles" mostrará exactamente cuántos minutos de atraso tuvo (ej. "Atraso (15 min)").
*   **🔴 FALTA:** El día era un día laboral según el horario del empleado, pero no se encontró ningún registro de marcación para ese día.
*   **⚪ LIBRE:** El día no era un día laboral según el horario asignado (ej. un sábado o domingo), o el empleado no tenía un horario asignado para esa fecha.

## 7.3. Exportar el Reporte

Una vez que el reporte esté generado en pantalla, tendrá la opción de exportarlo a formatos como **PDF** o **Excel** para su archivo, impresión o para compartirlo. Busque los botones de exportación usualmente ubicados en la parte superior de la tabla de resultados.

---

**¡Felicitaciones!** Ha completado el recorrido por todas las funcionalidades principales del sistema GobeBio. Con estos conocimientos, está completamente capacitado para gestionar la asistencia del personal de manera eficiente y precisa.

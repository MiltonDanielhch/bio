# 5.2. Gestión de Incidencias (Permisos, Justificaciones)

No todas las ausencias son faltas. Un funcionario puede tener un permiso autorizado, una baja médica o estar de vacaciones. El módulo de **Gestión de Incidencias** es la herramienta que le permite registrar estas situaciones especiales para que el sistema las tome en cuenta al generar los reportes.

El objetivo principal de este módulo es evitar que se marque una "Falta" o "Atraso" a un empleado cuya ausencia o llegada tarde estaba justificada.

Para acceder, haga clic en **Operaciones > Gestión de Incidencias** en el menú de la izquierda.

## Cómo Registrar una Nueva Incidencia

1.  En la pantalla de listado de incidencias, haga clic en el botón **"Añadir Nuevo"**.
2.  Se abrirá un formulario para que ingrese los detalles de la incidencia.

*(Insertar captura de pantalla del formulario para crear una incidencia)*

**Campos del Formulario:**

*   **Empleado:** Seleccione al funcionario al que corresponde la incidencia.
*   **Tipo de Incidencia:** Elija el motivo de la lista. Las opciones pueden incluir:
    *   Permiso (por horas o día completo)
    *   Falta Justificada (ej. baja médica)
    *   Vacaciones
    *   Comisión de Servicio
*   **Fecha de Inicio / Fecha de Fin:** Defina el rango de fechas que abarca la incidencia. Para un solo día, ambas fechas serán la misma.
*   **Hora de Inicio / Hora de Fin (Opcional):** Si la incidencia es solo por unas horas (ej. un permiso para ir al médico), puede especificar el rango horario.
*   **Motivo / Descripción:** Un campo de texto para que explique en detalle la razón de la incidencia.
*   **Adjuntar Archivo (Opcional):** Puede subir un documento de respaldo, como una foto del certificado médico, una solicitud firmada, etc.

3.  Haga clic en **"Guardar"**.

## ¿Cómo Afecta esto a los Reportes?

Cuando usted genera un reporte de asistencia, el sistema GobeBio es lo suficientemente inteligente como para revisar primero si existe alguna incidencia registrada para un empleado en un día determinado.

*   **Ejemplo:** Si un empleado tiene una "Falta Justificada" registrada para el día 15, cuando se genere el reporte, en lugar de mostrar "🔴 FALTA", el sistema mostrará "🔵 FALTA JUSTIFICADA" (o un estado similar), y no contará ese día como una ausencia injustificada.

---

Con la capacidad de gestionar incidencias, ahora tiene el control total sobre la interpretación de los datos de asistencia. El último paso en el flujo de trabajo es consolidar toda esta información en **Reportes de Asistencia** finales.
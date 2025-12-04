# 6. Operaciones: Registros de Asistencia

Una vez que ha descargado las marcaciones desde un dispositivo biométrico (como se explicó en la sección 5), todos esos datos se almacenan en el sistema. Este módulo le permite ver el historial completo de cada marcación individual.

Para acceder, haga clic en **Operaciones > Registros de Asistencia** en el menú de la izquierda.

## 6.1. Visualizar los Registros

Al ingresar, verá una tabla que contiene todos los registros de asistencia en orden cronológico, desde el más reciente al más antiguo.

*(Insertar captura de pantalla de la tabla de registros de asistencia: `admin/registros-asistencia`)*

Cada fila en esta tabla representa una única marcación realizada por un empleado en un dispositivo y contiene la siguiente información:

*   **Empleado:** El nombre del funcionario que realizó la marcación.
*   **Fecha y Hora:** El momento exacto en que se registró la asistencia.
*   **Dispositivo:** El nombre del reloj biométrico donde se realizó la marcación.
*   **UID en Dispositivo:** El ID técnico del usuario dentro del reloj.

## 6.2. Buscar y Filtrar Registros

Si necesita encontrar una marcación específica o revisar la actividad de un día concreto, puede utilizar las herramientas de búsqueda:

*   **Búsqueda General:** Utilice la barra de búsqueda en la parte superior derecha de la tabla para filtrar rápidamente por nombre de empleado, fecha o dispositivo.

## Nota Importante: Datos Crudos vs. Reportes

Es fundamental entender que esta pantalla muestra los **datos crudos** tal como vienen del reloj. Es simplemente un listado de "quién marcó y a qué hora".

Para ver un análisis completo que incluya el estado de cada día (es decir, si un empleado llegó **Puntual**, tuvo un **Atraso** o tuvo una **Falta**), deberá utilizar el módulo de **Reportes de Asistencia**, el cual se explica en la siguiente sección.

---

Ahora que sabemos cómo consultar las marcaciones individuales, estamos listos para el paso más importante: generar reportes consolidados.

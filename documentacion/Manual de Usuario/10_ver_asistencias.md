# 5.1. Visualización de Registros de Asistencia

Una vez que ha sincronizado las marcaciones desde un dispositivo biométrico, todos esos datos se almacenan de forma segura en el sistema. Este módulo le permite ver el historial completo de cada marcación individual, tal como fue registrada por el reloj.

Para acceder, haga clic en **Operaciones > Registros de Asistencia** en el menú de la izquierda.

## Visualizar la Tabla de Registros

Al ingresar, verá una tabla que contiene todos los registros de asistencia en orden cronológico, desde el más reciente al más antiguo.

*(Insertar captura de pantalla de la tabla de registros de asistencia: `admin/registros-asistencia`)*

Cada fila en esta tabla representa una única marcación realizada por un empleado en un dispositivo y contiene la siguiente información:

*   **Empleado:** El nombre del funcionario que realizó la marcación.
*   **Fecha y Hora:** El momento exacto en que se registró la asistencia.
*   **Dispositivo:** El nombre del reloj biométrico donde se realizó la marcación.
*   **UID en Dispositivo:** El ID técnico del usuario dentro de la memoria del reloj.

## Buscar y Filtrar Registros

Si necesita encontrar una marcación específica o revisar la actividad de un día concreto, puede utilizar las herramientas de búsqueda y filtrado que se encuentran sobre la tabla.

*   **Filtrar por Empleado:** Puede seleccionar un empleado específico para ver únicamente sus marcaciones.
*   **Filtrar por Rango de Fechas:** Puede definir una fecha de inicio y fin para acotar los resultados a un período determinado.
*   **Búsqueda General:** Utilice la barra de búsqueda para filtrar rápidamente por nombre de empleado, dispositivo, etc.

## Nota Importante: Datos Crudos vs. Reportes Analizados

Es fundamental entender que esta pantalla muestra los **datos crudos** tal como vienen del reloj. Es simplemente un listado de "quién marcó y a qué hora".

Para ver un análisis completo que incluya el estado de cada día (es decir, si un empleado llegó **Puntual**, tuvo un **Atraso** o tuvo una **Falta**), deberá utilizar el módulo de **Generación de Reportes**, el cual se explica en una sección posterior.

---

Ahora que sabemos cómo consultar las marcaciones individuales, estamos listos para el paso más importante: generar reportes consolidados que interpreten estos datos.
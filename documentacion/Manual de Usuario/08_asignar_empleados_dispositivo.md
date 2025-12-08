# 4.2. Asignar Empleados a un Dispositivo

Una vez que un dispositivo está registrado en GobeBio, el siguiente paso es indicarle al sistema qué empleados van a utilizar ese reloj para marcar su asistencia. Este proceso crea un vínculo lógico entre los funcionarios registrados en el sistema y el dispositivo físico.

Para que un funcionario pueda marcar en un reloj, primero debe estar "asignado" a él.

## Cómo Asignar Empleados

1.  Vaya a la sección de **Gestión de Dispositivos**.
2.  En la lista, ubique el dispositivo al que desea asignar personal y haga clic en el botón **Empleados** (⚫).

*(Insertar captura de pantalla de la lista de dispositivos con el botón "Empleados" resaltado)*

3.  Será dirigido a una nueva pantalla. A la izquierda, verá la lista de empleados que **aún no han sido asignados** a este dispositivo. A la derecha, verá los que **ya están asignados**.
4.  Puede usar el buscador para encontrar empleados rápidamente. Seleccione uno o varios empleados de la lista de la izquierda y haga clic en el botón **">>"** para moverlos a la lista de asignados.
5.  Si necesita quitar a un empleado, selecciónelo de la lista de la derecha y haga clic en **"<<"**.
6.  Una vez que haya finalizado la selección, haga clic en **"Guardar Asignaciones"**.

*(Insertar captura de pantalla de la vista de asignación de empleados: `admin/dispositivos/{id}/assign-employees`)*

---

**¡Importante!** Este paso solo crea la relación dentro del sistema GobeBio. El paso final es enviar esta lista de empleados a la memoria del reloj físico, lo cual se explica en la siguiente sección sobre **Sincronización**.
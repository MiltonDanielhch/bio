# Análisis y Hoja de Ruta de Mejoras para el Sistema GobeBio

Este documento detalla una serie de mejoras, optimizaciones y nuevas funcionalidades para fortalecer y expandir el ecosistema de GobeBio, basado en un análisis transversal de su arquitectura y código.

---

### I. Mejoras de Arquitectura y Calidad del Código (Fundamentos)

Estas son mejoras de "bajo nivel" que fortalecen la base del código, mejoran la seguridad, el rendimiento y reducen la posibilidad de errores futuros.

#### 1. Optimización de Consultas en Laravel (Prevención de N+1)

*   **Observación**: El `GenerarReporteJob` itera sobre empleados y luego, para cada empleado y cada día, busca marcaciones, horarios e incidencias. Esto puede generar cientos o miles de consultas a la base de datos para un solo reporte (el problema N+1).
*   **Riesgo**: Lentitud extrema en la generación de reportes con muchos empleados o rangos de fechas largos, pudiendo causar timeouts en el Job.
*   **Solución**: Utilizar **Eager Loading** (carga ansiosa) para obtener todos los datos necesarios en un número mínimo de consultas.

    **Ejemplo de implementación en `GenerarReporteJob`:**

    ```php
    // Antes (potencialmente lento)
    $empleados = Empleado::whereIn('id', $this->empleadoIds)->get();
    foreach ($empleados as $empleado) {
        // Dentro del bucle, se hacen consultas para cada empleado:
        $horario = $empleado->horarios()->whereDate(...)->first(); 
        $marcaciones = $empleado->marcaciones()->whereDate(...)->get();
    }

    // Después (optimizado)
    $empleados = Empleado::with([
        // Cargar previamente los horarios que se solapan con el rango del reporte
        'horariosAsignados' => function ($query) {
            $query->where('fecha_inicio', '<=', $this->fechaFin)
                  ->where(function ($q) {
                      $q->where('fecha_fin', '>=', $this->fechaInicio)
                        ->orWhereNull('fecha_fin');
                  });
        },
        // Cargar previamente las marcaciones del rango
        'marcaciones' => function ($query) {
            $query->whereBetween('fecha_hora', [$this->fechaInicio, $this->fechaFin->endOfDay()]);
        },
        // Cargar previamente las incidencias del rango
        'incidencias' => function ($query) {
            $query->where('fecha_inicio', '<=', $this->fechaFin)
                  ->where('fecha_fin', '>=', $this->fechaInicio);
        }
    ])->whereIn('id', $this->empleadoIds)->get();

    // Ahora, al iterar, los datos ya están en memoria, no se hacen más consultas.
    foreach ($empleados as $empleado) {
        // $empleado->horariosAsignados, $empleado->marcaciones, etc. ya están cargados.
    }
    ```

#### 2. Concurrencia en el Monitoreo de Dispositivos (Microservicio Python)

*   **Observación**: La tarea en segundo plano `check_device_status` del microservicio itera sobre la lista `KNOWN_DEVICES` y verifica el estado de cada dispositivo uno por uno.
*   **Riesgo**: Si tienes 10 dispositivos y cada uno tarda 3 segundos en responder, el ciclo completo tarda 30 segundos. Si un dispositivo no responde (timeout de 5s), bloquea la verificación de los demás durante ese tiempo.
*   **Solución**: Utilizar `asyncio.gather` para lanzar todas las verificaciones de red de forma concurrente.

    **Ejemplo de implementación en `background/tasks.py`:**

    ```python
    import asyncio
    from config import settings
    # ... otras importaciones

    async def check_device_status():
        """Verifica el estado de todos los dispositivos de forma concurrente."""
        logger.info("Iniciando ciclo de verificación de estado de dispositivos...")
        
        # Crear una tarea de verificación para cada IP
        tasks = [check_device(ip) for ip in settings.KNOWN_DEVICES]
        
        # Ejecutar todas las tareas concurrentemente
        results = await asyncio.gather(*tasks, return_exceptions=True)

        # Procesar resultados (opcional, para logging)
        for ip, result in zip(settings.KNOWN_DEVICES, results):
            if isinstance(result, Exception):
                logger.warning(f"Fallo la verificación para {ip} durante el ciclo: {result}")
        
        logger.info("Ciclo de verificación de estado completado.")

    async def start_background_tasks():
        while True:
            await check_device_status()
            await asyncio.sleep(settings.DEVICE_CHECK_INTERVAL)
    ```

#### 3. Tipado Estricto con Enums en PHP

*   **Observación**: Usas cadenas de texto para campos de estado (`'procesando'`, `'completado'`, `'Puntual'`, `'Atraso'`).
*   **Riesgo**: Errores tipográficos difíciles de depurar. El código es menos legible y no se autocompleta en el IDE.
*   **Solución**: Adoptar Enums de PHP 8.1+ para un código más robusto y auto-documentado.

    **Ejemplo de implementación:**

    1.  **Crear el Enum:**
        ```php
        // app/Enums/EstadoAsistencia.php
        namespace App\Enums;

        enum EstadoAsistencia: string
        {
            case PUNTUAL = 'Puntual';
            case ATRASO = 'Atraso';
            case FALTA_INJUSTIFICADA = 'Falta Injustificada';
            case FALTA_JUSTIFICADA = 'Falta Justificada';
            case PERMISO = 'Permiso';
            case VACACIONES = 'Vacaciones';
            case LIBRE = 'Libre';
        }
        ```
    2.  **Usarlo en el Modelo (si guardas el estado):**
        ```php
        // En el modelo correspondiente
        protected $casts = [
            'estado' => EstadoAsistencia::class,
        ];
        ```

---

### II. Mejoras a Funcionalidades Existentes

#### 1. Enriquecer el Reporte de Asistencia

*   **Observación**: El reporte actual se centra en el estado diario.
*   **Sugerencia**: Añadir una sección de **resumen totalizado** al final del reporte PDF.
*   **Implementación**:
    1.  En `GenerarReporteJob`, mientras procesas los días de cada empleado, acumula los totales en variables: `$totalAtrasos`, `$totalFaltasInjustificadas`, `$totalHorasTrabajadas`, `$totalHorasExtra`.
    2.  **Cálculo de Horas Extra**: Si la hora de la última marcación es posterior a la hora de salida definida en el horario, calcula la diferencia y súmala a `$totalHorasExtra`.
    3.  Pasa estos totales a la vista del PDF y renderiza una tabla de resumen al final del reporte de cada empleado.

#### 2. Notificaciones en Tiempo Real sobre el Estado de los Dispositivos

*   **Observación**: El frontend de Laravel no se entera de los cambios de estado de los dispositivos en tiempo real.
*   **Sugerencia**: Utilizar el endpoint de WebSocket (`/ws/{client_id}`) del microservicio para notificar al frontend.
*   **Implementación**:
    1.  **En `background/tasks.py` (Python)**: Modificar `check_device` para que, si el estado de un dispositivo cambia, envíe una notificación a todos los clientes conectados usando el `manager` de `ws_service`.
    2.  **En el Frontend (Laravel/JavaScript)**: En la vista de Dispositivos, usar JavaScript para establecer una conexión WebSocket, escuchar los mensajes y actualizar dinámicamente el indicador de estado del dispositivo correspondiente.

---

### III. Nuevas Funcionalidades Críticas (Hoja de Ruta)

#### 1. Módulo de Procesamiento y Cierre de Planillas

*   **Propósito**: Automatizar el proceso de fin de mes de RRHH. El sistema debe ser capaz de "cerrar" un periodo (ej. un mes), generar un resumen final consolidado y bloquearlo para evitar modificaciones. Esto es la base para el cálculo de la nómina.
*   **Implementación**:
    1.  **Nuevas Tablas**:
        *   `periodos_planilla` (`id`, `nombre`, `fecha_inicio`, `fecha_fin`, `estado` ('abierto', 'cerrado')).
        *   `resumen_mensual_asistencia` (`id`, `periodo_id`, `empleado_id`, `total_minutos_atraso`, `total_dias_falta`, `total_horas_trabajadas`, `total_horas_extra_50`, `total_horas_extra_100`).
    2.  **Nuevo Job `ProcesarPlanillaJob`**: Similar a `GenerarReporteJob`, pero su salida es guardar los datos en la tabla `resumen_mensual_asistencia`.
    3.  **Interfaz de Usuario**: Una nueva sección en el admin para ver los periodos, cerrarlos (lo que despacha el Job) y, una vez cerrado, ver el resumen consolidado y exportarlo a Excel.

#### 2. Portal de Autoservicio para Empleados

*   **Propósito**: Reducir drásticamente la carga administrativa de RRHH permitiendo que los empleados gestionen sus propias solicitudes y consulten su información.
*   **Implementación**:
    1.  **Nuevo Rol de Usuario**: Crear un rol "Empleado" en Laravel con permisos muy limitados.
    2.  **Autenticación de Empleados**: Permitir que los empleados inicien sesión (quizás con su DNI y una contraseña).
    3.  **Nuevas Vistas para Empleados**:
        *   **"Mi Asistencia"**: Una vista donde el empleado puede ver su propio historial de marcaciones en un calendario.
        *   **"Solicitar Incidencia"**: Un formulario donde el empleado puede solicitar vacaciones, un permiso por horas, o justificar una falta, adjuntando un archivo (ej. una foto de la receta médica).
    4.  **Flujo de Aprobación**: Cuando un empleado crea una solicitud, se notifica a su supervisor o a RRHH, quienes pueden aprobar o rechazar la solicitud desde su propio panel. La incidencia aprobada se registra automáticamente en el sistema.
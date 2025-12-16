# Módulo de Procesamiento y Cierre de Planillas

## 📋 Descripción General

El **Módulo de Procesamiento y Cierre de Planillas** es un sistema automatizado que permite a RRHH cerrar períodos de asistencia (generalmente mensuales) y generar un resumen consolidado e inmutable de la asistencia de todos los empleados.

### Propósito

Convertir a GobeBio de un "sistema de control" a un "sistema de gestión" que ahorra días de trabajo manual al área de Recursos Humanos al automatizar completamente el proceso de cierre de planillas.

---

## 🏗️ Arquitectura del Módulo

### Base de Datos

#### Tabla: `periodos_planilla`

Almacena los períodos de planilla definidos por el administrador.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT | ID único del período |
| `empresa_id` | BIGINT | FK a empresas |
| `nombre` | VARCHAR | Nombre descriptivo (ej: "Planilla Agosto 2025") |
| `codigo` | VARCHAR | Código único auto-generado (ej: "2025-08") |
| `fecha_inicio` | DATE | Fecha de inicio del período |
| `fecha_fin` | DATE | Fecha de fin del período |
| `estado` | ENUM | 'abierto', 'procesando', 'cerrado', 'error' |
| `descripcion` | TEXT | Descripción opcional |
| `cerrado_por` | BIGINT | FK al usuario que cerró el período |
| `fecha_cierre` | TIMESTAMP | Fecha y hora de cierre |
| `total_empleados_procesados` | INT | Contador de empleados procesados |
| `observaciones` | TEXT | Observaciones o errores |
| `configuracion` | JSON | Parámetros específicos del proceso |

#### Tabla: `resumen_mensual_asistencia`

Almacena el resumen consolidado por empleado para cada período.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT | ID único |
| `periodo_id` | BIGINT | FK a periodos_planilla |
| `empleado_id` | BIGINT | FK a empleados |
| `total_dias_laborables` | INT | Días que debía trabajar según horario |
| `total_dias_trabajados` | INT | Días con al menos una marcación |
| `total_dias_falta` | INT | Días sin marcación ni incidencia |
| `total_dias_incidencia` | INT | Días con incidencias aprobadas |
| `total_tardanzas` | INT | Número de tardanzas |
| `total_minutos_tardanza` | INT | Suma de minutos de retraso |
| `total_horas_trabajadas` | DECIMAL | Horas totales trabajadas |
| `total_horas_programadas` | DECIMAL | Horas que debía trabajar |
| `total_horas_diferencia` | DECIMAL | Diferencia (+ o -) |
| `total_horas_extra_25` | DECIMAL | Horas extra al 25% |
| `total_horas_extra_50` | DECIMAL | Horas extra al 50% |
| `total_horas_extra_100` | DECIMAL | Horas extra al 100% |
| `detalle_incidencias` | JSON | Desglose de incidencias por tipo |
| `detalle_tardanzas_por_dia` | JSON | Array con fecha y minutos de cada tardanza |
| `detalle_faltas` | JSON | Array de fechas con falta |
| `primer_marcacion` | DATE | Primera marcación del período |
| `ultima_marcacion` | DATE | Última marcación del período |
| `estado` | ENUM | 'pendiente', 'validado', 'observado', 'aprobado' |
| `observaciones` | TEXT | Observaciones adicionales |
| `metadatos` | JSON | Datos adicionales personalizables |

**Restricción única:** `(periodo_id, empleado_id)` - Un solo resumen por empleado por período.

---

## 🔧 Componentes Técnicos

### 1. Modelos

#### `PeriodoPlanilla.php`
- **Relaciones:**
  - `empresa()`: BelongsTo Empresa
  - `cerradoPor()`: BelongsTo User
  - `resumenes()`: HasMany ResumenMensualAsistencia

- **Scopes:**
  - `abiertos()`, `cerrados()`, `porEmpresa()`, `entreFechas()`

- **Métodos útiles:**
  - `puedeEditarse()`, `puedeCerrarse()`, `estaProcesando()`, `estaCerrado()`
  - `getDuracionDiasAttribute()`, `getNombreCompletoAttribute()`

#### `ResumenMensualAsistencia.php`
- **Relaciones:**
  - `periodo()`: BelongsTo PeriodoPlanilla
  - `empleado()`: BelongsTo Empleado

- **Scopes:**
  - `porPeriodo()`, `porEmpleado()`, `pendientes()`, `validados()`, `aprobados()`
  - `conFaltas()`, `conTardanzas()`

- **Atributos calculados:**
  - `porcentaje_asistencia`: % de asistencia sobre días laborables
  - `total_horas_extra`: Suma de todas las horas extra
  - `promedio_tardanza`: Promedio de minutos por tardanza

- **Métodos:**
  - `tieneProblemas()`: true si tiene faltas o muchas tardanzas
  - `esPerfecto()`: true si asistencia 100% sin tardanzas
  - `toExportArray()`: Convierte a array para exportación

### 2. Servicio: `PlanillaService.php`

Centraliza toda la lógica de negocio del procesamiento.

#### Métodos principales:

##### `procesarPeriodo(PeriodoPlanilla $periodo): bool`
Procesa un período completo:
1. Obtiene todos los empleados activos de la empresa
2. Procesa cada empleado individualmente
3. Actualiza el total de empleados procesados

##### `procesarEmpleado(PeriodoPlanilla $periodo, Empleado $empleado): ResumenMensualAsistencia`
Procesa un empleado específico:
1. Crea o actualiza el resumen
2. Itera por cada día del período
3. Para cada día:
   - Verifica si es día laboral según horario asignado
   - Detecta incidencias aprobadas
   - Detecta faltas (día laboral sin marcación ni incidencia)
   - Calcula tardanzas comparando hora de entrada real vs programada
   - Calcula horas trabajadas y horas extra
4. Consolida totales y guarda

##### `calcularTardanza($entrada, $horario): array`
Calcula si hay tardanza:
- Compara hora real vs hora programada
- Aplica tolerancia configurada
- Retorna si es tardanza y minutos de atraso

##### `calcularHorasTrabajadas($entrada, $salida, $horario, $nombreDia): array`
Calcula horas:
- Horas reales trabajadas
- Horas programadas
- Clasifica horas extra por porcentaje (25%, 50%, 100%)
- La clasificación puede ajustarse según normativa de cada país

### 3. Job: `ProcesarPlanillaJob.php`

Job asíncrono que ejecuta el procesamiento en segundo plano.

**Características:**
- Implementa `ShouldQueue` para ejecución asíncrona
- `$tries = 3`: Máximo 3 intentos en caso de error
- `$timeout = 1800`: Timeout de 30 minutos
- Manejo robusto de errores con logs
- Método `failed()` para actualizar estado en caso de fallo definitivo

**Flujo:**
1. Actualiza estado a "procesando"
2. Llama a `PlanillaService::procesarPeriodo()`
3. Actualiza estado a "cerrado" con fecha y usuario
4. En caso de error, actualiza estado a "error" con mensaje

### 4. Controlador: `PeriodoPlanillaController.php`

#### Rutas y Métodos:

| Ruta | Método | Acción |
|------|--------|--------|
| `GET /admin/periodos-planilla` | `index()` | Lista períodos con filtro por empresa |
| `GET /admin/periodos-planilla/create` | `create()` | Formulario de creación |
| `POST /admin/periodos-planilla` | `store()` | Guarda nuevo período |
| `GET /admin/periodos-planilla/{id}` | `show()` | Detalle del período con estadísticas |
| `POST /admin/periodos-planilla/{id}/cerrar` | `cerrar()` | Lanza el job de procesamiento |
| `POST /admin/periodos-planilla/{id}/reabrir` | `reabrir()` | Reabre un período cerrado |
| `GET /admin/periodos-planilla/{id}/exportar` | `exportar()` | Descarga Excel del resumen |
| `GET /admin/periodos-planilla/{periodoId}/empleado/{empleadoId}` | `verEmpleado()` | Detalle de un empleado |
| `DELETE /admin/periodos-planilla/{id}` | `destroy()` | Elimina período (solo si está abierto) |

---

## 🎨 Interfaz de Usuario

### Vista: `index.blade.php`
- Lista todos los períodos con paginación
- Filtro por empresa
- Indicadores de estado visual (badges)
- Botones de acción según estado:
  - **Abierto:** Cerrar, Eliminar
  - **Cerrado:** Ver, Exportar, Reabrir
  - **Procesando:** Solo Ver (auto-refresco)
- Auto-refresco cada 10s si hay períodos procesando

### Vista: `create.blade.php`
- Formulario de creación con validación
- Auto-completado inteligente:
  - Sugiere nombre basado en mes/año seleccionado
  - Auto-llena fecha fin con último día del mes
- Panel informativo sobre el proceso

### Vista: `show.blade.php`
- **Panel de información general:**
  - Código, Empresa, Fechas, Usuario que cerró
  
- **Estadísticas (solo si está cerrado):**
  - Total empleados
  - Empleados perfectos
  - Total faltas
  - Total tardanzas
  - Total horas trabajadas
  - Total horas extra

- **Tabla de resúmenes por empleado:**
  - Datos consolidados
  - Indicadores visuales (badges de color)
  - Enlace a detalle individual
  - Resalta filas con problemas

- **Estados:**
  - **Abierto:** Muestra botón para cerrar
  - **Procesando:** Loading con auto-refresco cada 5s
  - **Cerrado:** Muestra tabla completa y opciones de exportar/reabrir
  - **Error:** Muestra observaciones

### Vista: `empleado.blade.php`
- Información del empleado y período
- Resumen consolidado en cards
- Desglose de horas extra
- **Tabla detallada de tardanzas** con fecha y minutos
- **Lista de faltas** con fechas
- Observaciones si existen

---

## 🚀 Flujo de Uso

### 1. Crear Período
1. Admin accede a **Períodos de Planilla**
2. Click en "Nuevo Período"
3. Selecciona empresa, fechas y nombre
4. El sistema genera automáticamente el código
5. Guarda el período (estado: **Abierto**)

### 2. Cerrar Período
1. Desde el listado o detalle, click en "Cerrar Período"
2. Confirma la acción
3. Sistema despacha `ProcesarPlanillaJob`
4. Estado cambia a **Procesando**
5. Job procesa todos los empleados:
   - Calcula asistencia día por día
   - Detecta tardanzas y faltas
   - Calcula horas trabajadas y extras
   - Guarda resumen consolidado
6. Estado cambia a **Cerrado**

### 3. Revisar Resultados
1. Accede al período cerrado
2. Visualiza estadísticas generales
3. Revisa tabla de empleados
4. Click en empleado para ver detalle individual
5. Identifica empleados con problemas (resaltados)

### 4. Exportar para Nómina
1. Click en "Exportar a Excel"
2. Descarga archivo CSV/Excel
3. Archivo incluye:
   - Código y nombre de empleado
   - Departamento
   - Días laborables, trabajados, faltas
   - Tardanzas y minutos
   - Horas trabajadas y programadas
   - Horas extra clasificadas
   - % de asistencia

### 5. Reabrir Período (si es necesario)
1. Click en "Reabrir"
2. Confirma (ADVERTENCIA: elimina resúmenes)
3. Período vuelve a estado **Abierto**
4. Se pueden corregir datos de asistencia
5. Re-cerrar cuando esté listo

---

## 📊 Lógica de Cálculo

### Detección de Tardanzas
```
hora_entrada_real > (hora_programada + tolerancia)
  ➔ Es tardanza
  ➔ minutos_atraso = hora_real - hora_programada
```

### Detección de Faltas
```
SI es_dia_laboral
  Y NO tiene_marcaciones
  Y NO tiene_incidencia_aprobada
  ➔ Es falta
```

### Cálculo de Horas Extra
```
horas_trabajadas = hora_salida - hora_entrada
horas_programadas = según horario asignado
horas_extra = max(0, horas_trabajadas - horas_programadas)

Clasificación (ajustable según país):
- Sábado/Domingo: 100%
- Primeras 2 hrs: 25%
- Siguientes hrs: 50%
```

### Porcentaje de Asistencia
```
% = (total_dias_trabajados / total_dias_laborables) * 100
```

---

## ⚙️ Configuración y Personalización

### Normativa de Horas Extra
La clasificación de horas extra está en `PlanillaService::calcularHorasTrabajadas()`.

**Ejemplo actual:**
```php
if (in_array($nombreDia, ['Sábado', 'Domingo'])) {
    $horasExtra100 = $horasExtra;
} elseif ($horasExtra <= 2) {
    $horasExtra25 = $horasExtra;
} else {
    $horasExtra25 = 2;
    $horasExtra50 = $horasExtra - 2;
}
```

**Para personalizar:** Modifica esta lógica según la legislación laboral de tu país.

### Campos Adicionales (metadatos)
El campo `metadatos` (JSON) permite agregar información adicional sin modificar la estructura de la tabla:

```php
$resumen->metadatos = [
    'bono_asistencia' => true,
    'dias_descanso_medico' => 2,
    // ... cualquier otro dato
];
```

---

## 🔒 Seguridad y Validaciones

### Validaciones de Negocio
- ✅ Solo períodos **abiertos** pueden cerrarse
- ✅ Solo períodos **cerrados** pueden reabrirse o exportarse
- ✅ Solo períodos **abiertos** pueden eliminarse
- ✅ Un empleado solo puede tener un resumen por período (restricción única)

### Control de Acceso
Agregar policies para:
- Ver períodos solo de su empresa
- Solo admin/RRHH puede cerrar períodos
- Solo super-admin puede reabrir períodos cerrados

```php
// Ejemplo de Policy
public function cerrar(User $user, PeriodoPlanilla $periodo)
{
    return $user->hasRole('admin') 
        && $user->empresa_id === $periodo->empresa_id;
}
```

---

## 📈 Mejoras Futuras

### 1. Notificaciones
- Notificar por email cuando un período termine de procesarse
- Alertar a empleados con problemas de asistencia

### 2. Aprobación en Cascada
- Estado "pendiente" → "validado" → "aprobado"
- Workflow de aprobación por niveles (supervisor → RRHH → admin)

### 3. Reportes Adicionales
- PDF del resumen por empleado
- Gráficos de tendencias de asistencia
- Comparativa entre períodos

### 4. Integración con Nómina
- API para sistemas externos de planilla
- Exportación en formatos específicos (contabilidad, etc.)

### 5. Multi-compañía Avanzada
- Dashboard consolidado para holding
- Comparativas entre empresas

---

## 🐛 Troubleshooting

### El job no se ejecuta
**Verificar:**
1. ¿Está corriendo el queue worker?
   ```bash
   php artisan queue:work
   ```
2. Revisar logs: `storage/logs/laravel.log`
3. Verificar tabla `jobs` para ver si está en cola

### Estado queda en "procesando" indefinidamente
**Solución:**
1. Revisar `failed_jobs` table
2. Ejecutar manualmente:
   ```bash
   php artisan queue:retry all
   ```
3. Si persiste, reabrir y volver a cerrar

### Horas extra no se calculan bien
**Verificar:**
1. Horarios asignados correctamente al empleado
2. Marcaciones de entrada Y salida presentes
3. Ajustar lógica en `PlanillaService` según normativa

---

## 📝 Comandos Artisan Útiles

### Procesar manualmente un período (opcional)
```bash
php artisan planilla:procesar {periodo_id}
```

### Exportar período desde consola
```bash
php artisan planilla:exportar {periodo_id} --format=excel
```

---

## 💡 Ejemplo de Uso Completo

```php
// Crear período
$periodo = PeriodoPlanilla::create([
    'empresa_id' => 1,
    'nombre' => 'Planilla Diciembre 2025',
    'fecha_inicio' => '2025-12-01',
    'fecha_fin' => '2025-12-31',
]);

// Cerrar (lanzar job)
ProcesarPlanillaJob::dispatch($periodo);

// O cerrar de forma síncrona (en testing)
app(PlanillaService::class)->procesarPeriodo($periodo);

// Exportar
$data = app(PlanillaService::class)->exportarPeriodoExcel($periodo);
```

---

## ✅ Checklist de Implementación

- [x] Migraciones creadas
- [x] Modelos con relaciones y scopes
- [x] Servicio de lógica de negocio
- [x] Job asíncrono
- [x] Controlador con todas las acciones
- [x] Rutas registradas
- [x] Vistas para index, create, show, empleado
- [ ] Ejecutar migraciones
- [ ] Probar flujo completo
- [ ] Agregar al menú de Voyager
- [ ] Configurar permisos/policies
- [ ] Documentar en manual de usuario

---

**Creado:** {{ now()->format('d/m/Y') }}  
**Versión:** 1.0  
**Autor:** Sistema GobeBio


Esta guía te muestra cómo personalizar el módulo según las necesidades específicas de tu empresa o país.

---

## 🕐 Personalizar Cálculo de Horas Extra

### Ubicación del código
`app/Services/PlanillaService.php` → método `calcularHorasTrabajadas()`

### Escenario 1: Bolivia (Ejemplo actual)
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

### Escenario 2: Perú
```php
// Primeras 2 horas: 25%
// Siguientes horas (hasta hora 12): 35%
// Después de 12 horas o domingos: 100%

if (in_array($nombreDia, ['Domingo'])) {
    $horasExtra100 = $horasExtra;
} elseif ($horasTrabajadas > 12) {
    $horasExtra25 = 2;
    $horasExtra50 = min(10, $horasExtra - 2);
    $horasExtra100 = max(0, $horasExtra - 12);
} elseif ($horasExtra <= 2) {
    $horasExtra25 = $horasExtra;
} else {
    $horasExtra25 = 2;
    $horasExtra50 = $horasExtra - 2;
}
```

### Escenario 3: Chile
```php
// Primeras 2 horas diarias: 50%
// Después de 2 horas o domingos/festivos: 100%

if (in_array($nombreDia, ['Sábado', 'Domingo'])) {
    $horasExtra100 = $horasExtra;
} elseif ($horasExtra <= 2) {
    $horasExtra50 = $horasExtra;
    $horasExtra25 = 0;
} else {
    $horasExtra50 = 2;
    $horasExtra100 = $horasExtra - 2;
}
```

### Escenario 4: México
```php
// Primeras 9 horas semanales: 100%
// Siguientes horas: 200%
// Domingos: 125%

// Nota: Requiere tracking semanal, no diario
// Implementación más compleja que requiere contador semanal
```

---

## ⏰ Personalizar Detección de Tardanzas

### Ubicación del código
`app/Services/PlanillaService.php` → método `calcularTardanza()`

### Opción 1: Sin tolerancia (estricto)
```php
$esTardanza = $horaEntradaReal->gt($horaEntradaProgramada);
$minutos = $esTardanza ? $horaEntradaReal->diffInMinutes($horaEntradaProgramada) : 0;
```

### Opción 2: Tolerancia fija (ej: 10 minutos para todos)
```php
$limiteEntrada = $horaEntradaProgramada->copy()->addMinutes(10);
$esTardanza = $horaEntradaReal->gt($limiteEntrada);
```

### Opción 3: Tolerancia por horario (actual)
```php
$tolerancia = $horario->tolerancia_entrada ?? 0;
$limiteEntrada = $horaEntradaProgramada->copy()->addMinutes($tolerancia);
$esTardanza = $horaEntradaReal->gt($limiteEntrada);
```

### Opción 4: Redondeo a bloques de 15 minutos
```php
$minutosAtraso = $horaEntradaReal->diffInMinutes($horaEntradaProgramada);
$minutosRedondeados = ceil($minutosAtraso / 15) * 15; // Redondea hacia arriba

return [
    'es_tardanza' => $minutosAtraso > $tolerancia,
    'minutos' => $minutosRedondeados,
];
```

---

## 📅 Agregar Feriados/Días Festivos

### Opción 1: Tabla de feriados
Crear migración:
```php
Schema::create('feriados', function (Blueprint $table) {
    $table->id();
    $table->date('fecha');
    $table->string('nombre');
    $table->foreignId('empresa_id')->nullable(); // Si es por empresa
    $table->timestamps();
});
```

Modificar `PlanillaService::procesarEmpleado()`:
```php
// Antes de verificar si es día laboral
$esFeriado = \App\Models\Feriado::where('fecha', $fechaStr)
    ->where(function($q) use ($empleado) {
        $q->whereNull('empresa_id')
          ->orWhere('empresa_id', $empleado->empresa_id);
    })
    ->exists();

if ($esFeriado) {
    // Tratar como día no laboral, o aplicar reglas especiales
    continue;
}
```

### Opción 2: API externa de feriados
```php
use GuzzleHttp\Client;

private function esFeriado($fecha, $pais = 'BO')
{
    $client = new Client();
    $response = $client->get("https://date.nager.at/api/v3/PublicHolidays/{$fecha->year}/{$pais}");
    $feriados = json_decode($response->getBody(), true);
    
    return collect($feriados)->contains('date', $fecha->format('Y-m-d'));
}
```

---

## 💰 Calcular Descuentos por Tardanzas

Agregar campo en migración:
```php
$table->decimal('descuento_tardanzas', 10, 2)->default(0);
```

En `PlanillaService::procesarEmpleado()`:
```php
// Al final del procesamiento
$descuento = 0;

// Opción A: Descuento por tramos
if ($datos['total_tardanzas'] <= 3) {
    $descuento = 0;
} elseif ($datos['total_tardanzas'] <= 5) {
    $descuento = 50; // 50 bolivianos
} else {
    $descuento = 100;
}

// Opción B: Descuento proporcional
$descuento = ($datos['total_minutos_tardanza'] / 60) * $tarifaHoraria;

// Guardar
$datos['descuento_tardanzas'] = $descuento;
```

---

## 📊 Agregar Bonos por Asistencia

Agregar campo:
```php
$table->decimal('bono_asistencia', 10, 2)->default(0);
$table->boolean('elegible_bono')->default(false);
```

En `PlanillaService`:
```php
// Al final del procesamiento
$elegible = $datos['total_dias_falta'] == 0 
         && $datos['total_tardanzas'] <= 2;

$bono = $elegible ? 200 : 0; // 200 bolivianos

$datos['bono_asistencia'] = $bono;
$datos['elegible_bono'] = $elegible;
```

---

## 🏥 Considerar Días de Descanso Médico

Ya implementado con incidencias. Para personalizar:

```php
// En PlanillaService::procesarEmpleado()

if ($tieneIncidencia) {
    $incidenciaDia = $incidencias[$fechaStr]->first();
    $tipoIncidencia = $incidenciaDia->tipoIncidencia;
    
    // Distinguir tipos
    if ($tipoIncidencia->nombre === 'Descanso Médico') {
        // No contar como falta, pero tampoco como día trabajado
        $datos['total_dias_incidencia']++;
        $datos['dias_descanso_medico']++;
    } elseif ($tipoIncidencia->nombre === 'Permiso Pagado') {
        // Contar como trabajado
        $datos['total_dias_trabajados']++;
        $datos['total_dias_incidencia']++;
    }
    // ... otros tipos
}
```

---

## 📧 Agregar Notificaciones

### Notificar cuando se cierre un período

En `ProcesarPlanillaJob::handle()`:
```php
// Al final, después de cerrar exitosamente
\Notification::send(
    User::role('rrhh')->get(),
    new PeriodoCerradoNotification($this->periodo)
);
```

Crear la notificación:
```bash
php artisan make:notification PeriodoCerradoNotification
```

```php
public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject("Período {$this->periodo->nombre} cerrado")
        ->line("El período de planilla ha sido procesado exitosamente.")
        ->line("Total empleados procesados: {$this->periodo->total_empleados_procesados}")
        ->action('Ver Resumen', route('admin.periodos.show', $this->periodo->id));
}
```

### Notificar empleados con problemas

```php
$problematicos = $this->periodo->resumenes()
    ->where('total_dias_falta', '>', 2)
    ->orWhere('total_tardanzas', '>', 5)
    ->get();

foreach ($problematicos as $resumen) {
    $resumen->empleado->user?->notify(
        new AsistenciaObservadaNotification($resumen)
    );
}
```

---

## 🎨 Personalizar Interfaz

### Cambiar colores de estado

En `resources/views/admin/periodos/show.blade.php`:
```php
// Cambiar umbrales
$clase = $resumen->porcentaje_asistencia >= 98 ? 'success' :  // Era 95
         ($resumen->porcentaje_asistencia >= 85 ? 'warning' :  // Era 80
         'danger');
```

### Agregar columna personalizada

En la vista:
```html
<th>Columna Custom</th>
```

En el controlador, calcular el valor:
```php
foreach ($periodo->resumenes as $resumen) {
    $resumen->valor_custom = /* tu cálculo */;
}
```

---

## 📈 Agregar Gráficos

### Usando Chart.js

En `show.blade.php`:
```html
<canvas id="graficoAsistencia"></canvas>

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('graficoAsistencia');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($periodo->resumenes->pluck('empleado.nombre_completo')) !!},
        datasets: [{
            label: '% Asistencia',
            data: {!! json_encode($periodo->resumenes->pluck('porcentaje_asistencia')) !!},
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    }
});
</script>
@endsection
```

---

## 🔐 Agregar Aprobación Multi-nivel

### Agregar campos
```php
$table->foreignId('aprobado_por_supervisor')->nullable();
$table->timestamp('fecha_aprobacion_supervisor')->nullable();
$table->foreignId('aprobado_por_rrhh')->nullable();
$table->timestamp('fecha_aprobacion_rrhh')->nullable();
```

### Flujo de estados
```
pendiente → validado_supervisor → validado_rrhh → aprobado
```

### Métodos en el controlador
```php
public function aprobarSupervisor($periodoId)
{
    $periodo = PeriodoPlanilla::findOrFail($periodoId);
    
    // Verificar permisos
    if (!auth()->user()->hasRole('supervisor')) {
        abort(403);
    }
    
    $periodo->update([
        'estado' => 'validado_supervisor',
        'aprobado_por_supervisor' => auth()->id(),
        'fecha_aprobacion_supervisor' => now(),
    ]);
    
    return redirect()->back()->with('success', 'Período aprobado por supervisor');
}
```

---

## 🌍 Multi-Moneda

Si manejas múltiples monedas:

```php
// En ResumenMensualAsistencia
$table->string('moneda', 3)->default('BOB');
$table->decimal('tipo_cambio', 10, 4)->default(1);

// Métodos de conversión
public function getTotalHorasExtraUSD()
{
    return $this->total_horas_extra / $this->tipo_cambio;
}
```

---

## 📱 API para Integración Externa

Crear controlador API:
```php
// routes/api.php
Route::get('/periodos/{periodo}/resumen', [ApiPlanillaController::class, 'resumen'])
    ->middleware('auth:sanctum');

// ApiPlanillaController
public function resumen(PeriodoPlanilla $periodo)
{
    return response()->json([
        'periodo' => $periodo,
        'resumenes' => $periodo->resumenes()->with('empleado')->get(),
        'estadisticas' => [
            'total_empleados' => $periodo->resumenes->count(),
            // ... otras stats
        ]
    ]);
}
```

---

## 🧪 Testing Personalizado

```php
// tests/Feature/PlanillaCustomTest.php

public function test_calcula_horas_extra_correctamente()
{
    $planillaService = new PlanillaService();
    
    // Mock de entrada/salida
    $entrada = /* ... */;
    $salida = /* ... */;
    $horario = /* ... */;
    
    $resultado = $this->callPrivateMethod(
        $planillaService,
        'calcularHorasTrabajadas',
        [$entrada, $salida, $horario, 'Lunes']
    );
    
    $this->assertEquals(2, $resultado['horas_extra_25']);
    $this->assertEquals(1, $resultado['horas_extra_50']);
}

private function callPrivateMethod($object, $method, $args)
{
    $reflection = new \ReflectionClass(get_class($object));
    $method = $reflection->getMethod($method);
    $method->setAccessible(true);
    return $method->invokeArgs($object, $args);
}
```

---

## 💡 Consejos Finales

1. **Documenta tus cambios**: Actualiza la documentación cuando personalices
2. **Version control**: Usa git para hacer commits antes de personalizar
3. **Testing**: Prueba con datos reales antes de usar en producción
4. **Backup**: Siempre haz backup antes de modificar lógica crítica
5. **Consulta legal**: Verifica que los cálculos cumplan con la legislación local

---

## 📞 Soporte

Si necesitas ayuda con personalizaciones específicas, consulta:
- Documentación técnica completa
- Ejemplos de uso (`app/Examples/EjemploPlanillaUsage.php`)
- Código fuente del servicio (`app/Services/PlanillaService.php`)

---

**¡Buena suerte con tu implementación personalizada!** 🚀

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumenMensualAsistencia extends Model
{
    use HasFactory;

    protected $table = 'resumen_mensual_asistencia';

    protected $fillable = [
        'periodo_id',
        'empleado_id',
        'total_dias_laborables',
        'total_dias_trabajados',
        'total_dias_falta',
        'total_dias_incidencia',
        'total_tardanzas',
        'total_minutos_tardanza',
        'total_horas_trabajadas',
        'total_horas_programadas',
        'total_horas_diferencia',
        'total_horas_extra_25',
        'total_horas_extra_50',
        'total_horas_extra_100',
        'detalle_incidencias',
        'detalle_tardanzas_por_dia',
        'detalle_faltas',
        'primer_marcacion',
        'ultima_marcacion',
        'estado',
        'observaciones',
        'metadatos',
    ];

    protected $casts = [
        'total_dias_laborables' => 'integer',
        'total_dias_trabajados' => 'integer',
        'total_dias_falta' => 'integer',
        'total_dias_incidencia' => 'integer',
        'total_tardanzas' => 'integer',
        'total_minutos_tardanza' => 'integer',
        'total_horas_trabajadas' => 'decimal:2',
        'total_horas_programadas' => 'decimal:2',
        'total_horas_diferencia' => 'decimal:2',
        'total_horas_extra_25' => 'decimal:2',
        'total_horas_extra_50' => 'decimal:2',
        'total_horas_extra_100' => 'decimal:2',
        'detalle_incidencias' => 'array',
        'detalle_tardanzas_por_dia' => 'array',
        'detalle_faltas' => 'array',
        'metadatos' => 'array',
        'primer_marcacion' => 'date',
        'ultima_marcacion' => 'date',
    ];

    /**
     * Relaciones
     */
    public function periodo(): BelongsTo
    {
        return $this->belongsTo(PeriodoPlanilla::class, 'periodo_id');
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    /**
     * Scopes
     */
    public function scopePorPeriodo($query, $periodoId)
    {
        return $query->where('periodo_id', $periodoId);
    }

    public function scopePorEmpleado($query, $empleadoId)
    {
        return $query->where('empleado_id', $empleadoId);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeValidados($query)
    {
        return $query->where('estado', 'validado');
    }

    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobado');
    }

    public function scopeConFaltas($query)
    {
        return $query->where('total_dias_falta', '>', 0);
    }

    public function scopeConTardanzas($query)
    {
        return $query->where('total_tardanzas', '>', 0);
    }

    /**
     * Métodos útiles
     */
    public function getPorcentajeAsistenciaAttribute(): float
    {
        if ($this->total_dias_laborables == 0) {
            return 100;
        }
        return round(($this->total_dias_trabajados / $this->total_dias_laborables) * 100, 2);
    }

    public function getTotalHorasExtraAttribute(): float
    {
        return $this->total_horas_extra_25 + $this->total_horas_extra_50 + $this->total_horas_extra_100;
    }

    public function getPromedioTardanzaAttribute(): float
    {
        if ($this->total_tardanzas == 0) {
            return 0;
        }
        return round($this->total_minutos_tardanza / $this->total_tardanzas, 2);
    }

    public function tieneProblemas(): bool
    {
        return $this->total_dias_falta > 0 || $this->total_tardanzas > 3;
    }

    public function esPerfecto(): bool
    {
        return $this->total_dias_falta == 0 && $this->total_tardanzas == 0 && $this->total_dias_trabajados == $this->total_dias_laborables;
    }

    /**
     * Exportar a array para planilla
     */
    public function toExportArray(): array
    {
        return [
            'codigo_empleado' => $this->empleado->codigo_empleado ?? '',
            'nombre_completo' => $this->empleado->nombre_completo ?? '',
            'departamento' => $this->empleado->departamento->nombre_departamento ?? '',
            'dias_laborables' => $this->total_dias_laborables,
            'dias_trabajados' => $this->total_dias_trabajados,
            'dias_falta' => $this->total_dias_falta,
            'tardanzas' => $this->total_tardanzas,
            'minutos_tardanza' => $this->total_minutos_tardanza,
            'horas_trabajadas' => $this->total_horas_trabajadas,
            'horas_programadas' => $this->total_horas_programadas,
            'horas_extra_25' => $this->total_horas_extra_25,
            'horas_extra_50' => $this->total_horas_extra_50,
            'horas_extra_100' => $this->total_horas_extra_100,
            'porcentaje_asistencia' => $this->porcentaje_asistencia,
        ];
    }
}

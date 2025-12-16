<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodoPlanilla extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'periodos_planilla';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'codigo',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'descripcion',
        'cerrado_por',
        'fecha_cierre',
        'total_empleados_procesados',
        'observaciones',
        'configuracion',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_cierre' => 'datetime',
        'configuracion' => 'array',
        'total_empleados_procesados' => 'integer',
    ];

    /**
     * Relaciones
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }

    public function resumenes(): HasMany
    {
        return $this->hasMany(ResumenMensualAsistencia::class, 'periodo_id');
    }

    /**
     * Scopes
     */
    public function scopeAbiertos($query)
    {
        return $query->where('estado', 'abierto');
    }

    public function scopeCerrados($query)
    {
        return $query->where('estado', 'cerrado');
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha_inicio', [$inicio, $fin])
            ->orWhereBetween('fecha_fin', [$inicio, $fin]);
    }

    /**
     * Métodos útiles
     */
    public function puedeEditarse(): bool
    {
        return $this->estado === 'abierto';
    }

    public function puedeCerrarse(): bool
    {
        return $this->estado === 'abierto';
    }

    public function estaProcesando(): bool
    {
        return $this->estado === 'procesando';
    }

    public function estaCerrado(): bool
    {
        return $this->estado === 'cerrado';
    }

    public function getDuracionDiasAttribute(): int
    {
        return $this->fecha_inicio->diffInDays($this->fecha_fin) + 1;
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} ({$this->codigo})";
    }

    /**
     * Generar código automático
     */
    public static function generarCodigo($fechaInicio)
    {
        $fecha = \Carbon\Carbon::parse($fechaInicio);
        return $fecha->format('Y-m');
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generar código si no existe
        static::creating(function ($periodo) {
            if (empty($periodo->codigo)) {
                $periodo->codigo = self::generarCodigo($periodo->fecha_inicio);
            }
        });
    }
}

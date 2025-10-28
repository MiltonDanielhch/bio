<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionHorario extends Model
{
    use HasFactory;

    protected $table = 'asignacion_horarios';

    protected $fillable = [
        'empleado_id',
        'horario_id',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'activo'       => 'boolean',
    ];

    /* ---------------- relaciones ---------------- */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    /* ---------------- scopes ---------------- */
    public function scopeVigentes($query)
    {
        return $query->where('activo', true)
                     ->where(fn($q) => $q->whereNull('fecha_fin')
                                          ->orWhere('fecha_fin', '>=', today()));
    }

    public function scopeParaEmpleado($query, int $empleadoId)
    {
        return $query->where('empleado_id', $empleadoId);
    }

    public function scopeEnFecha($query, string $fecha)
    {
        return $query->where('fecha_inicio', '<=', $fecha)
                     ->where(fn($q) => $q->whereNull('fecha_fin')
                                          ->orWhere('fecha_fin', '>=', $fecha));
    }
}

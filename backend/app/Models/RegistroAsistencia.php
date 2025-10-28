<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistroAsistencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'registros_asistencia';

    protected $fillable = [
        'empleado_id',
        'dispositivo_id',
        'tipo_marcaje',
        'fecha_local',
        'hora_local',
        'fecha_hora',
        'tipo_verificacion',
        'latitud',
        'longitud',
        'precision_ubicacion',
        'confianza_verificacion',
        'procesado',
        'incidencia_id',
        'estado_validacion',
        'observaciones',
    ];

    protected $casts = [
        'fecha_local'             => 'date',
        'hora_local'              => 'datetime:H:i',
        'fecha_hora'              => 'datetime',
        'latitud'                 => 'decimal:8',
        'longitud'                => 'decimal:8',
        'precision_ubicacion'     => 'decimal:2',
        'confianza_verificacion'  => 'decimal:2',
        'procesado'               => 'boolean',
        // 'tipo_marcaje'            => \App\Enums\TipoMarcajeEnum::class,         // entrada | salida | ...
        // 'tipo_verificacion'       => \App\Enums\TipoVerificacionEnum::class,    // huella | rostro | ...
        // 'estado_validacion'       => \App\Enums\EstadoValidacionEnum::class,    // pendiente | validado | ...
    ];

    /* ---------------- relaciones ---------------- */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class);
    }

    public function incidencia()
    {
        return $this->belongsTo(Incidencia::class);
    }

    /* ---------------- scopes rápidos ---------------- */
    public function scopePendientes($query)
    {
        return $query->where('estado_validacion', 'pendiente');
    }

    public function scopeNoProcesados($query)
    {
        return $query->where('procesado', false);
    }

    public function scopeDelDia($query, string $fecha)
    {
        return $query->where('fecha_local', $fecha);
    }

    public function scopePorEmpleado($query, int $empleadoId)
    {
        return $query->where('empleado_id', $empleadoId);
    }
}

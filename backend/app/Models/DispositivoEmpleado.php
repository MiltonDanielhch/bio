<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispositivoEmpleado extends Model
{
    use HasFactory;

    protected $table = 'dispositivo_empleado';

    protected $fillable = [
        'empleado_id',
        'dispositivo_id',
        'zk_user_id',
        'privilegio',
        'tarjeta_id',
        'estado',
        'estado_sincronizacion',
        'ultima_sincronizacion',
    ];

    protected $casts = [
        'zk_user_id'            => 'integer',
        'ultima_sincronizacion' => 'datetime',
        // 'estado'                => \App\Enums\EstadoDispositivoEmpleadoEnum::class,      // activo | eliminado_en_device
        // 'estado_sincronizacion' => \App\Enums\EstadoSyncEnum::class, // pendiente | sincronizado | error
    ];

    /* ---------------- relaciones ---------------- */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class);
    }

    /* ---------------- scopes útiles ---------------- */
    public function scopePendientes($query)
    {
        return $query->where('estado_sincronizacion', 'pendiente');
    }

    public function scopeSincronizados($query)
    {
        return $query->where('estado_sincronizacion', 'sincronizado');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}

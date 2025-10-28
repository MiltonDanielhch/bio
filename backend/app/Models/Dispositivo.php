<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dispositivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dispositivos';

    protected $fillable = [
        'sucursal_id',
        'nombre_dispositivo',
        'tipo',
        'numero_serie',
        'direccion_ip',
        'puerto',
        'password',
        'ubicacion',
        'estado',
        'ultima_conexion',
        'version_firmware',
        'creado_por',
        'ultimo_user_id',
    ];

    protected $casts = [
        'puerto'             => 'integer',
        'password'           => 'integer',
        'ultima_conexion'    => 'datetime',
        'ultimo_user_id'     => 'integer',
        // 'estado'             => \App\Enums\EstadoDispositivoEnum::class, // o 'string'
        // 'tipo'               => \App\Enums\TipoDispositivoEnum::class,   // o 'string'
    ];

    /* ---------------- relaciones ---------------- */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function creador()
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por');
    }

    public function dispositivoEmpleados()
    {
        return $this->hasMany(DispositivoEmpleado::class);
    }

    public function registrosAsistencia()
    {
        return $this->hasMany(RegistroAsistencia::class);
    }

    /* ---------------- scopes ---------------- */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorIp($query, string $ip)
    {
        return $query->where('direccion_ip', $ip);
    }

    /* ---------------- helpers ---------------- */
    public function endpointZk(): string
    {
        return "tcp://{$this->direccion_ip}:{$this->puerto}";
    }
}

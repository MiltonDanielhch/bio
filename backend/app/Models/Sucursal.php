<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sucursal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sucursales';

    protected $fillable = [
        'empresa_id',
        'nombre_sucursal',
        'direccion',
        'ciudad',
        'pais',
        'zona_horaria',
        'latitud',
        'longitud',
        'estado',
        'creado_por',
    ];

    protected $casts = [
        'latitud'  => 'float',
        'longitud' => 'float',
        'estado'   => 'string' // o 'string' si no usas enum
    ];

    /* -------------------------------------------------------------
     * RELACIONES
     * ------------------------------------------------------------- */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function creador()
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por');
    }

    public function departamentos()
    {
        return $this->hasMany(Departamento::class);
    }

    public function dispositivos()
    {
        return $this->hasMany(Dispositivo::class);
    }
}

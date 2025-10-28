<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'departamentos';

    protected $fillable = [
        'sucursal_id',
        'nombre_departamento',
        'descripcion',
        'jefe_empleado_id',
        'creado_por',
        'estado',
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    /* -------------------------------------------------------------
     * RELACIONES
     * ------------------------------------------------------------- */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function jefe()
    {
        return $this->belongsTo(Empleado::class, 'jefe_empleado_id');
    }

    public function creador()
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por');
    }

    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}

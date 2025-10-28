<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'empresas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre_empresa',
        'ruc',
        'direccion',
        'telefono',
        'email',
        'logo',
        'color_primario',
        'estado',
        'creado_por',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
      'estado' => 'string'
    ];

    /* -----------------------------------------------------------------
     * RELACIONES
     * ----------------------------------------------------------------- */
    public function creador()
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por');
    }

    public function sucursales()
    {
        return $this->hasMany(\App\Models\Sucursal::class);
    }

    public function empleados()
    {
        return $this->hasMany(\App\Models\Empleado::class);
    }

    public function horarios()
    {
        return $this->hasMany(\App\Models\Horario::class);
    }

    public function reportes()
    {
        return $this->hasMany(\App\Models\ReporteAsistencia::class);
    }

    public function logs()
    {
        return $this->hasMany(\App\Models\LogSistema::class);
    }
}

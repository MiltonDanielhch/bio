<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'empleados';

    protected $fillable = [
        'empresa_id',
        'departamento_id',
        'codigo_empleado',
        'dni',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'genero',
        'email',
        'telefono',
        'direccion',
        'fecha_contratacion',
        'tipo_contrato',
        'estado',
        'foto_perfil',
        'user_id',
        'creado_por',
    ];

    protected $casts = [
        'fecha_nacimiento'   => 'date',
        'fecha_contratacion' => 'date',
        'genero'             => 'string',
        'tipo_contrato'      => 'string', // o 'string'
        'estado'             => 'string' // o 'string'
    ];

    /* ---------------- relaciones ---------------- */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function creador()
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por');
    }

    public function huellas()
    {
        return $this->hasMany(Huella::class);
    }

    public function rostros()
    {
        return $this->hasMany(Rostro::class);
    }

    public function registrosAsistencia()
    {
        return $this->hasMany(RegistroAsistencia::class);
    }

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }

    public function asignacionesHorario()
    {
        return $this->hasMany(AsignacionHorario::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'horarios';

    protected $fillable = [
        'nombre',
        'hora_entrada',
        'hora_salida',
        'tolerancia_minutos',
        'dias_laborales',
        'creado_por',
    ];

    protected $casts = [
        'tolerancia_minutos'    => 'integer',
        'dias_laborales'        => 'array',
    ];

    /* ---------------- relaciones ---------------- */
    public function creador()
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por');
    }

    public function asignacionesHorario()
    {
        return $this->hasMany(AsignacionHorario::class);
    }

    /* ---------------- helpers ---------------- */
    public function esDiaLaboral(string $nombreDiaEnIngles): bool
    {
        // Aseguramos que la comparación sea siempre en minúsculas
        return in_array(strtolower($nombreDiaEnIngles), $this->dias_laborales ?? []);
    }
}

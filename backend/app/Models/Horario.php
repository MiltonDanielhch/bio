<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horarios';

    protected $fillable = [
        'empresa_id',
        'nombre_horario',
        'hora_entrada',
        'hora_salida',
        'hora_entrada_almuerzo',
        'hora_salida_almuerzo',
        'tolerancia_entrada',
        'tolerancia_salida',
        'dias_laborales',
        'flexible',
        'nocturno',
        'creado_por',
    ];

    protected $casts = [
        'hora_entrada'          => 'datetime:H:i',
        'hora_salida'           => 'datetime:H:i',
        'hora_entrada_almuerzo' => 'datetime:H:i',
        'hora_salida_almuerzo'  => 'datetime:H:i',
        'tolerancia_entrada'    => 'integer',
        'tolerancia_salida'     => 'integer',
        'dias_laborales'        => 'array',          // ['Lunes','Martes',...]
        'flexible'              => 'boolean',
        'nocturno'              => 'boolean',
    ];

    /* ---------------- relaciones ---------------- */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function creador()
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionHorario::class);
    }

    /* ---------------- helpers ---------------- */
    public function esDiaLaboral(string $nombreDia): bool
    {
        return in_array($nombreDia, $this->dias_laborales ?? []);
    }

    public function rangoAlmuerzoMinutos(): ?int
    {
        if (!$this->hora_entrada_almuerzo || !$this->hora_salida_almuerzo) {
            return null;
        }
        return $this->hora_entrada_almuerzo->diffInMinutes($this->hora_salida_almuerzo);
    }
}

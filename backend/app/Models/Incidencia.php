<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incidencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'incidencias';

    protected $fillable = [
        'empleado_id',
        'tipo_incidencia_id',
        'fecha_incidencia',
        'hora_incidencia',
        'motivo',
        'observaciones',
        'evidencia',
        'estado',
        'aprobado_por',
        'aprobado_en',
        'creado_por',
    ];

    protected $casts = [
        'fecha_incidencia' => 'date',
        'hora_incidencia'  => 'datetime:H:i',
        'aprobado_en'      => 'datetime',
        // 'estado'           => \App\Enums\EstadoIncidenciaEnum::class, // pendiente | aprobado | rechazado
    ];

    /* ---------------- relaciones ---------------- */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function tipo()
    {
        return $this->belongsTo(TipoIncidencia::class, 'tipo_incidencia_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(\App\Models\User::class, 'aprobado_por');
    }

    public function creador()
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por');
    }

    /* ---------------- scopes ---------------- */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobado');
    }

    public function scopeDelMes($query, string $yearMonth)
    {
        // $yearMonth = '2025-09'
        return $query->whereYear('fecha_incidencia', substr($yearMonth, 0, 4))
                     ->whereMonth('fecha_incidencia', substr($yearMonth, 5, 2));
    }
}

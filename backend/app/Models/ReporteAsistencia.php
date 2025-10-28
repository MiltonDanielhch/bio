<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteAsistencia extends Model
{
    use HasFactory;

    protected $table = 'reportes_asistencia';

    protected $fillable = [
        'empresa_id',
        'nombre_reporte',
        'fecha_inicio',
        'fecha_fin',
        'tipo',
        'filtros',
        'generado_por',
        'archivo_path',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'filtros'      => 'array',
        // 'estado'       => \App\Enums\EstadoReporteEnum::class, // procesando | completado | error
    ];

    /* ---------------- relaciones ---------------- */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function generador()
    {
        return $this->belongsTo(\App\Models\User::class, 'generado_por');
    }

    /* ---------------- scopes ---------------- */
    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completado');
    }

    public function scopeRecientes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /* ---------------- helpers ---------------- */
    public function nombreArchivo(): string
    {
        return basename($this->archivo_path ?? '');
    }

    public function urlDescarga(): string
    {
        return $this->archivo_path
            ? \Storage::disk('public')->url($this->archivo_path)
            : '#';
    }
}

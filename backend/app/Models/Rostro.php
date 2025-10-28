<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rostro extends Model
{
    use HasFactory;

    protected $table = 'rostros';

    protected $fillable = [
        'empleado_id',
        'zk_user_id',
        'template_rostro',
        'foto_rostro',
        'calidad',
        'estado',
    ];

    protected $casts = [
        'zk_user_id'      => 'integer',
        // 'calidad'         => \App\Enums\CalidadRostroEnum::class, // alta | media | baja
        // 'estado'          => \App\Enums\EstadoRostroEnum::class,  // activo | inactivo
    ];

    /* ---------------- relaciones ---------------- */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    /* ---------------- scopes ---------------- */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorZkUser($query, int $zkUserId)
    {
        return $query->where('zk_user_id', $zkUserId);
    }

    /* ---------------- helpers ---------------- */
    public function getTemplateBase64Attribute(): string
    {
        return base64_encode($this->template_rostro);
    }

    public function setTemplateFromBase64(string $b64): void
    {
        $this->template_rostro = base64_decode($b64);
    }
}

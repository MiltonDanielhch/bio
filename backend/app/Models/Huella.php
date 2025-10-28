<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Huella extends Model
{
    use HasFactory;

    protected $table = 'huellas';

    /* -------------------------------------------------
     * IMPORTANTE: template_huella es binario puro
     * ------------------------------------------------- */
    protected $fillable = [
        'empleado_id',
        'zk_user_id',
        'template_huella',
        'numero_dedo',
        'calidad',
        'formato_template',
        'estado',
    ];

    /* -------------------------------------------------
     * Evitamos que Eloquent toque el campo binario
     * ------------------------------------------------- */
    protected $casts = [
        'template_huella' => 'string',
        'numero_dedo'     => 'integer',
        'zk_user_id'      => 'integer',
        // 'calidad'         => \App\Enums\CalidadHuellaEnum::class,      // alta | media | baja
        // 'formato_template'=> \App\Enums\FormatoTemplateEnum::class,    // iso | ansi | zk
        // 'estado'          => \App\Enums\EstadoHuellaEnum::class,       // activo | inactivo
    ];

    public $timestamps = true;

    /* ---------------- relaciones ---------------- */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    /* ---------------- scopes ---------------- */
    public function scopeActivas($query)
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
        return base64_encode($this->template_huella);
    }

    public function setTemplateFromBase64(string $b64): void
    {
        $this->template_huella = base64_decode($b64);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSistema extends Model
{
    use HasFactory;

    protected $table = 'logs_sistema';

    protected $fillable = [
        'accion',
        'descripcion',
        'datos_antes',
        'datos_despues',
        'tabla_afectada',
        'usuario_id',
        'empresa_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'datos_antes'  => 'array',
        'datos_despues'=> 'array',
    ];

    public $timestamps = true;

    /* ---------------- relaciones ---------------- */
    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /* ---------------- scopes ---------------- */
    public function scopePorTabla($query, string $tabla)
    {
        return $query->where('tabla_afectada', $tabla);
    }

    public function scopePorUsuario($query, int $userId)
    {
        return $query->where('usuario_id', $userId);
    }

    public function scopePorEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /* ---------------- helpers ---------------- */
    public static function registrar(
        string $accion,
        string $descripcion,
        ?array $antes = null,
        ?array $despues = null,
        ?string $tabla = null,
        ?int $usuarioId = null,
        ?int $empresaId = null
    ): self {
        return self::create([
            'accion'          => $accion,
            'descripcion'     => $descripcion,
            'datos_antes'     => $antes,
            'datos_despues'   => $despues,
            'tabla_afectada'  => $tabla,
            'usuario_id'      => $usuarioId ?? auth()->id(),
            'empresa_id'      => $empresaId ?? auth()->user()?->empresa_id,
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
        ]);
    }
}

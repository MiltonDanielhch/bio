<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoIncidencia extends Model
{
    use HasFactory;

    protected $table = 'tipos_incidencia';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /* ---------------- relaciones ---------------- */
    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }
}

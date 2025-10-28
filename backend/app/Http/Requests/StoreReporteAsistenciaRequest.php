<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreReporteAsistenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', \App\Models\ReporteAsistencia::class);
    }

    public function rules(): array
    {
        return [
            'empresa_id'   => 'required|exists:empresas,id',
            'fecha_inicio' => 'required|date|before_or_equal:fecha_fin',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            'tipo'         => 'required|in:diario,semanal,mensual,custom',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateAsignacionHorarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('asignacionHorario'));
    }

    public function rules(): array
    {
        return [
            'empleado_id'  => 'required|exists:empleados,id',
            'horario_id'   => 'required|exists:horarios,id',
            'fecha_inicio' => 'required|date|before_or_equal:fecha_fin',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'activo'       => 'nullable|boolean',
        ];
    }
}

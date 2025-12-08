<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateHorarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('horario'));
    }

    public function rules(): array
    {
        return [
            'nombre'                => ['required', 'string', 'max:100', Rule::unique('horarios')->ignore($this->route('horario')->id)],
            'hora_entrada'          => 'required|date_format:H:i',
            'hora_salida'           => 'required|date_format:H:i|after:hora_entrada',
            'tolerancia_minutos'    => 'nullable|integer|min:0|max:60',
            'dias_laborales'        => 'required|array|min:1|max:7',
            'dias_laborales.*'      => 'in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateHorarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('horario'));
    }

    public function rules(): array
    {
        return [
            'empresa_id'            => 'required|exists:empresas,id',
            'nombre_horario'        => 'required|string|max:100',
            'hora_entrada'          => 'required|date_format:H:i',
            'hora_salida'           => 'required|date_format:H:i|after:hora_entrada',
            'hora_entrada_almuerzo' => 'nullable|date_format:H:i|before:hora_salida_almuerzo',
            'hora_salida_almuerzo'  => 'nullable|date_format:H:i|after:hora_entrada_almuerzo',
            'tolerancia_entrada'    => 'nullable|integer|min:0|max:60',
            'tolerancia_salida'     => 'nullable|integer|min:0|max:60',
            'dias_laborales'        => 'required|array|min:1|max:7',
            'dias_laborales.*'      => 'in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'flexible'              => 'nullable|boolean',
            'nocturno'              => 'nullable|boolean',
        ];
    }
}

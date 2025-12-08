<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class UpdateRegistroAsistenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $registro = $this->route('registro');
        return $this->user()->can('update', $registro);
    }

    public function rules(): array
    {
        return [
            'empleado_id' => 'required|exists:empleados,id',
            'dispositivo_id' => 'required|exists:dispositivos,id',
            'tipo_marcaje' => 'required|in:entrada,salida,entrada_almuerzo,salida_almuerzo,general',
            'fecha_local' => 'required|date',
            'hora_local' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'],
            'tipo_verificacion' => 'required|in:huella,rostro,tarjeta,manual,clave',
            'observaciones' => 'nullable|string',
        ];
    }

    public function getValidatedData(): array
    {
        $validated = $this->validated();
        $validated['fecha_hora'] = Carbon::parse($validated['fecha_local'] . ' ' . $validated['hora_local'])->toDateTimeString();
        return $validated;
    }
}

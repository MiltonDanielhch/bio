<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreAsignacionHorarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', \App\Models\AsignacionHorario::class);
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $empleadoId = $this->empleado_id;
            $fechaInicio = $this->fecha_inicio;
            $fechaFin = $this->fecha_fin;

            $conflict = \App\Models\AsignacionHorario::where('empleado_id', $empleadoId)
                ->where('activo', true)
                ->where(function ($q) use ($fechaInicio, $fechaFin) {
                    $q->whereNull('fecha_fin')
                      ->orWhere('fecha_fin', '>=', $fechaInicio);
                })
                ->when($fechaFin, fn($q) => $q->where('fecha_inicio', '<=', $fechaFin))
                ->exists();

            if ($conflict) {
                $validator->errors()->add('fecha_inicio', 'El empleado ya tiene un horario activo en ese rango.');
            }
        });
    }
}

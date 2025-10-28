<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateDepartamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('departamento'));
    }

    public function rules(): array
    {
        return [
            'sucursal_id'          => 'required|exists:sucursales,id',
            'nombre_departamento'  => 'required|string|max:100',
            'descripcion'          => 'nullable|string|max:255',
            'jefe_empleado_id'     => 'nullable|exists:empleados,id',
        ];
    }
}

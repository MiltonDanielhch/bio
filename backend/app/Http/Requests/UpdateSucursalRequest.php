<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateSucursalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('sucursal'));
    }

    public function rules(): array
    {
        return [
            'empresa_id'       => 'required|exists:empresas,id',
            'nombre_sucursal'  => 'required|string|max:100',
            'direccion'        => 'nullable|string|max:255',
            'ciudad'           => 'nullable|string|max:100',
            'pais'             => 'nullable|string|max:100',
            'zona_horaria'     => 'nullable|string|max:50',
            'latitud'          => 'nullable|numeric|between:-90,90',
            'longitud'         => 'nullable|numeric|between:-180,180',
            'estado'           => 'nullable|in:activo,inactivo',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreEmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', \App\Models\Empresa::class);
    }

    public function rules(): array
    {
        return [
            'nombre_empresa'  => 'required|string|max:100',
            'ruc'             => 'required|string|size:25|unique:empresas,ruc',
            'direccion'       => 'nullable|string|max:255',
            'telefono'        => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:100',
            'logo'            => 'nullable|image|max:2048',
            'color_primario'  => 'nullable|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'estado'          => 'nullable|in:activo,inactivo',
        ];
    }
}

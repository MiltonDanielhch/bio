<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmpleadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Cambiar a `true` si no necesitas lógica de autorización
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'empresa_id' => 'required|exists:empresas,id',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'codigo_empleado' => 'required|string|max:50|unique:empleados',
            'dni' => 'required|string|max:20|unique:empleados',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|string',
            'email' => 'nullable|email|unique:empleados',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'fecha_contratacion' => 'required|date',
            'tipo_contrato' => ['required', 'string', 'max:50', Rule::in(['indefinido', 'plazo_fijo', 'servicios'])],
            'estado' => 'required|string',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'empresa_id.required' => 'La empresa es obligatoria.',
            'codigo_empleado.required' => 'El código de empleado es obligatorio.',
            'codigo_empleado.unique' => 'Este código de empleado ya está en uso.',
            'dni.required' => 'El DNI es obligatorio.',
            'dni.unique' => 'Este DNI ya está en uso.',
            'nombres.required' => 'Los nombres son obligatorios.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'fecha_contratacion.required' => 'La fecha de contratación es obligatoria.',
            'tipo_contrato.required' => 'El tipo de contrato es obligatorio.',
            'tipo_contrato.in' => 'El tipo de contrato seleccionado no es válido.',
            'estado.required' => 'El estado es obligatorio.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreDispositivoEmpleadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', \App\Models\DispositivoEmpleado::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'empleado_id' => [
                'required',
                'exists:empleados,id',
                Rule::unique('dispositivo_empleado')->where('dispositivo_id', $this->input('dispositivo_id'))
            ],
            'dispositivo_id' => 'required|exists:dispositivos,id',
            'zk_user_id' => [
                'required',
                'integer',
                Rule::unique('dispositivo_empleado')->where('dispositivo_id', $this->input('dispositivo_id'))
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'zk_user_id.unique' => 'El ID de usuario ya está en uso en este dispositivo.',
            'empleado_id.unique' => 'Este empleado ya está asignado a este dispositivo.',
        ];
    }
}

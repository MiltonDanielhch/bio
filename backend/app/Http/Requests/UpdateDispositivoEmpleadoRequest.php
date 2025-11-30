<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateDispositivoEmpleadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // El modelo 'map' se obtiene del route model binding
        return Gate::allows('update', $this->route('map'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $mapId = $this->route('map')->id;

        return [
            'empleado_id' => [
                'required',
                'exists:empleados,id',
                Rule::unique('dispositivo_empleado')->where('dispositivo_id', $this->dispositivo_id)->ignore($mapId)
            ],
            'dispositivo_id' => 'required|exists:dispositivos,id',
            'zk_user_id' => [
                'required',
                'integer',
                Rule::unique('dispositivo_empleado')->where('dispositivo_id', $this->dispositivo_id)->ignore($mapId)
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

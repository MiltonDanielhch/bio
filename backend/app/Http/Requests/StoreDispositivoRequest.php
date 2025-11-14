<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDispositivoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return $this->user()->can('create', \App\Models\Dispositivo::class);
        return true; // Cambiar a la línea de arriba cuando tengas la Policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sucursal_id' => 'required|exists:sucursales,id',
            'nombre_dispositivo' => 'required|string|max:255',
            'tipo' => 'required|in:huella,facial,huella_facial,tarjeta',
            'numero_serie' => 'required|string|unique:dispositivos,numero_serie',
            'direccion_ip' => 'nullable|ip|unique:dispositivos,direccion_ip',
            'puerto' => 'required|integer|min:1|max:65535',
            'password' => 'nullable|integer',
            'ubicacion' => 'nullable|string|max:255',
            'estado' => 'required|in:activo,inactivo,mantenimiento',
        ];
    }
}

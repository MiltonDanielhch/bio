<?php

namespace Database\Factories;

use App\Models\Empleado;
use App\Models\Horario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AsignacionHorario>
 */
class AsignacionHorarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'empleado_id' => Empleado::factory(),
            'horario_id' => Horario::factory(),
            'fecha_inicio' => now()->subMonth(),
            'fecha_fin' => null, // Sin fecha fin por defecto
            'activo' => true,
        ];
    }
}

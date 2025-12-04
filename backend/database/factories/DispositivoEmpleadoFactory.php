<?php

namespace Database\Factories;

use App\Models\Dispositivo;
use App\Models\Empleado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DispositivoEmpleado>
 */
class DispositivoEmpleadoFactory extends Factory
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
            'dispositivo_id' => Dispositivo::factory(),
            'zk_user_id' => $this->faker->unique()->numberBetween(1, 10000),
            'privilegio' => 'usuario',
            'estado' => 'activo',
            'estado_sincronizacion' => 'pendiente',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sucursal>
 */
class SucursalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'empresa_id' => Empresa::factory(),
            'nombre_sucursal' => $this->faker->city() . ' Branch',
            'direccion' => $this->faker->address(),
            'ciudad' => $this->faker->city(),
            'pais' => $this->faker->country(),
            'estado' => 'activo',
        ];
    }
}

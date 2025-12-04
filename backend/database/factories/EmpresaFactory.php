<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Empresa>
 */
class EmpresaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_empresa' => $this->faker->company(),
            'ruc' => $this->faker->numerify('###########'),
            'direccion' => $this->faker->address(),
            'telefono' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'estado' => 'activo',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Departamento;
use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Empleado>
 */
class EmpleadoFactory extends Factory
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
            'departamento_id' => Departamento::factory(),
            'codigo_empleado' => $this->faker->unique()->numerify('EMP###'),
            'nombres' => $this->faker->firstName(),
            'apellidos' => $this->faker->lastName(),
            'fecha_nacimiento' => $this->faker->date(),
            'fecha_contratacion' => $this->faker->date(),
            'tipo_contrato' => 'indefinido',
            'genero' => $this->faker->randomElement(['M', 'F']),
            'direccion' => $this->faker->address(),
            'telefono' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'estado' => 'activo',
            'dni' => $this->faker->unique()->numerify('########'),
            // Add other required fields if necessary based on migration constraints,
            // but these seem to be the core ones based on the user request.
        ];
    }
}

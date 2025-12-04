<?php

namespace Database\Factories;

use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dispositivo>
 */
class DispositivoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sucursal_id' => Sucursal::factory(),
            'nombre_dispositivo' => 'ZKTeco ' . $this->faker->randomNumber(3),
            'tipo' => $this->faker->randomElement(['huella', 'facial', 'huella_facial']),
            'numero_serie' => $this->faker->numerify('#######'),
            'direccion_ip' => '192.168.' . $this->faker->numberBetween(1, 254) . '.' . $this->faker->numberBetween(1, 254),
            'puerto' => 4370,
            'password' => $this->faker->randomElement([0, 123456]),
            'ubicacion' => $this->faker->city(),
            'estado' => 'activo',
        ];
    }
}

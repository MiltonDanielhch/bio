<?php

namespace Database\Factories;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Horario>
 */
class HorarioFactory extends Factory
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
            'nombre_horario' => $this->faker->randomElement(['Turno Mañana', 'Turno Tarde', 'Turno Completo', 'Turno Noche']),
            'hora_entrada' => '08:00',
            'hora_salida' => '17:00',
            'hora_entrada_almuerzo' => '12:00',
            'hora_salida_almuerzo' => '13:00',
            'tolerancia_entrada' => 15,
            'tolerancia_salida' => 15,
            'dias_laborales' => ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'],
            'flexible' => false,
            'nocturno' => false,
        ];
    }
}

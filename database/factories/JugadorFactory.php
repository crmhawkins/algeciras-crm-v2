<?php

namespace Database\Factories;

use App\Models\Jugador;
use Illuminate\Database\Eloquent\Factories\Factory;

class JugadorFactory extends Factory
{
    protected $model = Jugador::class;

    public function definition(): array
    {
        return [
            'nombre_completo' => $this->faker->name('male'),
            'dorsal' => $this->faker->unique()->numberBetween(1, 25),
            'posicion' => $this->faker->randomElement(['portero', 'defensa', 'centrocampista', 'delantero']),
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-35 years', '-18 years'),
            'nacionalidad' => 'España',
            'foto' => null,
            'biografia' => $this->faker->optional()->paragraph(),
            'altura_cm' => $this->faker->numberBetween(168, 195),
            'peso_kg' => $this->faker->numberBetween(65, 92),
            'equipo' => 'primer-equipo',
            'temporada' => '2025-2026',
            'sofascore_id' => null,
            'goles' => $this->faker->numberBetween(0, 15),
            'asistencias' => $this->faker->numberBetween(0, 12),
            'minutos_jugados' => $this->faker->numberBetween(0, 3000),
            'partidos_jugados' => $this->faker->numberBetween(0, 34),
            'tarjetas_amarillas' => $this->faker->numberBetween(0, 8),
            'tarjetas_rojas' => $this->faker->numberBetween(0, 2),
            'activo' => true,
        ];
    }
}

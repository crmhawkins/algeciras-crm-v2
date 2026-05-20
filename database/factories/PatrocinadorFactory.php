<?php

namespace Database\Factories;

use App\Models\Patrocinador;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatrocinadorFactory extends Factory
{
    protected $model = Patrocinador::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company(),
            'logo' => 'placeholder-logo.png',
            'enlace_web' => $this->faker->optional()->url(),
            'descripcion' => $this->faker->optional()->sentence(),
            'tipo' => $this->faker->randomElement(['principal', 'oficial', 'colaborador', 'proveedor']),
            'desde' => $this->faker->dateTimeBetween('-2 years', '-1 month')->format('Y-m-d'),
            'hasta' => $this->faker->dateTimeBetween('+1 month', '+2 years')->format('Y-m-d'),
            'orden' => $this->faker->numberBetween(0, 50),
            'activo' => true,
        ];
    }
}

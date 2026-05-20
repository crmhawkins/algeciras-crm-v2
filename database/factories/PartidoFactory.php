<?php

namespace Database\Factories;

use App\Models\Partido;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartidoFactory extends Factory
{
    protected $model = Partido::class;

    public function definition(): array
    {
        $esLocal = $this->faker->boolean();
        $localNombre = $esLocal ? 'Algeciras CF' : $this->faker->company();
        $visitanteNombre = $esLocal ? $this->faker->company() : 'Algeciras CF';

        return [
            'temporada' => '2025-2026',
            'jornada' => $this->faker->numberBetween(1, 38),
            'competicion' => 'Primera Federación',
            'local_id' => null,
            'visitante_id' => null,
            'local_nombre' => $localNombre,
            'visitante_nombre' => $visitanteNombre,
            'local_escudo' => null,
            'visitante_escudo' => null,
            'goles_local' => null,
            'goles_visitante' => null,
            'fecha' => $this->faker->dateTimeBetween('-3 months', '+3 months'),
            'estadio' => $esLocal ? 'Nuevo Mirador' : $this->faker->city(),
            'arbitro' => null,
            'estado' => Partido::ESTADO_PROGRAMADO,
            'compralaentrada_evento_id' => null,
            'compralaentrada_sesion_id' => null,
            'resumen_url' => null,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\BannerPublicidad;
use Illuminate\Database\Eloquent\Factories\Factory;

class BannerPublicidadFactory extends Factory
{
    protected $model = BannerPublicidad::class;

    public function definition(): array
    {
        return [
            'titulo' => $this->faker->sentence(4),
            'imagen_desktop' => 'placeholder-banner.jpg',
            'imagen_mobile' => null,
            'enlace' => $this->faker->optional()->url(),
            'posicion' => $this->faker->randomElement([
                'home_hero',
                'home_lateral',
                'tienda_top',
                'noticias_lateral',
                'app_inicio',
            ]),
            'desde' => null,
            'hasta' => null,
            'orden' => $this->faker->numberBetween(0, 50),
            'clicks' => 0,
            'impresiones' => 0,
            'activo' => true,
        ];
    }
}

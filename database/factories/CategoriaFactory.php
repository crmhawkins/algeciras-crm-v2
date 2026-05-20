<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        $nombre = $this->faker->unique()->words(2, true);

        return [
            'nombre' => ucfirst($nombre),
            'slug' => Str::slug($nombre).'-'.Str::random(5),
            'descripcion' => $this->faker->optional()->sentence(),
            'parent_id' => null,
            'imagen' => null,
            'orden' => $this->faker->numberBetween(0, 100),
            'visible_web' => true,
            'visible_app' => true,
            'visible_tpv' => true,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Noticia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NoticiaFactory extends Factory
{
    protected $model = Noticia::class;

    public function definition(): array
    {
        $titulo = $this->faker->unique()->sentence(6);

        return [
            'slug' => Str::slug($titulo).'-'.Str::random(5),
            'titulo' => $titulo,
            'extracto' => $this->faker->paragraph(),
            'contenido' => collect($this->faker->paragraphs(6))->map(fn ($p) => "<p>{$p}</p>")->implode("\n"),
            'categoria_id' => null,
            'autor_id' => null,
            'imagen_destacada' => null,
            'meta_titulo' => null,
            'meta_descripcion' => null,
            'etiquetas' => $this->faker->words(3),
            'publicada' => true,
            'publicada_en' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'vistas' => $this->faker->numberBetween(0, 5000),
            'destacada_home' => $this->faker->boolean(20),
        ];
    }
}

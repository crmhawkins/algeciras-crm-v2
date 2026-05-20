<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        $nombre = $this->faker->unique()->words(3, true);
        $precio = $this->faker->randomFloat(2, 5, 250);

        return [
            'nombre' => ucfirst($nombre),
            'slug' => Str::slug($nombre).'-'.Str::random(5),
            'descripcion_corta' => $this->faker->sentence(),
            'descripcion_larga' => $this->faker->paragraphs(3, true),
            'sku' => 'ACF-'.strtoupper(Str::random(8)),
            'categoria_id' => Categoria::factory(),
            'marca' => $this->faker->optional()->company(),
            'precio_base' => $precio,
            'precio_oferta' => $this->faker->boolean(20) ? round($precio * 0.8, 2) : null,
            'coste' => round($precio * 0.5, 2),
            'iva_pct' => 21,
            'stock' => $this->faker->numberBetween(0, 100),
            'stock_minimo' => 5,
            'peso_gramos' => $this->faker->numberBetween(100, 2000),
            'es_destacado' => $this->faker->boolean(20),
            'es_novedad' => $this->faker->boolean(15),
            'visible_web' => true,
            'visible_app' => true,
            'visible_tpv' => true,
            'requiere_envio' => true,
            'meta_titulo' => null,
            'meta_descripcion' => null,
        ];
    }
}

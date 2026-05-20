<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'Equipaciones',
            'Merchandising',
            'Coleccionismo',
            'Niños',
            'Mujer',
        ];

        foreach ($categorias as $index => $nombre) {
            Categoria::updateOrCreate(
                ['slug' => Str::slug($nombre)],
                [
                    'nombre' => $nombre,
                    'descripcion' => null,
                    'parent_id' => null,
                    'orden' => $index + 1,
                    'visible_web' => true,
                    'visible_app' => true,
                    'visible_tpv' => true,
                ],
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\MetodoPago;
use Illuminate\Database\Seeder;

class MetodoPagoSeeder extends Seeder
{
    public function run(): void
    {
        $metodos = [
            [
                'codigo' => 'redsys',
                'nombre' => 'Tarjeta (Redsys)',
                'disponible_web' => true,
                'disponible_app' => true,
                'disponible_tpv' => false,
                'orden' => 1,
            ],
            [
                'codigo' => 'bizum',
                'nombre' => 'Bizum',
                'disponible_web' => true,
                'disponible_app' => true,
                'disponible_tpv' => false,
                'orden' => 2,
            ],
            [
                'codigo' => 'transferencia',
                'nombre' => 'Transferencia bancaria',
                'disponible_web' => true,
                'disponible_app' => false,
                'disponible_tpv' => false,
                'orden' => 3,
            ],
            [
                'codigo' => 'efectivo',
                'nombre' => 'Efectivo',
                'disponible_web' => false,
                'disponible_app' => false,
                'disponible_tpv' => true,
                'orden' => 4,
            ],
            [
                'codigo' => 'datafono_tpv',
                'nombre' => 'Datáfono TPV',
                'disponible_web' => false,
                'disponible_app' => false,
                'disponible_tpv' => true,
                'orden' => 5,
            ],
        ];

        foreach ($metodos as $m) {
            MetodoPago::updateOrCreate(
                ['codigo' => $m['codigo']],
                array_merge($m, [
                    'activo' => true,
                    'comision_pct' => 0,
                ]),
            );
        }
    }
}

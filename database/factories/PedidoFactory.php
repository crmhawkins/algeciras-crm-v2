<?php

namespace Database\Factories;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PedidoFactory extends Factory
{
    protected $model = Pedido::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 10, 300);
        $iva = round($subtotal * 0.21, 2);
        $gastos = $this->faker->randomElement([0, 4.95, 6.95]);
        $total = round($subtotal + $iva + $gastos, 2);

        return [
            'codigo' => 'ACF-'.now()->year.'-'.strtoupper(Str::random(6)),
            'canal' => $this->faker->randomElement(['web', 'app', 'tpv']),
            'estado' => Pedido::ESTADO_BORRADOR,
            'cliente_id' => User::factory(),
            'nombre_cliente' => $this->faker->name(),
            'email_cliente' => $this->faker->safeEmail(),
            'telefono_cliente' => $this->faker->phoneNumber(),
            'direccion_envio' => $this->faker->streetAddress(),
            'cp_envio' => $this->faker->postcode(),
            'ciudad_envio' => 'Algeciras',
            'provincia_envio' => 'Cádiz',
            'pais_envio' => 'ES',
            'subtotal' => $subtotal,
            'descuento' => 0,
            'iva' => $iva,
            'gastos_envio' => $gastos,
            'total' => $total,
            'metodo_pago' => 'redsys',
            'referencia_pago' => null,
            'pagado_en' => null,
            'cancelado_en' => null,
            'notas_internas' => null,
            'notas_cliente' => null,
            'cajero_id' => null,
        ];
    }
}

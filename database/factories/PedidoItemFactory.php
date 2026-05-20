<?php

namespace Database\Factories;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoItemFactory extends Factory
{
    protected $model = PedidoItem::class;

    public function definition(): array
    {
        $producto = Producto::factory()->create();
        $cantidad = $this->faker->numberBetween(1, 4);
        $precio = (float) $producto->precio_base;
        $subtotal = round($precio * $cantidad, 2);
        $iva = 21;
        $total = round($subtotal * (1 + $iva / 100), 2);

        return [
            'pedido_id' => Pedido::factory(),
            'producto_id' => $producto->id,
            'variante_id' => null,
            'sku' => $producto->sku,
            'nombre_producto' => $producto->nombre,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'descuento_pct' => 0,
            'iva_pct' => $iva,
            'subtotal_linea' => $subtotal,
            'total_linea' => $total,
        ];
    }
}

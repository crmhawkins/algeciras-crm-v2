<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PedidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'canal' => $this->canal,
            'estado' => $this->estado,
            'cliente' => [
                'id' => $this->cliente_id,
                'nombre' => $this->nombre_cliente,
                'email' => $this->email_cliente,
                'telefono' => $this->telefono_cliente,
            ],
            'envio' => [
                'direccion' => $this->direccion_envio,
                'cp' => $this->cp_envio,
                'ciudad' => $this->ciudad_envio,
                'provincia' => $this->provincia_envio,
                'pais' => $this->pais_envio,
            ],
            'importes' => [
                'subtotal' => (float) $this->subtotal,
                'descuento' => (float) $this->descuento,
                'iva' => (float) $this->iva,
                'gastos_envio' => (float) $this->gastos_envio,
                'total' => (float) $this->total,
            ],
            'metodo_pago' => $this->metodo_pago,
            'referencia_pago' => $this->referencia_pago,
            'pagado_en' => optional($this->pagado_en)?->toIso8601String(),
            'created_at' => optional($this->created_at)?->toIso8601String(),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($i) => [
                'id' => $i->id,
                'producto_id' => $i->producto_id,
                'variante_id' => $i->variante_id,
                'sku' => $i->sku,
                'nombre' => $i->nombre_producto,
                'cantidad' => $i->cantidad,
                'precio_unitario' => (float) $i->precio_unitario,
                'subtotal_linea' => (float) $i->subtotal_linea,
                'total_linea' => (float) $i->total_linea,
            ])),
        ];
    }
}

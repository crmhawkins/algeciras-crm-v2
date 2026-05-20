<?php

namespace App\Observers;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Services\StockService;

class PedidoItemObserver
{
    public function __construct(private readonly StockService $stock) {}

    /**
     * When a pedido_item is created AND the parent pedido is already paid,
     * decrement stock. Most commonly, items are created in 'borrador' state
     * and stock is decremented when PedidoObserver detects transition to 'pagado'.
     */
    public function created(PedidoItem $item): void
    {
        $pedido = $item->pedido;

        if (! $pedido || ! $pedido->esPagado()) {
            return;
        }

        $this->stock->decrementar(
            producto: $item->producto,
            variante: $item->variante,
            cantidad: (int) $item->cantidad,
            tipo: $this->stock->tipoMovimientoVenta($pedido->canal),
            referencia: $pedido,
            motivo: "Venta {$pedido->canal} pedido {$pedido->codigo}",
        );
    }
}

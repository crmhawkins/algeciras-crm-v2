<?php

namespace App\Observers;

use App\Models\Pedido;
use App\Services\StockService;

class PedidoObserver
{
    public function __construct(private readonly StockService $stock) {}

    /**
     * Reactive stock management:
     * - transition to a "paid-like" state → decrement stock for each item
     * - transition to cancelado/devuelto AFTER being paid → rollback (re-increment)
     */
    public function updated(Pedido $pedido): void
    {
        if (! $pedido->wasChanged('estado')) {
            return;
        }

        $estadoAnterior = $pedido->getOriginal('estado');
        $estadoNuevo = $pedido->estado;

        $estadosPagados = [
            Pedido::ESTADO_PAGADO,
            Pedido::ESTADO_PREPARANDO,
            Pedido::ESTADO_ENVIADO,
            Pedido::ESTADO_ENTREGADO,
        ];

        $entrandoEnPagado = ! in_array($estadoAnterior, $estadosPagados, true)
            && in_array($estadoNuevo, $estadosPagados, true);

        $saliendoDePagado = in_array($estadoAnterior, $estadosPagados, true)
            && in_array($estadoNuevo, [Pedido::ESTADO_CANCELADO, Pedido::ESTADO_DEVUELTO], true);

        if ($entrandoEnPagado) {
            $this->descontarStock($pedido);
            if (! $pedido->pagado_en) {
                $pedido->updateQuietly(['pagado_en' => now()]);
            }
        }

        if ($saliendoDePagado) {
            $this->devolverStock($pedido, $estadoNuevo);
            if ($estadoNuevo === Pedido::ESTADO_CANCELADO && ! $pedido->cancelado_en) {
                $pedido->updateQuietly(['cancelado_en' => now()]);
            }
        }
    }

    private function descontarStock(Pedido $pedido): void
    {
        $tipo = $this->stock->tipoMovimientoVenta($pedido->canal);

        foreach ($pedido->items as $item) {
            $this->stock->decrementar(
                producto: $item->producto,
                variante: $item->variante,
                cantidad: (int) $item->cantidad,
                tipo: $tipo,
                referencia: $pedido,
                motivo: "Pago confirmado pedido {$pedido->codigo}",
            );
        }
    }

    private function devolverStock(Pedido $pedido, string $estadoNuevo): void
    {
        $tipo = $estadoNuevo === Pedido::ESTADO_DEVUELTO ? 'devolucion' : 'entrada';
        $motivo = $estadoNuevo === Pedido::ESTADO_DEVUELTO
            ? "Devolución pedido {$pedido->codigo}"
            : "Cancelación pedido {$pedido->codigo}";

        foreach ($pedido->items as $item) {
            $this->stock->incrementar(
                producto: $item->producto,
                variante: $item->variante,
                cantidad: (int) $item->cantidad,
                tipo: $tipo,
                referencia: $pedido,
                motivo: $motivo,
            );
        }
    }
}

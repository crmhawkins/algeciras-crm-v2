<?php

namespace App\Observers;

use App\Models\Producto;
use App\Models\StockMovimiento;

class ProductoObserver
{
    /**
     * When stock is changed directly (outside StockService), log an "ajuste" movement
     * so the audit trail remains complete. StockService writes its own movements and
     * marks the model so we skip duplicate logging.
     */
    public function updating(Producto $producto): void
    {
        if (! $producto->isDirty('stock')) {
            return;
        }

        if ($producto->stockServiceManaged ?? false) {
            return;
        }

        $stockAntes = (int) $producto->getOriginal('stock');
        $stockDespues = (int) $producto->stock;

        StockMovimiento::create([
            'producto_id' => $producto->id,
            'variante_id' => null,
            'tipo' => 'ajuste',
            'cantidad' => $stockDespues - $stockAntes,
            'stock_antes' => $stockAntes,
            'stock_despues' => $stockDespues,
            'motivo' => 'Ajuste directo desde panel/edición de producto.',
            'usuario_id' => auth()->id(),
            'created_at' => now(),
        ]);
    }
}

<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\StockMovimiento;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Decrement product/variant stock atomically. Used on sales.
     *
     * @throws InsufficientStockException
     */
    public function decrementar(
        Producto $producto,
        ?ProductoVariante $variante,
        int $cantidad,
        string $tipo,
        ?Pedido $referencia = null,
        ?User $usuario = null,
        ?string $motivo = null,
        bool $permitirNegativo = false,
    ): StockMovimiento {
        if ($cantidad <= 0) {
            throw new \InvalidArgumentException('La cantidad a decrementar debe ser positiva.');
        }

        return DB::transaction(function () use (
            $producto,
            $variante,
            $cantidad,
            $tipo,
            $referencia,
            $usuario,
            $motivo,
            $permitirNegativo,
        ) {
            if ($variante) {
                $variante = ProductoVariante::whereKey($variante->id)->lockForUpdate()->first();
                $stockAntes = (int) $variante->stock;
            } else {
                $producto = Producto::whereKey($producto->id)->lockForUpdate()->first();
                $stockAntes = (int) $producto->stock;
            }

            $stockDespues = $stockAntes - $cantidad;

            if ($stockDespues < 0 && ! $permitirNegativo) {
                throw new InsufficientStockException(
                    productoId: $producto->id,
                    varianteId: $variante?->id,
                    stockActual: $stockAntes,
                    cantidadSolicitada: $cantidad,
                );
            }

            if ($variante) {
                $variante->update(['stock' => $stockDespues]);
            } else {
                $producto->update(['stock' => $stockDespues]);
            }

            return StockMovimiento::create([
                'producto_id' => $producto->id,
                'variante_id' => $variante?->id,
                'tipo' => $tipo,
                'cantidad' => -$cantidad,
                'stock_antes' => $stockAntes,
                'stock_despues' => $stockDespues,
                'motivo' => $motivo,
                'referencia_tipo' => $referencia ? Pedido::class : null,
                'referencia_id' => $referencia?->id,
                'usuario_id' => $usuario?->id,
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Increment product/variant stock atomically. Used on refunds/returns/entries.
     */
    public function incrementar(
        Producto $producto,
        ?ProductoVariante $variante,
        int $cantidad,
        string $tipo,
        ?Pedido $referencia = null,
        ?User $usuario = null,
        ?string $motivo = null,
    ): StockMovimiento {
        if ($cantidad <= 0) {
            throw new \InvalidArgumentException('La cantidad a incrementar debe ser positiva.');
        }

        return DB::transaction(function () use (
            $producto,
            $variante,
            $cantidad,
            $tipo,
            $referencia,
            $usuario,
            $motivo,
        ) {
            if ($variante) {
                $variante = ProductoVariante::whereKey($variante->id)->lockForUpdate()->first();
                $stockAntes = (int) $variante->stock;
                $stockDespues = $stockAntes + $cantidad;
                $variante->update(['stock' => $stockDespues]);
            } else {
                $producto = Producto::whereKey($producto->id)->lockForUpdate()->first();
                $stockAntes = (int) $producto->stock;
                $stockDespues = $stockAntes + $cantidad;
                $producto->update(['stock' => $stockDespues]);
            }

            return StockMovimiento::create([
                'producto_id' => $producto->id,
                'variante_id' => $variante?->id,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'stock_antes' => $stockAntes,
                'stock_despues' => $stockDespues,
                'motivo' => $motivo,
                'referencia_tipo' => $referencia ? Pedido::class : null,
                'referencia_id' => $referencia?->id,
                'usuario_id' => $usuario?->id,
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Manually adjust stock to an absolute value. Logs the delta as movement.
     */
    public function ajustar(
        Producto $producto,
        int $nuevoStock,
        string $motivo,
        ?User $usuario = null,
        ?ProductoVariante $variante = null,
    ): StockMovimiento {
        if ($nuevoStock < 0) {
            throw new \InvalidArgumentException('El stock nuevo no puede ser negativo.');
        }

        return DB::transaction(function () use ($producto, $nuevoStock, $motivo, $usuario, $variante) {
            if ($variante) {
                $variante = ProductoVariante::whereKey($variante->id)->lockForUpdate()->first();
                $stockAntes = (int) $variante->stock;
                $variante->update(['stock' => $nuevoStock]);
            } else {
                $producto = Producto::whereKey($producto->id)->lockForUpdate()->first();
                $stockAntes = (int) $producto->stock;
                $producto->update(['stock' => $nuevoStock]);
            }

            return StockMovimiento::create([
                'producto_id' => $producto->id,
                'variante_id' => $variante?->id,
                'tipo' => 'ajuste',
                'cantidad' => $nuevoStock - $stockAntes,
                'stock_antes' => $stockAntes,
                'stock_despues' => $nuevoStock,
                'motivo' => $motivo,
                'usuario_id' => $usuario?->id,
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Map pedido.canal → stock movement type.
     */
    public function tipoMovimientoVenta(string $canal): string
    {
        return match ($canal) {
            'web' => 'venta_web',
            'app' => 'venta_app',
            'tpv' => 'venta_tpv',
            default => 'salida',
        };
    }
}

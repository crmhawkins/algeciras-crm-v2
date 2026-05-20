<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePedidoRequest;
use App\Http\Resources\PedidoResource;
use App\Models\Cupon;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function store(StorePedidoRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data, $request) {
            $subtotal = 0;
            $iva = 0;
            $linesPayload = [];

            foreach ($data['items'] as $line) {
                $producto = Producto::lockForUpdate()->findOrFail($line['producto_id']);
                $precio = (float) ($producto->precio_oferta ?? $producto->precio_base);
                $cantidad = (int) $line['cantidad'];
                $linea = round($precio * $cantidad, 2);
                $lineaIva = round($linea * ((float) $producto->iva_pct / 100), 2);

                $subtotal += $linea;
                $iva += $lineaIva;

                $linesPayload[] = [
                    'producto' => $producto,
                    'variante_id' => $line['variante_id'] ?? null,
                    'sku' => $producto->sku,
                    'nombre_producto' => $producto->nombre,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'iva_pct' => (float) $producto->iva_pct,
                    'subtotal_linea' => $linea,
                    'total_linea' => round($linea + $lineaIva, 2),
                ];
            }

            $descuento = 0;
            if (! empty($data['cupon'])) {
                $cupon = Cupon::activo()->where('codigo', $data['cupon'])->first();
                if ($cupon && $cupon->disponibleEnCanal($data['canal'])) {
                    $descuento = $cupon->calcularDescuento($subtotal);
                    $cupon->increment('usos_actual');
                }
            }

            $gastosEnvio = $data['canal'] === 'tpv' ? 0 : ($subtotal >= 50 ? 0 : 4.95);
            $total = round($subtotal - $descuento + $iva + $gastosEnvio, 2);

            $pedido = Pedido::create([
                'codigo' => Pedido::generarCodigo(),
                'canal' => $data['canal'],
                'estado' => Pedido::ESTADO_PENDIENTE_PAGO,
                'cliente_id' => $request->user()?->id,
                'nombre_cliente' => $data['nombre_cliente'],
                'email_cliente' => $data['email_cliente'],
                'telefono_cliente' => $data['telefono_cliente'] ?? null,
                'direccion_envio' => $data['direccion_envio'] ?? null,
                'cp_envio' => $data['cp_envio'] ?? null,
                'ciudad_envio' => $data['ciudad_envio'] ?? null,
                'provincia_envio' => $data['provincia_envio'] ?? null,
                'pais_envio' => $data['pais_envio'] ?? 'ES',
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'iva' => $iva,
                'gastos_envio' => $gastosEnvio,
                'total' => $total,
                'metodo_pago' => $data['metodo_pago'],
                'notas_cliente' => $data['notas_cliente'] ?? null,
                'cajero_id' => $data['canal'] === 'tpv' ? $request->user()?->id : null,
            ]);

            foreach ($linesPayload as $line) {
                $pedido->items()->create([
                    'producto_id' => $line['producto']->id,
                    'variante_id' => $line['variante_id'],
                    'sku' => $line['sku'],
                    'nombre_producto' => $line['nombre_producto'],
                    'cantidad' => $line['cantidad'],
                    'precio_unitario' => $line['precio_unitario'],
                    'descuento_pct' => 0,
                    'iva_pct' => $line['iva_pct'],
                    'subtotal_linea' => $line['subtotal_linea'],
                    'total_linea' => $line['total_linea'],
                ]);
            }

            $pedido->load('items');

            return (new PedidoResource($pedido))
                ->response()
                ->setStatusCode(201);
        });
    }

    public function show(Request $request, string $codigo)
    {
        $query = Pedido::query()->with('items');

        if ($request->user()) {
            $query->where(function ($q) use ($request) {
                $q->where('cliente_id', $request->user()->id)
                    ->orWhere('cajero_id', $request->user()->id);
            });
        }

        $pedido = $query->where('codigo', $codigo)->firstOrFail();

        return new PedidoResource($pedido);
    }
}

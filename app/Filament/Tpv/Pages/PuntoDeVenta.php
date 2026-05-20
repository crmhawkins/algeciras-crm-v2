<?php

namespace App\Filament\Tpv\Pages;

use App\Models\MetodoPago;
use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Models\User;
use App\Services\StockService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PuntoDeVenta extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $navigationLabel = 'Punto de Venta';

    protected static ?string $title = 'Punto de Venta';

    protected static ?string $slug = 'punto-de-venta';

    protected string $view = 'filament.tpv.pages.punto-de-venta';

    // State
    public string $search = '';
    public ?int $categoriaFilter = null;
    /** @var array<int, array{producto_id:int, sku:string, nombre:string, precio:float, iva:float, qty:int}> */
    public array $cart = [];
    public ?int $clienteId = null;
    public bool $clienteAnonimo = true;
    public string $metodoPago = 'efectivo';
    public bool $showTicket = false;
    public ?string $ultimoCodigo = null;
    public float $ultimoTotal = 0;

    public function getProductos(): Collection
    {
        return Producto::query()
            ->where('visible_tpv', true)
            ->where('stock', '>', 0)
            ->when($this->search !== '', fn ($q) =>
                $q->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                })
            )
            ->when($this->categoriaFilter, fn ($q) => $q->where('categoria_id', $this->categoriaFilter))
            ->with('imagenPrincipal')
            ->orderBy('nombre')
            ->limit(60)
            ->get();
    }

    public function getCategorias(): Collection
    {
        return Categoria::query()
            ->where('visible_tpv', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
    }

    public function getMetodosPago(): Collection
    {
        return MetodoPago::query()
            ->where('activo', true)
            ->where('disponible_tpv', true)
            ->orderBy('orden')
            ->get();
    }

    public function getClientes(): Collection
    {
        return User::query()->orderBy('name')->limit(20)->get();
    }

    public function addToCart(int $productoId): void
    {
        $producto = Producto::find($productoId);
        if (! $producto || $producto->stock <= 0) {
            Notification::make()->title('Producto sin stock')->danger()->send();
            return;
        }

        foreach ($this->cart as $idx => $item) {
            if ($item['producto_id'] === $productoId) {
                if ($this->cart[$idx]['qty'] + 1 > $producto->stock) {
                    Notification::make()->title('No hay más stock disponible')->warning()->send();
                    return;
                }
                $this->cart[$idx]['qty']++;
                return;
            }
        }

        $this->cart[] = [
            'producto_id' => $producto->id,
            'sku' => (string) $producto->sku,
            'nombre' => $producto->nombre,
            'precio' => (float) ($producto->precio_oferta ?? $producto->precio_base),
            'iva' => (float) ($producto->iva_pct ?? 21),
            'qty' => 1,
        ];
    }

    public function increment(int $index): void
    {
        if (! isset($this->cart[$index])) return;
        $producto = Producto::find($this->cart[$index]['producto_id']);
        if (! $producto) return;
        if ($this->cart[$index]['qty'] + 1 > $producto->stock) {
            Notification::make()->title('Sin stock para aumentar')->warning()->send();
            return;
        }
        $this->cart[$index]['qty']++;
    }

    public function decrement(int $index): void
    {
        if (! isset($this->cart[$index])) return;
        if ($this->cart[$index]['qty'] <= 1) {
            $this->removeFromCart($index);
            return;
        }
        $this->cart[$index]['qty']--;
    }

    public function removeFromCart(int $index): void
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->clienteId = null;
        $this->clienteAnonimo = true;
        $this->showTicket = false;
        $this->ultimoCodigo = null;
        $this->ultimoTotal = 0;
    }

    public function getSubtotal(): float
    {
        $subtotal = 0;
        foreach ($this->cart as $item) {
            $base = $item['precio'] / (1 + $item['iva'] / 100);
            $subtotal += $base * $item['qty'];
        }
        return round($subtotal, 2);
    }

    public function getIva(): float
    {
        $iva = 0;
        foreach ($this->cart as $item) {
            $base = $item['precio'] / (1 + $item['iva'] / 100);
            $iva += ($item['precio'] - $base) * $item['qty'];
        }
        return round($iva, 2);
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['precio'] * $item['qty'];
        }
        return round($total, 2);
    }

    public function cobrar(): void
    {
        if (empty($this->cart)) {
            Notification::make()->title('El carrito está vacío')->warning()->send();
            return;
        }

        $stockService = app(StockService::class);

        DB::transaction(function () use ($stockService) {
            $codigo = Pedido::generarCodigo();
            $subtotal = $this->getSubtotal();
            $iva = $this->getIva();
            $total = $this->getTotal();

            $pedido = Pedido::create([
                'codigo' => $codigo,
                'canal' => Pedido::CANAL_TPV,
                'estado' => Pedido::ESTADO_PAGADO,
                'cliente_id' => $this->clienteAnonimo ? null : $this->clienteId,
                'nombre_cliente' => $this->clienteAnonimo ? null : (User::find($this->clienteId)?->name),
                'email_cliente' => $this->clienteAnonimo ? null : (User::find($this->clienteId)?->email),
                'subtotal' => $subtotal,
                'descuento' => 0,
                'iva' => $iva,
                'gastos_envio' => 0,
                'total' => $total,
                'metodo_pago' => $this->metodoPago,
                'pagado_en' => now(),
                'cajero_id' => auth()->id(),
            ]);

            foreach ($this->cart as $item) {
                $base = $item['precio'] / (1 + $item['iva'] / 100);
                $subtotalLinea = round($base * $item['qty'], 2);
                $totalLinea = round($item['precio'] * $item['qty'], 2);

                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item['producto_id'],
                    'sku' => $item['sku'],
                    'nombre_producto' => $item['nombre'],
                    'cantidad' => $item['qty'],
                    'precio_unitario' => $item['precio'],
                    'descuento_pct' => 0,
                    'iva_pct' => $item['iva'],
                    'subtotal_linea' => $subtotalLinea,
                    'total_linea' => $totalLinea,
                ]);

                $producto = Producto::find($item['producto_id']);
                if ($producto) {
                    $stockService->decrementar(
                        producto: $producto,
                        variante: null,
                        cantidad: $item['qty'],
                        tipo: 'venta_tpv',
                        referencia: $pedido,
                        usuario: auth()->user(),
                        motivo: 'Venta TPV #' . $pedido->codigo,
                    );
                }
            }

            $this->ultimoCodigo = $codigo;
            $this->ultimoTotal = $total;
            $this->showTicket = true;
        });

        Notification::make()
            ->title('Venta registrada')
            ->body('Pedido ' . $this->ultimoCodigo . ' · €' . number_format($this->ultimoTotal, 2, ',', '.'))
            ->success()
            ->send();
    }

    public function nuevaVenta(): void
    {
        $this->clearCart();
    }
}

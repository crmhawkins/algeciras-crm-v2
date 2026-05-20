<?php

namespace App\Providers;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Observers\PedidoItemObserver;
use App\Observers\PedidoObserver;
use App\Observers\ProductoObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Producto::observe(ProductoObserver::class);
        PedidoItem::observe(PedidoItemObserver::class);
        Pedido::observe(PedidoObserver::class);
    }
}

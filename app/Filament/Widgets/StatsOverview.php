<?php

namespace App\Filament\Widgets;

use App\Models\Abonado;
use App\Models\Pedido;
use App\Models\Producto;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $ventasHoy = Pedido::whereDate('pagado_en', today())
            ->whereIn('estado', ['pagado', 'preparando', 'enviado', 'entregado'])
            ->sum('total');

        $stockBajo = Producto::whereColumn('stock', '<=', 'stock_minimo')->count();

        $pedidosPendientes = Pedido::whereIn('estado', ['pendiente_pago', 'preparando'])->count();

        $abonadosActivos = Abonado::where('activo', true)
            ->where('valido_hasta', '>=', now())
            ->count();

        return [
            Stat::make('Ventas hoy', '€ ' . number_format((float) $ventasHoy, 2, ',', '.'))
                ->description('Total cobrado en pedidos hoy')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Stock bajo', (string) $stockBajo)
                ->description('Productos en o bajo el mínimo')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($stockBajo > 0 ? 'warning' : 'success'),

            Stat::make('Pedidos pendientes', (string) $pedidosPendientes)
                ->description('Pendientes de pago o preparando')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color($pedidosPendientes > 0 ? 'info' : 'gray'),

            Stat::make('Abonados activos', (string) $abonadosActivos)
                ->description('Socios con abono vigente')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
        ];
    }
}

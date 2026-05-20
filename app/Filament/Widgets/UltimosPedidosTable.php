<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UltimosPedidosTable extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Últimos pedidos';

    public function table(Table $table): Table
    {
        return $table
            ->query(Pedido::query()->latest()->limit(10))
            ->paginated(false)
            ->columns([
                TextColumn::make('codigo')->label('Código')->fontFamily('mono')->weight('bold'),
                TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i'),
                TextColumn::make('canal')->label('Canal')->badge()->color(fn ($s) => match ($s) {
                    'web' => 'info', 'app' => 'success', 'tpv' => 'warning', default => 'gray',
                }),
                TextColumn::make('cliente.name')->label('Cliente')->placeholder(fn (Pedido $r) => $r->nombre_cliente ?? 'Anónimo'),
                TextColumn::make('estado')->label('Estado')->badge()->color(fn ($s) => match ($s) {
                    'pagado', 'entregado' => 'success',
                    'enviado', 'preparando' => 'info',
                    'pendiente_pago' => 'warning',
                    'cancelado', 'devuelto' => 'danger',
                    default => 'gray',
                }),
                TextColumn::make('total')->label('Total')->money('EUR')->weight('bold')->alignRight(),
            ])
            ->recordActions([])
            ->toolbarActions([])
            ->headerActions([]);
    }
}

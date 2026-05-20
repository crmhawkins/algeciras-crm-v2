<?php

namespace App\Filament\Widgets;

use App\Models\Producto;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProductosStockBajoTable extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Productos con stock bajo';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Producto::query()
                    ->whereColumn('stock', '<=', 'stock_minimo')
                    ->orderBy('stock')
                    ->limit(10)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('sku')->label('SKU')->fontFamily('mono')->size('xs'),
                TextColumn::make('nombre')->label('Producto')->limit(40)->weight('bold'),
                TextColumn::make('categoria.nombre')->label('Categoría')->badge()->color('gray'),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->badge()
                    ->color(fn (Producto $r) => $r->stock <= 0 ? 'danger' : 'warning')
                    ->formatStateUsing(fn ($s, Producto $r) => $s . ' / min ' . $r->stock_minimo)
                    ->alignCenter(),
            ])
            ->recordActions([])
            ->toolbarActions([])
            ->headerActions([]);
    }
}

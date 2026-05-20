<?php

namespace App\Filament\Resources\StockMovimientos;

use App\Filament\Resources\StockMovimientos\Pages\ListStockMovimientos;
use App\Models\StockMovimiento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;

class StockMovimientoResource extends Resource
{
    protected static ?string $model = StockMovimiento::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static \UnitEnum|string|null $navigationGroup = 'Tienda';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Movimiento de stock';

    protected static ?string $pluralModelLabel = 'Movimientos de stock';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('producto.sku')
                    ->label('SKU')
                    ->fontFamily('mono')
                    ->size('xs'),
                TextColumn::make('variante.nombre')
                    ->label('Variante')
                    ->placeholder('—'),
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'venta_web', 'venta_app', 'venta_tpv' => 'success',
                        'devolucion' => 'info',
                        'entrada' => 'primary',
                        'ajuste' => 'warning',
                        'merma' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->alignRight()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => ($state > 0 ? '+' : '') . $state),
                TextColumn::make('stock_antes')->label('Antes')->alignRight(),
                TextColumn::make('stock_despues')->label('Después')->alignRight()->weight('bold'),
                TextColumn::make('motivo')->label('Motivo')->limit(40)->wrap(),
                TextColumn::make('usuario.name')->label('Usuario')->placeholder('Sistema'),
                TextColumn::make('referencia_id')
                    ->label('Pedido')
                    ->formatStateUsing(fn ($state, $record) => $state ? '#' . $state : '—'),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'venta_web' => 'Venta Web',
                        'venta_app' => 'Venta App',
                        'venta_tpv' => 'Venta TPV',
                        'devolucion' => 'Devolución',
                        'entrada' => 'Entrada',
                        'ajuste' => 'Ajuste',
                        'merma' => 'Merma',
                        'salida' => 'Salida',
                    ])
                    ->multiple(),
                SelectFilter::make('producto_id')
                    ->label('Producto')
                    ->relationship('producto', 'nombre')
                    ->searchable()
                    ->preload(),
                Filter::make('fecha')
                    ->schema([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                            ->when($data['hasta'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '<=', $d));
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMovimientos::route('/'),
        ];
    }
}

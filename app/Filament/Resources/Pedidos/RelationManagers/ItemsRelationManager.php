<?php

namespace App\Filament\Resources\Pedidos\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Items del pedido';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_producto')->label('Producto')->wrap(),
                TextColumn::make('sku')->label('SKU')->fontFamily('mono')->size('xs'),
                TextColumn::make('variante.nombre')->label('Variante')->placeholder('—'),
                TextColumn::make('cantidad')->label('Cant.')->alignCenter(),
                TextColumn::make('precio_unitario')->label('P.U.')->money('EUR'),
                TextColumn::make('descuento_pct')->label('Dto.')->suffix('%'),
                TextColumn::make('iva_pct')->label('IVA')->suffix('%'),
                TextColumn::make('subtotal_linea')->label('Subtotal')->money('EUR'),
                TextColumn::make('total_linea')->label('Total')->money('EUR')->weight('bold'),
            ])
            ->recordActions([])
            ->headerActions([])
            ->toolbarActions([]);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}

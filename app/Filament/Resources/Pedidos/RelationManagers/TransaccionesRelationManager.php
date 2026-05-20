<?php

namespace App\Filament\Resources\Pedidos\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransaccionesRelationManager extends RelationManager
{
    protected static string $relationship = 'transacciones';

    protected static ?string $title = 'Transacciones de pago';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i'),
                TextColumn::make('gateway')->label('Gateway')->badge(),
                TextColumn::make('referencia')->label('Referencia')->copyable()->fontFamily('mono')->size('xs'),
                TextColumn::make('monto')->label('Monto')->money('EUR'),
                TextColumn::make('estado')->label('Estado')->badge()->color(fn ($state) => match ($state) {
                    'completada', 'success' => 'success',
                    'pendiente' => 'warning',
                    'fallida', 'error' => 'danger',
                    default => 'gray',
                }),
                TextColumn::make('error_mensaje')->label('Error')->limit(40)->placeholder('—'),
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

<?php

namespace App\Filament\Resources\Productos\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PreciosCanalRelationManager extends RelationManager
{
    protected static string $relationship = 'preciosCanal';

    protected static ?string $title = 'Precios por canal';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('canal')
                ->label('Canal')
                ->required()
                ->options([
                    'web' => 'Web',
                    'app' => 'App',
                    'tpv' => 'TPV',
                ]),
            Select::make('variante_id')
                ->label('Variante (opcional)')
                ->relationship('variante', 'nombre')
                ->searchable()
                ->preload(),
            TextInput::make('precio')
                ->label('Precio')
                ->required()
                ->numeric()
                ->prefix('€')
                ->step(0.01),
            TextInput::make('descuento_pct')
                ->label('Descuento (%)')
                ->numeric()
                ->suffix('%')
                ->step(0.01)
                ->minValue(0)
                ->maxValue(100),
            TextInput::make('descuento_socio_pct')
                ->label('Descuento socio (%)')
                ->numeric()
                ->suffix('%')
                ->step(0.01)
                ->minValue(0)
                ->maxValue(100),
            DateTimePicker::make('desde')->label('Desde'),
            DateTimePicker::make('hasta')->label('Hasta'),
            Toggle::make('activo')->label('Activo')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('canal')
                    ->label('Canal')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'web' => 'info',
                        'app' => 'success',
                        'tpv' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('variante.nombre')->label('Variante')->placeholder('—'),
                TextColumn::make('precio')->label('Precio')->money('EUR'),
                TextColumn::make('descuento_pct')->label('Desc. %')->suffix('%')->placeholder('—'),
                TextColumn::make('descuento_socio_pct')->label('Desc. socio %')->suffix('%')->placeholder('—'),
                TextColumn::make('desde')->label('Desde')->dateTime('d/m/Y H:i')->placeholder('—'),
                TextColumn::make('hasta')->label('Hasta')->dateTime('d/m/Y H:i')->placeholder('—'),
                IconColumn::make('activo')->label('Activo')->boolean(),
            ])
            ->headerActions([
                CreateAction::make()->label('Nuevo precio'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

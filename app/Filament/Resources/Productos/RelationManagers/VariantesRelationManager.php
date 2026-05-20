<?php

namespace App\Filament\Resources\Productos\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantesRelationManager extends RelationManager
{
    protected static string $relationship = 'variantes';

    protected static ?string $title = 'Variantes';

    protected static ?string $modelLabel = 'variante';

    protected static ?string $pluralModelLabel = 'variantes';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(120),
            TextInput::make('sku')
                ->label('SKU')
                ->maxLength(64)
                ->unique(ignoreRecord: true),
            KeyValue::make('atributos')
                ->label('Atributos')
                ->keyLabel('Atributo')
                ->valueLabel('Valor')
                ->helperText('Ej: talla = M, color = rojo'),
            TextInput::make('precio_extra')
                ->label('Precio extra')
                ->numeric()
                ->prefix('€')
                ->default(0)
                ->step(0.01),
            TextInput::make('stock')
                ->label('Stock')
                ->numeric()
                ->integer()
                ->default(0)
                ->minValue(0),
            TextInput::make('orden')
                ->label('Orden')
                ->numeric()
                ->integer()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('orden')
            ->defaultSort('orden')
            ->columns([
                TextColumn::make('nombre')->label('Variante')->searchable(),
                TextColumn::make('sku')->label('SKU')->fontFamily('mono')->size('xs'),
                TextColumn::make('precio_extra')->label('Extra')->money('EUR'),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
            ])
            ->headerActions([
                CreateAction::make()->label('Nueva variante'),
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

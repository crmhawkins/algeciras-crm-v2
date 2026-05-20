<?php

namespace App\Filament\Resources\Productos\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ImagenesRelationManager extends RelationManager
{
    protected static string $relationship = 'imagenes';

    protected static ?string $title = 'Imágenes';

    protected static ?string $modelLabel = 'imagen';

    protected static ?string $pluralModelLabel = 'imágenes';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('ruta')
                ->label('Imagen')
                ->image()
                ->imageEditor()
                ->disk('public')
                ->directory('productos')
                ->required(),
            TextInput::make('alt')
                ->label('Texto alternativo (alt)')
                ->maxLength(255),
            TextInput::make('orden')
                ->label('Orden')
                ->numeric()
                ->integer()
                ->default(0),
            Toggle::make('es_principal')
                ->label('Imagen principal'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('orden')
            ->defaultSort('orden')
            ->columns([
                ImageColumn::make('ruta')
                    ->label('Imagen')
                    ->disk('public')
                    ->height(60),
                TextColumn::make('alt')->label('Alt')->limit(40),
                ToggleColumn::make('es_principal')->label('Principal'),
                TextColumn::make('orden')->label('Orden')->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Subir imagen'),
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

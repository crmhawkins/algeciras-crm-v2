<?php

namespace App\Filament\Resources\NoticiaCategorias;

use App\Filament\Resources\NoticiaCategorias\Pages\ListNoticiaCategorias;
use App\Models\NoticiaCategoria;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NoticiaCategoriaResource extends Resource
{
    protected static ?string $model = NoticiaCategoria::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static \UnitEnum|string|null $navigationGroup = 'Contenidos Web';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Categoría de noticia';

    protected static ?string $pluralModelLabel = 'Categorías de noticias';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(120)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, callable $set, $context) =>
                    $context === 'create' ? $set('slug', Str::slug((string) $state)) : null
                ),
            TextInput::make('slug')->label('Slug')->required()->maxLength(120)->unique(ignoreRecord: true),
            ColorPicker::make('color')->label('Color')->default('#C8102E'),
            TextInput::make('orden')->label('Orden')->numeric()->integer()->default(0),
            Textarea::make('descripcion')->label('Descripción')->rows(2)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('orden')
            ->defaultSort('orden')
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable()->weight('bold'),
                TextColumn::make('slug')->label('Slug')->fontFamily('mono')->size('xs'),
                ColorColumn::make('color')->label('Color'),
                TextColumn::make('noticias_count')->counts('noticias')->label('Noticias')->badge()->color('info'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListNoticiaCategorias::route('/')];
    }
}

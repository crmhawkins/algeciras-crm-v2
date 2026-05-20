<?php

namespace App\Filament\Resources\Categorias;

use App\Filament\Resources\Categorias\Pages\ListCategorias;
use App\Models\Categoria;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoriaResource extends Resource
{
    protected static ?string $model = Categoria::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static \UnitEnum|string|null $navigationGroup = 'Tienda';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Categoría';

    protected static ?string $pluralModelLabel = 'Categorías';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(120)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, callable $set, $context) =>
                    $context === 'create' ? $set('slug', Str::slug((string) $state)) : null
                ),
            TextInput::make('slug')
                ->label('Slug (URL)')
                ->required()
                ->maxLength(120)
                ->unique(ignoreRecord: true),
            Textarea::make('descripcion')
                ->label('Descripción')
                ->rows(3)
                ->columnSpanFull(),
            Select::make('parent_id')
                ->label('Categoría padre')
                ->relationship('parent', 'nombre', fn ($query, $record) =>
                    $record ? $query->where('id', '!=', $record->id) : $query
                )
                ->searchable()
                ->preload()
                ->placeholder('Categoría raíz'),
            FileUpload::make('imagen')
                ->label('Imagen')
                ->image()
                ->disk('public')
                ->directory('categorias'),
            TextInput::make('orden')
                ->label('Orden')
                ->numeric()
                ->integer()
                ->default(0),
            Grid::make(3)->schema([
                Toggle::make('visible_web')->label('Visible en Web')->default(true),
                Toggle::make('visible_app')->label('Visible en App')->default(true),
                Toggle::make('visible_tpv')->label('Visible en TPV')->default(true),
            ])->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('orden')
            ->defaultSort('orden')
            ->columns([
                ImageColumn::make('imagen')->label('Imagen')->disk('public')->circular(),
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable()->weight('bold'),
                TextColumn::make('parent.nombre')->label('Padre')->badge()->color('gray')->placeholder('— raíz —'),
                TextColumn::make('productos_count')
                    ->label('Productos')
                    ->counts('productos')
                    ->badge()
                    ->color('info'),
                IconColumn::make('visible_web')->label('Web')->boolean(),
                IconColumn::make('visible_app')->label('App')->boolean(),
                IconColumn::make('visible_tpv')->label('TPV')->boolean(),
                TextColumn::make('orden')->label('Orden')->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index' => ListCategorias::route('/'),
        ];
    }
}

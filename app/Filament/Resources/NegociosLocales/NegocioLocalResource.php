<?php

namespace App\Filament\Resources\NegociosLocales;

use App\Filament\Resources\NegociosLocales\Pages\ListNegociosLocales;
use App\Filament\Resources\NegociosLocales\RelationManagers\OfertasRelationManager;
use App\Models\NegocioLocal;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NegocioLocalResource extends Resource
{
    protected static ?string $model = NegocioLocal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static \UnitEnum|string|null $navigationGroup = 'Zona Socio';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Negocio local';

    protected static ?string $pluralModelLabel = 'Negocios locales';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(180),
            TextInput::make('categoria')->label('Categoría')->maxLength(120),
            TextInput::make('direccion')->label('Dirección')->maxLength(255)->columnSpanFull(),
            TextInput::make('telefono')->label('Teléfono')->maxLength(40),
            TextInput::make('web')->label('Web')->url()->maxLength(255),
            FileUpload::make('logo')->label('Logo')->image()->disk('public')->directory('negocios'),
            Toggle::make('activo')->label('Activo')->default(true),
            Textarea::make('descripcion')->label('Descripción')->rows(3)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')->label('Logo')->disk('public')->circular(),
                TextColumn::make('nombre')->label('Nombre')->searchable()->weight('bold'),
                TextColumn::make('categoria')->label('Categoría')->badge(),
                TextColumn::make('telefono')->label('Teléfono'),
                TextColumn::make('ofertas_count')->counts('ofertas')->label('Ofertas')->badge()->color('success'),
                IconColumn::make('activo')->label('Activo')->boolean(),
            ])
            ->filters([
                TernaryFilter::make('activo'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            OfertasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return ['index' => ListNegociosLocales::route('/')];
    }
}

<?php

namespace App\Filament\Resources\Equipos;

use App\Filament\Resources\Equipos\Pages\ListEquipos;
use App\Models\Equipo;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EquipoResource extends Resource
{
    protected static ?string $model = Equipo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static \UnitEnum|string|null $navigationGroup = 'Primer Equipo';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Equipo';

    protected static ?string $pluralModelLabel = 'Equipos';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(120),
            TextInput::make('ciudad')->label('Ciudad')->maxLength(120),
            FileUpload::make('escudo')->label('Escudo')->image()->disk('public')->directory('equipos'),
            TextInput::make('sofascore_id')->label('SofaScore ID')->numeric()->integer(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('escudo')->label('Escudo')->disk('public')->height(40),
                TextColumn::make('nombre')->label('Nombre')->searchable()->weight('bold'),
                TextColumn::make('ciudad')->label('Ciudad'),
                TextColumn::make('sofascore_id')->label('SofaScore')->fontFamily('mono')->size('xs'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListEquipos::route('/')];
    }
}

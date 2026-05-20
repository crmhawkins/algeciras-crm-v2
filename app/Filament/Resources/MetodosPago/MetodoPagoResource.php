<?php

namespace App\Filament\Resources\MetodosPago;

use App\Filament\Resources\MetodosPago\Pages\ListMetodosPago;
use App\Models\MetodoPago;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MetodoPagoResource extends Resource
{
    protected static ?string $model = MetodoPago::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static \UnitEnum|string|null $navigationGroup = 'Tienda';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Método de pago';

    protected static ?string $pluralModelLabel = 'Métodos de pago';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('codigo')->label('Código')->required()->unique(ignoreRecord: true)->maxLength(40),
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(120),
            TextInput::make('comision_pct')->label('Comisión (%)')->numeric()->suffix('%')->step(0.01),
            TextInput::make('orden')->label('Orden')->numeric()->integer()->default(0),
            Toggle::make('activo')->label('Activo')->default(true),
            Grid::make(3)->schema([
                Toggle::make('disponible_web')->label('Web')->default(true),
                Toggle::make('disponible_app')->label('App')->default(true),
                Toggle::make('disponible_tpv')->label('TPV')->default(true),
            ])->columnSpanFull(),
            KeyValue::make('configuracion')->label('Configuración')->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('orden')
            ->defaultSort('orden')
            ->columns([
                TextColumn::make('codigo')->label('Código')->fontFamily('mono')->size('xs'),
                TextColumn::make('nombre')->label('Nombre')->searchable()->weight('bold'),
                TextColumn::make('comision_pct')->label('Comisión')->suffix('%')->placeholder('—'),
                IconColumn::make('disponible_web')->label('Web')->boolean(),
                IconColumn::make('disponible_app')->label('App')->boolean(),
                IconColumn::make('disponible_tpv')->label('TPV')->boolean(),
                IconColumn::make('activo')->label('Activo')->boolean(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListMetodosPago::route('/')];
    }
}

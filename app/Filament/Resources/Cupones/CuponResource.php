<?php

namespace App\Filament\Resources\Cupones;

use App\Filament\Resources\Cupones\Pages\ListCupones;
use App\Models\Cupon;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CuponResource extends Resource
{
    protected static ?string $model = Cupon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static \UnitEnum|string|null $navigationGroup = 'Tienda';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Cupón';

    protected static ?string $pluralModelLabel = 'Cupones';

    protected static ?string $recordTitleAttribute = 'codigo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('codigo')->label('Código')->required()->unique(ignoreRecord: true)->maxLength(40),
            Select::make('tipo')->label('Tipo')->required()->options([
                'porcentaje' => 'Porcentaje (%)',
                'fijo' => 'Importe fijo (€)',
            ])->default('porcentaje'),
            TextInput::make('valor')->label('Valor')->required()->numeric()->step(0.01)->minValue(0),
            TextInput::make('compra_minima')->label('Compra mínima')->numeric()->prefix('€')->step(0.01),
            TextInput::make('usos_max')->label('Usos máximos')->numeric()->integer()->placeholder('Sin límite'),
            TextInput::make('usos_actual')->label('Usos actual')->numeric()->integer()->default(0)->disabled()->dehydrated(),
            DateTimePicker::make('valido_desde')->label('Válido desde')->required(),
            DateTimePicker::make('valido_hasta')->label('Válido hasta')->required(),
            Toggle::make('solo_socios')->label('Sólo socios'),
            Toggle::make('activo')->label('Activo')->default(true),
            CheckboxList::make('canales')->label('Canales')->options([
                'web' => 'Web',
                'app' => 'App',
                'tpv' => 'TPV',
            ])->columns(3)->columnSpanFull(),
            Textarea::make('descripcion')->label('Descripción')->rows(2)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('codigo')->label('Código')->searchable()->copyable()->fontFamily('mono')->weight('bold'),
                TextColumn::make('tipo')->label('Tipo')->badge(),
                TextColumn::make('valor')->label('Valor')->formatStateUsing(fn ($state, Cupon $r) =>
                    $r->tipo === 'porcentaje' ? $state . '%' : '€ ' . $state
                ),
                TextColumn::make('compra_minima')->label('Mín. compra')->money('EUR')->placeholder('—'),
                TextColumn::make('usos_actual')->label('Usos')->formatStateUsing(fn ($state, Cupon $r) =>
                    $state . ($r->usos_max ? ' / ' . $r->usos_max : '')
                ),
                TextColumn::make('valido_desde')->label('Desde')->date('d/m/Y'),
                TextColumn::make('valido_hasta')->label('Hasta')->date('d/m/Y'),
                IconColumn::make('solo_socios')->label('Socios')->boolean(),
                IconColumn::make('activo')->label('Activo')->boolean(),
            ])
            ->filters([
                TernaryFilter::make('activo')->label('Activos'),
                TernaryFilter::make('solo_socios')->label('Sólo socios'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListCupones::route('/')];
    }
}

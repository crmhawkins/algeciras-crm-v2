<?php

namespace App\Filament\Resources\Clasificaciones;

use App\Filament\Resources\Clasificaciones\Pages\ListClasificaciones;
use App\Models\Clasificacion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClasificacionResource extends Resource
{
    protected static ?string $model = Clasificacion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static \UnitEnum|string|null $navigationGroup = 'Primer Equipo';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Posición';

    protected static ?string $pluralModelLabel = 'Clasificación';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('posicion')
            ->columns([
                TextColumn::make('posicion')->label('#')->badge()->color('primary')->weight('bold'),
                ImageColumn::make('equipo.escudo')->label('')->disk('public')->circular()->height(32),
                TextColumn::make('equipo.nombre')->label('Equipo')->searchable()->weight('bold'),
                TextColumn::make('partidos_jugados')->label('PJ')->alignCenter(),
                TextColumn::make('victorias')->label('G')->alignCenter()->color('success'),
                TextColumn::make('empates')->label('E')->alignCenter()->color('warning'),
                TextColumn::make('derrotas')->label('P')->alignCenter()->color('danger'),
                TextColumn::make('goles_favor')->label('GF')->alignCenter(),
                TextColumn::make('goles_contra')->label('GC')->alignCenter(),
                TextColumn::make('diferencia_goles')->label('DG')->state(fn (Clasificacion $r) => $r->diferencia_goles)->alignCenter(),
                TextColumn::make('puntos')->label('PTS')->alignCenter()->badge()->color('primary')->weight('bold'),
                TextColumn::make('actualizado_en')->label('Actualizada')->dateTime('d/m/Y H:i')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('temporada'),
                SelectFilter::make('competicion'),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return ['index' => ListClasificaciones::route('/')];
    }
}

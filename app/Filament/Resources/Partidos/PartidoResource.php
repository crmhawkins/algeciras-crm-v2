<?php

namespace App\Filament\Resources\Partidos;

use App\Filament\Resources\Partidos\Pages\ListPartidos;
use App\Models\Partido;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PartidoResource extends Resource
{
    protected static ?string $model = Partido::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static \UnitEnum|string|null $navigationGroup = 'Primer Equipo';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Partido';

    protected static ?string $pluralModelLabel = 'Partidos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos del partido')->columns(2)->schema([
                TextInput::make('temporada')->label('Temporada')->default('2025-2026')->required()->maxLength(20),
                TextInput::make('jornada')->label('Jornada')->numeric()->integer()->required(),
                TextInput::make('competicion')->label('Competición')->default('Liga')->required()->maxLength(80),
                Select::make('estado')->label('Estado')->required()->options([
                    'programado' => 'Programado',
                    'en_juego' => 'En juego',
                    'finalizado' => 'Finalizado',
                    'aplazado' => 'Aplazado',
                    'cancelado' => 'Cancelado',
                ])->default('programado'),
                DateTimePicker::make('fecha')->label('Fecha y hora')->required(),
                TextInput::make('estadio')->label('Estadio')->maxLength(120),
                TextInput::make('arbitro')->label('Árbitro')->maxLength(120),
            ]),
            Section::make('Equipos')->columns(2)->schema([
                Select::make('local_id')->label('Equipo local')->relationship('local', 'nombre')->searchable()->preload(),
                Select::make('visitante_id')->label('Equipo visitante')->relationship('visitante', 'nombre')->searchable()->preload(),
                TextInput::make('local_nombre')->label('Nombre local (texto)')->maxLength(120),
                TextInput::make('visitante_nombre')->label('Nombre visitante (texto)')->maxLength(120),
            ]),
            Section::make('Resultado')->columns(2)->schema([
                TextInput::make('goles_local')->label('Goles local')->numeric()->integer()->minValue(0),
                TextInput::make('goles_visitante')->label('Goles visitante')->numeric()->integer()->minValue(0),
            ]),
            Section::make('Entradas y resumen')->collapsed()->columns(2)->schema([
                TextInput::make('compralaentrada_evento_id')->label('CompraLaEntrada Evento ID'),
                TextInput::make('compralaentrada_sesion_id')->label('CompraLaEntrada Sesión ID'),
                TextInput::make('resumen_url')->label('URL resumen vídeo')->url()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('fecha', 'desc')
            ->columns([
                TextColumn::make('jornada')->label('J')->badge()->color('gray'),
                TextColumn::make('fecha')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('competicion')->label('Competición')->badge()->color('info'),
                TextColumn::make('local.nombre')->label('Local')->placeholder(fn (Partido $r) => $r->local_nombre)->weight('bold'),
                TextColumn::make('marcador')
                    ->label('Resultado')
                    ->state(fn (Partido $r) => $r->estado === 'finalizado'
                        ? ($r->goles_local . ' - ' . $r->goles_visitante)
                        : '—')
                    ->badge()
                    ->color('warning')
                    ->alignCenter(),
                TextColumn::make('visitante.nombre')->label('Visitante')->placeholder(fn (Partido $r) => $r->visitante_nombre)->weight('bold'),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'programado' => 'info',
                        'en_juego' => 'warning',
                        'finalizado' => 'success',
                        'aplazado' => 'gray',
                        'cancelado' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('estadio')->label('Estadio')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('estado')->options([
                    'programado' => 'Programado',
                    'en_juego' => 'En juego',
                    'finalizado' => 'Finalizado',
                    'aplazado' => 'Aplazado',
                    'cancelado' => 'Cancelado',
                ])->multiple(),
                SelectFilter::make('temporada'),
                Filter::make('proximos')
                    ->label('Solo próximos')
                    ->query(fn ($q) => $q->where('fecha', '>=', now()))
                    ->toggle(),
                Filter::make('pasados')
                    ->label('Solo pasados')
                    ->query(fn ($q) => $q->where('fecha', '<', now()))
                    ->toggle(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListPartidos::route('/')];
    }
}

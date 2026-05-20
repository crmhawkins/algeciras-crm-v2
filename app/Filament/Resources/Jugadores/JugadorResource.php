<?php

namespace App\Filament\Resources\Jugadores;

use App\Filament\Resources\Jugadores\Pages\ListJugadores;
use App\Models\Jugador;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class JugadorResource extends Resource
{
    protected static ?string $model = Jugador::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static \UnitEnum|string|null $navigationGroup = 'Primer Equipo';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Jugador';

    protected static ?string $pluralModelLabel = 'Jugadores';

    protected static ?string $recordTitleAttribute = 'nombre_completo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('jugador')->columnSpanFull()->tabs([
                Tab::make('Ficha')->icon('heroicon-o-identification')->schema([
                    Section::make('Datos personales')->columns(2)->schema([
                        TextInput::make('nombre_completo')->label('Nombre completo')->required()->maxLength(180),
                        TextInput::make('dorsal')->label('Dorsal')->required()->numeric()->integer()->minValue(0)->maxValue(99),
                        Select::make('posicion')->label('Posición')->required()->options([
                            'portero' => 'Portero',
                            'defensa' => 'Defensa',
                            'centrocampista' => 'Centrocampista',
                            'delantero' => 'Delantero',
                        ]),
                        DatePicker::make('fecha_nacimiento')->label('Fecha de nacimiento'),
                        TextInput::make('nacionalidad')->label('Nacionalidad')->maxLength(80),
                        TextInput::make('altura_cm')->label('Altura (cm)')->numeric()->integer()->suffix('cm'),
                        TextInput::make('peso_kg')->label('Peso (kg)')->numeric()->integer()->suffix('kg'),
                        TextInput::make('equipo')->label('Equipo')->default('primer-equipo')->maxLength(60),
                        TextInput::make('temporada')->label('Temporada')->default('2025-2026')->maxLength(20),
                        TextInput::make('sofascore_id')->label('SofaScore ID')->numeric()->integer()->placeholder('opcional'),
                    ]),
                    Section::make('Foto')->schema([
                        FileUpload::make('foto')->label('Foto')->image()->imageEditor()->disk('public')->directory('jugadores'),
                    ]),
                    Section::make('Biografía')->schema([
                        RichEditor::make('biografia')->label('Biografía')->columnSpanFull(),
                    ]),
                    Toggle::make('activo')->label('En plantilla')->default(true),
                ]),
                Tab::make('Estadísticas')->icon('heroicon-o-chart-bar')->schema([
                    Grid::make(4)->schema([
                        TextInput::make('goles')->label('Goles')->numeric()->integer()->default(0),
                        TextInput::make('asistencias')->label('Asistencias')->numeric()->integer()->default(0),
                        TextInput::make('partidos_jugados')->label('Partidos')->numeric()->integer()->default(0),
                        TextInput::make('minutos_jugados')->label('Minutos')->numeric()->integer()->default(0),
                        TextInput::make('tarjetas_amarillas')->label('Amarillas')->numeric()->integer()->default(0),
                        TextInput::make('tarjetas_rojas')->label('Rojas')->numeric()->integer()->default(0),
                    ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('dorsal')
            ->columns([
                ImageColumn::make('foto')->label('Foto')->disk('public')->circular(),
                TextColumn::make('dorsal')->label('#')->badge()->color('primary')->weight('bold'),
                TextColumn::make('nombre_completo')->label('Nombre')->searchable()->weight('bold'),
                TextColumn::make('posicion')
                    ->label('Posición')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'portero' => 'warning',
                        'defensa' => 'info',
                        'centrocampista' => 'success',
                        'delantero' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('nacionalidad')->label('Nac.')->toggleable(),
                TextColumn::make('goles')->label('G')->badge()->color('success'),
                TextColumn::make('asistencias')->label('A')->badge()->color('info'),
                TextColumn::make('partidos_jugados')->label('PJ')->toggleable(),
                TextColumn::make('temporada')->label('Temp.')->badge()->toggleable(),
                TextColumn::make('activo')->label('Activo')->badge()->color(fn ($state) => $state ? 'success' : 'danger')->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),
            ])
            ->filters([
                SelectFilter::make('posicion')->options([
                    'portero' => 'Portero',
                    'defensa' => 'Defensa',
                    'centrocampista' => 'Centrocampista',
                    'delantero' => 'Delantero',
                ]),
                SelectFilter::make('temporada'),
                TernaryFilter::make('activo'),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('sync_sofascore')
                        ->label('Sincronizar SofaScore')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->visible(fn (Jugador $r) => filled($r->sofascore_id))
                        ->action(fn () => Notification::make()
                            ->title('Sincronización pendiente')
                            ->body('Integración con SofaScore disponible próximamente.')
                            ->warning()
                            ->send()),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListJugadores::route('/')];
    }
}

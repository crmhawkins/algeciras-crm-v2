<?php

namespace App\Filament\Resources\Abonados;

use App\Filament\Resources\Abonados\Pages\ListAbonados;
use App\Models\Abonado;
use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AbonadoResource extends Resource
{
    protected static ?string $model = Abonado::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static \UnitEnum|string|null $navigationGroup = 'Zona Socio';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Abonado';

    protected static ?string $pluralModelLabel = 'Abonados';

    protected static ?string $recordTitleAttribute = 'numero_socio';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Socio')->columns(2)->schema([
                Select::make('user_id')->label('Usuario')->relationship('user', 'name')->searchable()->preload()->required(),
                TextInput::make('numero_socio')->label('Número de socio')->required()->maxLength(20)->unique(ignoreRecord: true),
                TextInput::make('temporada')->label('Temporada')->default('2025-2026')->required()->maxLength(20),
                Select::make('tipo_abono')->label('Tipo de abono')->options([
                    'general' => 'General',
                    'socio' => 'Socio',
                    'reducido' => 'Reducido',
                    'infantil' => 'Infantil',
                    'vip' => 'VIP',
                ]),
            ]),
            Section::make('Asiento')->columns(4)->schema([
                TextInput::make('grada')->label('Grada')->maxLength(40),
                TextInput::make('sector')->label('Sector')->maxLength(40),
                TextInput::make('fila')->label('Fila')->maxLength(10),
                TextInput::make('asiento')->label('Asiento')->maxLength(10),
            ]),
            Section::make('Validez')->columns(3)->schema([
                DatePicker::make('valido_desde')->label('Válido desde')->required(),
                DatePicker::make('valido_hasta')->label('Válido hasta')->required(),
                Toggle::make('activo')->label('Activo')->default(true),
                TextInput::make('precio_pagado')->label('Precio pagado')->numeric()->prefix('€')->step(0.01),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('numero_socio')
            ->columns([
                TextColumn::make('numero_socio')->label('Nº socio')->searchable()->fontFamily('mono')->weight('bold'),
                TextColumn::make('user.name')->label('Nombre')->searchable(),
                TextColumn::make('user.email')->label('Email')->toggleable(),
                TextColumn::make('temporada')->label('Temporada')->badge(),
                TextColumn::make('tipo_abono')->label('Tipo')->badge()->color('info'),
                TextColumn::make('grada')->label('Grada')->toggleable(),
                TextColumn::make('asiento')
                    ->label('Asiento')
                    ->state(fn (Abonado $r) => trim(implode(' · ', array_filter([$r->grada, $r->sector, $r->fila, $r->asiento]))) ?: '—'),
                TextColumn::make('valido_hasta')->label('Vence')->date('d/m/Y')->sortable(),
                IconColumn::make('activo')->label('Activo')->boolean(),
                TextColumn::make('precio_pagado')->label('Pagó')->money('EUR')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('temporada'),
                SelectFilter::make('tipo_abono')->options([
                    'general' => 'General',
                    'socio' => 'Socio',
                    'reducido' => 'Reducido',
                    'infantil' => 'Infantil',
                    'vip' => 'VIP',
                ]),
                TernaryFilter::make('activo'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('renovar')
                        ->label('Renovar próxima temporada')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $abonado) {
                                $current = $abonado->temporada;
                                $next = static::nextSeason($current);
                                Abonado::create([
                                    'user_id' => $abonado->user_id,
                                    'numero_socio' => $abonado->numero_socio,
                                    'temporada' => $next,
                                    'tipo_abono' => $abonado->tipo_abono,
                                    'grada' => $abonado->grada,
                                    'sector' => $abonado->sector,
                                    'fila' => $abonado->fila,
                                    'asiento' => $abonado->asiento,
                                    'valido_desde' => now()->copy()->startOfDay(),
                                    'valido_hasta' => now()->copy()->addYear(),
                                    'activo' => true,
                                    'precio_pagado' => $abonado->precio_pagado,
                                ]);
                                $count++;
                            }
                            Notification::make()
                                ->title("Renovados $count abonados")
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function nextSeason(string $current): string
    {
        if (preg_match('/^(\d{4})-(\d{4})$/', $current, $m)) {
            return ((int) $m[1] + 1) . '-' . ((int) $m[2] + 1);
        }
        return $current;
    }

    public static function getPages(): array
    {
        return ['index' => ListAbonados::route('/')];
    }
}

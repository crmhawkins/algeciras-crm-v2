<?php

namespace App\Filament\Resources\Pedidos;

use App\Models\Pedido;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PedidosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->copyable()
                    ->weight('bold'),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('canal')
                    ->label('Canal')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'web' => 'info',
                        'app' => 'success',
                        'tpv' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('cliente.name')
                    ->label('Cliente')
                    ->searchable()
                    ->placeholder(fn (Pedido $r) => $r->nombre_cliente ?? 'Anónimo'),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'borrador' => 'gray',
                        'pendiente_pago' => 'warning',
                        'pagado' => 'success',
                        'preparando' => 'info',
                        'enviado' => 'primary',
                        'entregado' => 'success',
                        'cancelado' => 'danger',
                        'devuelto' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => str_replace('_', ' ', ucfirst($state))),
                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('EUR')
                    ->sortable()
                    ->weight('bold')
                    ->alignRight(),
                TextColumn::make('metodo_pago')
                    ->label('Pago')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'borrador' => 'Borrador',
                        'pendiente_pago' => 'Pendiente de pago',
                        'pagado' => 'Pagado',
                        'preparando' => 'Preparando',
                        'enviado' => 'Enviado',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado',
                        'devuelto' => 'Devuelto',
                    ])
                    ->multiple(),
                SelectFilter::make('canal')
                    ->label('Canal')
                    ->options([
                        'web' => 'Web',
                        'app' => 'App',
                        'tpv' => 'TPV',
                    ])
                    ->multiple(),
                Filter::make('fechas')
                    ->schema([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(fn ($q, array $data) => $q
                        ->when($data['desde'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                        ->when($data['hasta'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('marcar_pagado')
                        ->label('Marcar como pagado')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Pedido $r) => in_array($r->estado, ['borrador', 'pendiente_pago']))
                        ->requiresConfirmation()
                        ->action(function (Pedido $record) {
                            $record->update(['estado' => 'pagado', 'pagado_en' => now()]);
                            Notification::make()->title('Pedido marcado como pagado')->success()->send();
                        }),
                    Action::make('preparando')
                        ->label('Marcar como preparando')
                        ->icon('heroicon-o-archive-box')
                        ->color('info')
                        ->visible(fn (Pedido $r) => $r->estado === 'pagado')
                        ->action(function (Pedido $record) {
                            $record->update(['estado' => 'preparando']);
                            Notification::make()->title('Pedido en preparación')->success()->send();
                        }),
                    Action::make('marcar_enviado')
                        ->label('Marcar como enviado')
                        ->icon('heroicon-o-truck')
                        ->color('primary')
                        ->visible(fn (Pedido $r) => in_array($r->estado, ['pagado', 'preparando']))
                        ->action(function (Pedido $record) {
                            $record->update(['estado' => 'enviado']);
                            Notification::make()->title('Pedido marcado como enviado')->success()->send();
                        }),
                    Action::make('marcar_entregado')
                        ->label('Marcar como entregado')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn (Pedido $r) => $r->estado === 'enviado')
                        ->action(function (Pedido $record) {
                            $record->update(['estado' => 'entregado']);
                            Notification::make()->title('Pedido entregado')->success()->send();
                        }),
                    Action::make('cancelar')
                        ->label('Cancelar pedido')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (Pedido $r) => ! in_array($r->estado, ['cancelado', 'devuelto', 'entregado']))
                        ->requiresConfirmation()
                        ->modalDescription('El stock de los items se restaurará automáticamente.')
                        ->action(function (Pedido $record) {
                            $record->update(['estado' => 'cancelado', 'cancelado_en' => now()]);
                            Notification::make()->title('Pedido cancelado')->success()->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

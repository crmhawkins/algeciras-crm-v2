<?php

namespace App\Filament\Resources\Productos\Tables;

use App\Models\Producto;
use App\Services\StockService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->size('xs'),
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40)
                    ->wrap(),
                TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('precio_base')
                    ->label('Precio')
                    ->money('EUR')
                    ->sortable()
                    ->alignRight(),
                TextColumn::make('precio_oferta')
                    ->label('Oferta')
                    ->money('EUR')
                    ->color('danger')
                    ->alignRight()
                    ->placeholder('—'),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->badge()
                    ->alignCenter()
                    ->color(fn (Producto $r): string => match (true) {
                        $r->stock <= 0 => 'danger',
                        $r->stock <= $r->stock_minimo => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state, Producto $r) => $state . ' / min ' . $r->stock_minimo)
                    ->sortable(),
                IconColumn::make('visible_web')
                    ->label('Web')
                    ->boolean()
                    ->trueIcon('heroicon-s-globe-alt')
                    ->falseIcon('heroicon-o-no-symbol')
                    ->trueColor('success')
                    ->falseColor('gray'),
                IconColumn::make('visible_app')
                    ->label('App')
                    ->boolean()
                    ->trueIcon('heroicon-s-device-phone-mobile')
                    ->falseIcon('heroicon-o-no-symbol')
                    ->trueColor('success')
                    ->falseColor('gray'),
                IconColumn::make('visible_tpv')
                    ->label('TPV')
                    ->boolean()
                    ->trueIcon('heroicon-s-building-storefront')
                    ->falseIcon('heroicon-o-no-symbol')
                    ->trueColor('success')
                    ->falseColor('gray'),
                IconColumn::make('es_destacado')
                    ->label('★')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->trueColor('warning')
                    ->falseIcon('heroicon-o-star')
                    ->falseColor('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('categoria_id')
                    ->label('Categoría')
                    ->relationship('categoria', 'nombre')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                TernaryFilter::make('visible_web')->label('Visible en Web'),
                TernaryFilter::make('visible_app')->label('Visible en App'),
                TernaryFilter::make('visible_tpv')->label('Visible en TPV'),
                TernaryFilter::make('con_stock')
                    ->label('Con stock')
                    ->placeholder('Todos')
                    ->trueLabel('Solo con stock')
                    ->falseLabel('Sin stock')
                    ->queries(
                        true: fn ($q) => $q->where('stock', '>', 0),
                        false: fn ($q) => $q->where('stock', '<=', 0),
                    ),
                TernaryFilter::make('stock_bajo')
                    ->label('Stock bajo (≤ mínimo)')
                    ->placeholder('Todos')
                    ->trueLabel('Stock bajo')
                    ->falseLabel('Stock OK')
                    ->queries(
                        true: fn ($q) => $q->whereColumn('stock', '<=', 'stock_minimo'),
                        false: fn ($q) => $q->whereColumn('stock', '>', 'stock_minimo'),
                    ),
                TernaryFilter::make('es_destacado')->label('Destacados'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('ajustar_stock')
                        ->label('Ajustar stock')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->color('warning')
                        ->schema([
                            TextInput::make('nuevo_stock')
                                ->label('Nuevo stock (absoluto)')
                                ->required()
                                ->numeric()
                                ->integer()
                                ->minValue(0)
                                ->default(fn (Producto $r) => $r->stock),
                            Textarea::make('motivo')
                                ->label('Motivo del ajuste')
                                ->required()
                                ->rows(3)
                                ->placeholder('Ej. recuento físico, merma, ajuste manual, etc.'),
                        ])
                        ->action(function (array $data, Producto $record): void {
                            app(StockService::class)->ajustar(
                                producto: $record,
                                nuevoStock: (int) $data['nuevo_stock'],
                                motivo: $data['motivo'],
                                usuario: auth()->user(),
                            );

                            Notification::make()
                                ->title('Stock ajustado correctamente')
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('activar_web')
                        ->label('Activar en Web')
                        ->icon('heroicon-o-globe-alt')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['visible_web' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('desactivar_web')
                        ->label('Desactivar en Web')
                        ->icon('heroicon-o-no-symbol')
                        ->color('gray')
                        ->action(fn ($records) => $records->each->update(['visible_web' => false]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('activar_app')
                        ->label('Activar en App')
                        ->icon('heroicon-o-device-phone-mobile')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['visible_app' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('activar_tpv')
                        ->label('Activar en TPV')
                        ->icon('heroicon-o-building-storefront')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['visible_tpv' => true]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}

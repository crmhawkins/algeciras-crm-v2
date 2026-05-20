<?php

namespace App\Filament\Resources\Pedidos;

use App\Filament\Resources\Pedidos\Pages\CreatePedido;
use App\Filament\Resources\Pedidos\Pages\EditPedido;
use App\Filament\Resources\Pedidos\Pages\ListPedidos;
use App\Filament\Resources\Pedidos\Pages\ViewPedido;
use App\Filament\Resources\Pedidos\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\Pedidos\RelationManagers\TransaccionesRelationManager;
use App\Models\Pedido;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static \UnitEnum|string|null $navigationGroup = 'Tienda';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Pedido';

    protected static ?string $pluralModelLabel = 'Pedidos';

    protected static ?string $recordTitleAttribute = 'codigo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos del pedido')
                ->columns(3)
                ->schema([
                    TextInput::make('codigo')
                        ->label('Código')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Se generará automáticamente'),
                    Select::make('canal')
                        ->label('Canal')
                        ->required()
                        ->options([
                            'web' => 'Web',
                            'app' => 'App',
                            'tpv' => 'TPV',
                        ]),
                    Select::make('estado')
                        ->label('Estado')
                        ->required()
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
                        ->default('borrador'),
                ]),
            Section::make('Cliente')
                ->columns(2)
                ->schema([
                    Select::make('cliente_id')
                        ->label('Cliente registrado')
                        ->relationship('cliente', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Cliente anónimo'),
                    TextInput::make('nombre_cliente')->label('Nombre cliente'),
                    TextInput::make('email_cliente')->label('Email')->email(),
                    TextInput::make('telefono_cliente')->label('Teléfono'),
                ]),
            Section::make('Envío')
                ->collapsed()
                ->columns(3)
                ->schema([
                    TextInput::make('direccion_envio')->label('Dirección')->columnSpan(3),
                    TextInput::make('cp_envio')->label('CP'),
                    TextInput::make('ciudad_envio')->label('Ciudad'),
                    TextInput::make('provincia_envio')->label('Provincia'),
                    TextInput::make('pais_envio')->label('País')->default('España'),
                ]),
            Section::make('Importes')
                ->columns(4)
                ->schema([
                    TextInput::make('subtotal')->label('Subtotal')->numeric()->prefix('€')->default(0)->step(0.01),
                    TextInput::make('descuento')->label('Descuento')->numeric()->prefix('€')->default(0)->step(0.01),
                    TextInput::make('iva')->label('IVA')->numeric()->prefix('€')->default(0)->step(0.01),
                    TextInput::make('gastos_envio')->label('Gastos envío')->numeric()->prefix('€')->default(0)->step(0.01),
                    TextInput::make('total')->label('TOTAL')->required()->numeric()->prefix('€')->default(0)->step(0.01)->columnSpanFull(),
                ]),
            Section::make('Pago')
                ->columns(3)
                ->schema([
                    TextInput::make('metodo_pago')->label('Método de pago'),
                    TextInput::make('referencia_pago')->label('Referencia pago'),
                    DateTimePicker::make('pagado_en')->label('Pagado en'),
                ]),
            Section::make('Notas')
                ->columns(2)
                ->collapsed()
                ->schema([
                    Textarea::make('notas_cliente')->label('Notas del cliente')->rows(3),
                    Textarea::make('notas_internas')->label('Notas internas')->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return PedidosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            TransaccionesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPedidos::route('/'),
            'create' => CreatePedido::route('/create'),
            'view' => ViewPedido::route('/{record}'),
            'edit' => EditPedido::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['codigo', 'email_cliente', 'nombre_cliente'];
    }
}

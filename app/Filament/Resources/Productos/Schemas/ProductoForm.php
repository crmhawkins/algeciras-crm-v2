<?php

namespace App\Filament\Resources\Productos\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('producto')
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Datos básicos')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Section::make('Identificación')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('nombre')
                                        ->label('Nombre')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn ($state, callable $set, $context) =>
                                            $context === 'create' ? $set('slug', Str::slug((string) $state)) : null
                                        ),
                                    TextInput::make('slug')
                                        ->label('Slug (URL)')
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(ignoreRecord: true),
                                    TextInput::make('sku')
                                        ->label('SKU')
                                        ->required()
                                        ->maxLength(64)
                                        ->unique(ignoreRecord: true),
                                    TextInput::make('marca')
                                        ->label('Marca')
                                        ->maxLength(120),
                                    Select::make('categoria_id')
                                        ->label('Categoría')
                                        ->relationship('categoria', 'nombre')
                                        ->searchable()
                                        ->preload()
                                        ->required(),
                                ]),
                            Section::make('Descripción')
                                ->schema([
                                    Textarea::make('descripcion_corta')
                                        ->label('Descripción corta')
                                        ->rows(3)
                                        ->maxLength(500),
                                    RichEditor::make('descripcion_larga')
                                        ->label('Descripción larga')
                                        ->columnSpanFull(),
                                ]),
                            Section::make('Etiquetas')
                                ->columns(3)
                                ->schema([
                                    Toggle::make('es_destacado')
                                        ->label('Destacado')
                                        ->inline(false),
                                    Toggle::make('es_novedad')
                                        ->label('Novedad')
                                        ->inline(false),
                                    Toggle::make('requiere_envio')
                                        ->label('Requiere envío')
                                        ->inline(false)
                                        ->default(true),
                                ]),
                        ]),
                    Tab::make('Precios')
                        ->icon('heroicon-o-currency-euro')
                        ->schema([
                            Section::make('Precios principales')
                                ->columns(3)
                                ->schema([
                                    TextInput::make('precio_base')
                                        ->label('Precio base')
                                        ->required()
                                        ->numeric()
                                        ->prefix('€')
                                        ->step(0.01)
                                        ->minValue(0),
                                    TextInput::make('precio_oferta')
                                        ->label('Precio oferta')
                                        ->numeric()
                                        ->prefix('€')
                                        ->step(0.01)
                                        ->minValue(0)
                                        ->helperText('Si se rellena, se mostrará como precio rebajado'),
                                    TextInput::make('coste')
                                        ->label('Coste interno')
                                        ->numeric()
                                        ->prefix('€')
                                        ->step(0.01)
                                        ->minValue(0)
                                        ->helperText('No visible para el cliente'),
                                    TextInput::make('iva_pct')
                                        ->label('IVA (%)')
                                        ->required()
                                        ->numeric()
                                        ->suffix('%')
                                        ->step(0.01)
                                        ->default(21)
                                        ->minValue(0)
                                        ->maxValue(100),
                                ]),
                        ]),
                    Tab::make('Stock')
                        ->icon('heroicon-o-archive-box')
                        ->schema([
                            Section::make('Inventario')
                                ->columns(3)
                                ->schema([
                                    TextInput::make('stock')
                                        ->label('Stock actual')
                                        ->required()
                                        ->numeric()
                                        ->integer()
                                        ->default(0)
                                        ->minValue(0)
                                        ->helperText('Para ajustes con motivo usa la acción "Ajustar stock" desde el listado'),
                                    TextInput::make('stock_minimo')
                                        ->label('Stock mínimo')
                                        ->required()
                                        ->numeric()
                                        ->integer()
                                        ->default(0)
                                        ->minValue(0)
                                        ->helperText('Aviso cuando stock cae por debajo'),
                                    TextInput::make('peso_gramos')
                                        ->label('Peso (gramos)')
                                        ->numeric()
                                        ->integer()
                                        ->minValue(0)
                                        ->suffix('g'),
                                ]),
                        ]),
                    Tab::make('Visibilidad por canal')
                        ->icon('heroicon-o-eye')
                        ->schema([
                            Section::make('¿Dónde se muestra este producto?')
                                ->description('Activa los canales donde quieres que sea visible')
                                ->columns(3)
                                ->schema([
                                    Toggle::make('visible_web')
                                        ->label('Visible en Web')
                                        ->onColor('success')
                                        ->offColor('gray')
                                        ->default(true)
                                        ->inline(false),
                                    Toggle::make('visible_app')
                                        ->label('Visible en App')
                                        ->onColor('success')
                                        ->offColor('gray')
                                        ->default(true)
                                        ->inline(false),
                                    Toggle::make('visible_tpv')
                                        ->label('Visible en TPV')
                                        ->onColor('success')
                                        ->offColor('gray')
                                        ->default(true)
                                        ->inline(false),
                                ]),
                        ]),
                    Tab::make('SEO')
                        ->icon('heroicon-o-magnifying-glass')
                        ->schema([
                            Section::make('Metadatos SEO')
                                ->schema([
                                    TextInput::make('meta_titulo')
                                        ->label('Meta título')
                                        ->maxLength(160)
                                        ->helperText('Ideal entre 50 y 60 caracteres'),
                                    Textarea::make('meta_descripcion')
                                        ->label('Meta descripción')
                                        ->rows(3)
                                        ->maxLength(255)
                                        ->helperText('Ideal entre 120 y 160 caracteres'),
                                ]),
                        ]),
                ]),
        ]);
    }
}

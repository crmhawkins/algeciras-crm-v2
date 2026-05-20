<?php

namespace App\Filament\Resources\OfertasSocios;

use App\Filament\Resources\OfertasSocios\Pages\ListOfertasSocios;
use App\Models\OfertaSocio;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class OfertaSocioResource extends Resource
{
    protected static ?string $model = OfertaSocio::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    protected static \UnitEnum|string|null $navigationGroup = 'Zona Socio';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Oferta socio';

    protected static ?string $pluralModelLabel = 'Ofertas socio';

    protected static ?string $recordTitleAttribute = 'titulo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('negocio_id')->label('Negocio')->relationship('negocio', 'nombre')->searchable()->preload()->required(),
            TextInput::make('titulo')->label('Título')->required()->maxLength(180),
            TextInput::make('descuento_pct')->label('Descuento (%)')->numeric()->suffix('%')->step(0.01),
            DatePicker::make('valido_desde')->label('Desde')->required(),
            DatePicker::make('valido_hasta')->label('Hasta')->required(),
            FileUpload::make('imagen')->label('Imagen')->image()->disk('public')->directory('ofertas'),
            Toggle::make('activo')->label('Activo')->default(true),
            Textarea::make('descripcion')->label('Descripción')->rows(3)->columnSpanFull(),
            Textarea::make('condiciones')->label('Condiciones')->rows(2)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen')->label('')->disk('public')->circular(),
                TextColumn::make('titulo')->label('Título')->searchable()->weight('bold'),
                TextColumn::make('negocio.nombre')->label('Negocio')->badge()->color('info'),
                TextColumn::make('descuento_pct')->label('Dto.')->suffix('%')->badge()->color('warning'),
                TextColumn::make('valido_desde')->label('Desde')->date('d/m/Y'),
                TextColumn::make('valido_hasta')->label('Hasta')->date('d/m/Y'),
                TextColumn::make('clicks')->label('Clicks')->badge()->color('info'),
                IconColumn::make('activo')->label('Activo')->boolean(),
            ])
            ->filters([
                SelectFilter::make('negocio_id')->label('Negocio')->relationship('negocio', 'nombre')->searchable()->preload(),
                TernaryFilter::make('activo'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListOfertasSocios::route('/')];
    }
}

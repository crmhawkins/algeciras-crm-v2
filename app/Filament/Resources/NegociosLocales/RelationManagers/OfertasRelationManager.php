<?php

namespace App\Filament\Resources\NegociosLocales\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OfertasRelationManager extends RelationManager
{
    protected static string $relationship = 'ofertas';

    protected static ?string $title = 'Ofertas';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('titulo')->label('Título')->required()->maxLength(180),
            TextInput::make('descuento_pct')->label('Descuento (%)')->numeric()->suffix('%'),
            DatePicker::make('valido_desde')->label('Desde')->required(),
            DatePicker::make('valido_hasta')->label('Hasta')->required(),
            FileUpload::make('imagen')->label('Imagen')->image()->disk('public')->directory('ofertas'),
            Toggle::make('activo')->label('Activo')->default(true),
            Textarea::make('descripcion')->label('Descripción')->rows(2)->columnSpanFull(),
            Textarea::make('condiciones')->label('Condiciones')->rows(2)->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen')->label('')->disk('public')->circular(),
                TextColumn::make('titulo')->label('Título')->searchable()->weight('bold'),
                TextColumn::make('descuento_pct')->label('Dto.')->suffix('%')->badge()->color('warning'),
                TextColumn::make('valido_desde')->label('Desde')->date('d/m/Y'),
                TextColumn::make('valido_hasta')->label('Hasta')->date('d/m/Y'),
                TextColumn::make('clicks')->label('Clicks')->badge()->color('info'),
                IconColumn::make('activo')->label('Activo')->boolean(),
            ])
            ->headerActions([CreateAction::make()->label('Nueva oferta')])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}

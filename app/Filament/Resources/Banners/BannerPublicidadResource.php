<?php

namespace App\Filament\Resources\Banners;

use App\Filament\Resources\Banners\Pages\ListBanners;
use App\Models\BannerPublicidad;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BannerPublicidadResource extends Resource
{
    protected static ?string $model = BannerPublicidad::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static \UnitEnum|string|null $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Banner';

    protected static ?string $pluralModelLabel = 'Banners publicitarios';

    protected static ?string $recordTitleAttribute = 'titulo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos')->columns(2)->schema([
                TextInput::make('titulo')->label('Título')->required()->maxLength(180),
                Select::make('posicion')->label('Posición')->required()->options([
                    'home_top' => 'Home — superior',
                    'home_mid' => 'Home — central',
                    'home_bottom' => 'Home — inferior',
                    'tienda' => 'Tienda',
                    'noticias' => 'Noticias',
                    'sidebar' => 'Sidebar',
                ]),
                TextInput::make('enlace')->label('Enlace')->url()->maxLength(500)->columnSpanFull(),
                TextInput::make('orden')->label('Orden')->numeric()->integer()->default(0),
                Toggle::make('activo')->label('Activo')->default(true),
                DateTimePicker::make('desde')->label('Vigencia desde'),
                DateTimePicker::make('hasta')->label('Vigencia hasta'),
            ]),
            Section::make('Imágenes')->columns(2)->schema([
                FileUpload::make('imagen_desktop')->label('Imagen desktop')->image()->imageEditor()->disk('public')->directory('banners'),
                FileUpload::make('imagen_mobile')->label('Imagen móvil')->image()->imageEditor()->disk('public')->directory('banners'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('orden')
            ->defaultSort('orden')
            ->columns([
                ImageColumn::make('imagen_desktop')->label('Desktop')->disk('public')->height(40),
                TextColumn::make('titulo')->label('Título')->searchable()->weight('bold'),
                TextColumn::make('posicion')->label('Posición')->badge()->color('info'),
                TextColumn::make('clicks')->label('Clicks')->badge()->color('success'),
                TextColumn::make('impresiones')->label('Impresiones')->badge()->color('gray'),
                TextColumn::make('desde')->label('Desde')->dateTime('d/m/Y')->placeholder('—'),
                TextColumn::make('hasta')->label('Hasta')->dateTime('d/m/Y')->placeholder('—'),
                IconColumn::make('activo')->label('Activo')->boolean(),
            ])
            ->filters([
                SelectFilter::make('posicion'),
                TernaryFilter::make('activo'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListBanners::route('/')];
    }
}

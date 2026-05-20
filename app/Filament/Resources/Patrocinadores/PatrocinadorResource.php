<?php

namespace App\Filament\Resources\Patrocinadores;

use App\Filament\Resources\Patrocinadores\Pages\ListPatrocinadores;
use App\Models\Patrocinador;
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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PatrocinadorResource extends Resource
{
    protected static ?string $model = Patrocinador::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static \UnitEnum|string|null $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Patrocinador';

    protected static ?string $pluralModelLabel = 'Patrocinadores';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos del patrocinador')->columns(2)->schema([
                TextInput::make('nombre')->label('Nombre')->required()->maxLength(180),
                Select::make('tipo')->label('Tipo')->required()->options([
                    'principal' => 'Principal',
                    'oficial' => 'Oficial',
                    'colaborador' => 'Colaborador',
                    'patrocinador' => 'Patrocinador',
                ])->default('colaborador'),
                TextInput::make('enlace_web')->label('Web')->url()->maxLength(255),
                TextInput::make('orden')->label('Orden')->numeric()->integer()->default(0),
                DatePicker::make('desde')->label('Vigencia desde'),
                DatePicker::make('hasta')->label('Vigencia hasta'),
                Toggle::make('activo')->label('Activo')->default(true),
            ]),
            Section::make('Logo')->schema([
                FileUpload::make('logo')->label('Logo')->image()->disk('public')->directory('patrocinadores'),
            ]),
            Section::make('Descripción')->schema([
                Textarea::make('descripcion')->label('Descripción')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('orden')
            ->defaultSort('orden')
            ->columns([
                ImageColumn::make('logo')->label('Logo')->disk('public')->height(40),
                TextColumn::make('nombre')->label('Nombre')->searchable()->weight('bold'),
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'principal' => 'warning',
                        'oficial' => 'gray',
                        'colaborador' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('desde')->label('Desde')->date('d/m/Y')->placeholder('—'),
                TextColumn::make('hasta')->label('Hasta')->date('d/m/Y')->placeholder('—'),
                IconColumn::make('activo')->label('Activo')->boolean(),
            ])
            ->filters([
                SelectFilter::make('tipo')->options([
                    'principal' => 'Principal',
                    'oficial' => 'Oficial',
                    'colaborador' => 'Colaborador',
                    'patrocinador' => 'Patrocinador',
                ]),
                TernaryFilter::make('activo'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListPatrocinadores::route('/')];
    }
}

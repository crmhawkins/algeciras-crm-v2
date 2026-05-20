<?php

namespace App\Filament\Resources\Noticias;

use App\Filament\Resources\Noticias\Pages\ListNoticias;
use App\Models\Noticia;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class NoticiaResource extends Resource
{
    protected static ?string $model = Noticia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static \UnitEnum|string|null $navigationGroup = 'Contenidos Web';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Noticia';

    protected static ?string $pluralModelLabel = 'Noticias';

    protected static ?string $recordTitleAttribute = 'titulo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('noticia')->columnSpanFull()->tabs([
                Tab::make('Contenido')->icon('heroicon-o-document-text')->schema([
                    Section::make()->columns(2)->schema([
                        TextInput::make('titulo')->label('Título')->required()->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, $context) =>
                                $context === 'create' ? $set('slug', Str::slug((string) $state)) : null
                            ),
                        TextInput::make('slug')->label('Slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                        Select::make('categoria_id')
                            ->label('Categoría')
                            ->relationship('categoria', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('autor_id')
                            ->label('Autor')
                            ->relationship('autor', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id())
                            ->required(),
                    ]),
                    Section::make('Resumen y cuerpo')->schema([
                        Textarea::make('extracto')->label('Extracto')->rows(3)->maxLength(500),
                        RichEditor::make('contenido')->label('Contenido')->required()->columnSpanFull(),
                    ]),
                    Section::make('Imagen destacada')->schema([
                        FileUpload::make('imagen_destacada')
                            ->label('Imagen')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('noticias'),
                    ]),
                    TagsInput::make('etiquetas')->label('Etiquetas')->columnSpanFull(),
                ]),
                Tab::make('Publicación')->icon('heroicon-o-clock')->schema([
                    Toggle::make('publicada')->label('Publicada')->default(false),
                    Toggle::make('destacada_home')->label('Destacada en home'),
                    DateTimePicker::make('publicada_en')->label('Programar publicación')->helperText('Déjalo en blanco para publicar inmediatamente'),
                ]),
                Tab::make('SEO')->icon('heroicon-o-magnifying-glass')->schema([
                    TextInput::make('meta_titulo')->label('Meta título')->maxLength(160),
                    Textarea::make('meta_descripcion')->label('Meta descripción')->rows(3)->maxLength(255),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('publicada_en', 'desc')
            ->columns([
                ImageColumn::make('imagen_destacada')->label('Imagen')->disk('public')->height(50),
                TextColumn::make('titulo')->label('Título')->searchable()->weight('bold')->limit(50)->wrap(),
                TextColumn::make('categoria.nombre')->label('Categoría')->badge()
                    ->color(fn ($state, Noticia $r) => $r->categoria?->color ?? 'gray'),
                TextColumn::make('autor.name')->label('Autor')->toggleable(),
                TextColumn::make('vistas')->label('Vistas')->badge()->color('info')->toggleable(),
                IconColumn::make('publicada')->label('Pub.')->boolean(),
                IconColumn::make('destacada_home')->label('★')->boolean()->trueIcon('heroicon-s-star')->trueColor('warning'),
                TextColumn::make('publicada_en')->label('Publicada en')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('categoria_id')
                    ->label('Categoría')
                    ->relationship('categoria', 'nombre')
                    ->multiple()
                    ->preload(),
                TernaryFilter::make('publicada'),
                TernaryFilter::make('destacada_home')->label('Destacada home'),
                TrashedFilter::make(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return ['index' => ListNoticias::route('/')];
    }
}

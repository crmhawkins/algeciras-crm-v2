<?php

namespace App\Filament\Resources\PaginasWeb;

use App\Filament\Resources\PaginasWeb\Pages\ListPaginasWeb;
use App\Models\PaginaWeb;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PaginaWebResource extends Resource
{
    protected static ?string $model = PaginaWeb::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static \UnitEnum|string|null $navigationGroup = 'Contenidos Web';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Página web';

    protected static ?string $pluralModelLabel = 'Páginas web';

    protected static ?string $recordTitleAttribute = 'titulo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('pagina')->columnSpanFull()->tabs([
                Tab::make('Contenido')->icon('heroicon-o-document-text')->schema([
                    Section::make()->columns(2)->schema([
                        TextInput::make('titulo')->label('Título')->required()->maxLength(200)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, $context) =>
                                $context === 'create' ? $set('slug', Str::slug((string) $state)) : null
                            ),
                        TextInput::make('slug')->label('Slug')->required()->maxLength(200)->unique(ignoreRecord: true),
                        Select::make('padre_id')
                            ->label('Página padre')
                            ->relationship('padre', 'titulo', fn ($q, $record) =>
                                $record ? $q->where('id', '!=', $record->id) : $q
                            )
                            ->searchable()
                            ->preload(),
                        TextInput::make('orden_menu')->label('Orden en menú')->numeric()->integer()->default(0),
                    ]),
                    Section::make('Cuerpo')->schema([
                        RichEditor::make('contenido')->label('Contenido')->columnSpanFull(),
                    ]),
                    Section::make('Imagen destacada')->schema([
                        FileUpload::make('imagen_destacada')
                            ->label('Imagen')
                            ->image()
                            ->disk('public')
                            ->directory('paginas'),
                    ]),
                    Toggle::make('publicada')->label('Publicada')->default(false),
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
            ->reorderable('orden_menu')
            ->defaultSort('orden_menu')
            ->columns([
                TextColumn::make('titulo')->label('Título')->searchable()->weight('bold'),
                TextColumn::make('slug')->label('Slug')->fontFamily('mono')->size('xs')->copyable(),
                TextColumn::make('padre.titulo')->label('Padre')->badge()->color('gray')->placeholder('—'),
                IconColumn::make('publicada')->label('Publicada')->boolean(),
                TextColumn::make('updated_at')->label('Actualizada')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('publicada')->label('Publicadas'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListPaginasWeb::route('/')];
    }
}

<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static \UnitEnum|string|null $navigationGroup = 'Sistema';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Usuario';

    protected static ?string $pluralModelLabel = 'Usuarios';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos del usuario')->columns(2)->schema([
                TextInput::make('name')->label('Nombre')->required()->maxLength(180),
                TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true)->maxLength(180),
                TextInput::make('telefono')->label('Teléfono')->maxLength(40),
                TextInput::make('dni')->label('DNI / NIF')->maxLength(20),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn ($context) => $context === 'create')
                    ->minLength(8)
                    ->helperText('Déjalo en blanco para mantener la contraseña actual'),
                FileUpload::make('avatar')->label('Avatar')->image()->imageEditor()->disk('public')->directory('avatars'),
            ]),
            Section::make('Permisos')->schema([
                Select::make('roles')
                    ->label('Roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('avatar')->label('')->disk('public')->circular(),
                TextColumn::make('name')->label('Nombre')->searchable()->weight('bold'),
                TextColumn::make('email')->label('Email')->searchable()->copyable(),
                TextColumn::make('telefono')->label('Teléfono')->toggleable(),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(',')
                    ->color(fn ($state) => match (strtolower((string) $state)) {
                        'super-admin' => 'danger',
                        'gestor-web', 'gestor-tienda' => 'warning',
                        'tpv' => 'info',
                        'editor' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label('Registrado')->dateTime('d/m/Y')->toggleable(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListUsers::route('/')];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'dni'];
    }
}

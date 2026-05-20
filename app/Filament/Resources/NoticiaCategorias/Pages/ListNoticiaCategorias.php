<?php

namespace App\Filament\Resources\NoticiaCategorias\Pages;

use App\Filament\Resources\NoticiaCategorias\NoticiaCategoriaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNoticiaCategorias extends ListRecords
{
    protected static string $resource = NoticiaCategoriaResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nueva categoría')];
    }
}

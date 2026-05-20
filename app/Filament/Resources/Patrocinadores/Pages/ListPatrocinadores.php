<?php

namespace App\Filament\Resources\Patrocinadores\Pages;

use App\Filament\Resources\Patrocinadores\PatrocinadorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPatrocinadores extends ListRecords
{
    protected static string $resource = PatrocinadorResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nuevo patrocinador')];
    }
}

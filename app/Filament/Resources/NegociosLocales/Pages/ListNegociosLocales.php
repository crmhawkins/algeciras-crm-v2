<?php

namespace App\Filament\Resources\NegociosLocales\Pages;

use App\Filament\Resources\NegociosLocales\NegocioLocalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNegociosLocales extends ListRecords
{
    protected static string $resource = NegocioLocalResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nuevo negocio')];
    }
}

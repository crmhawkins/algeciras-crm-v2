<?php

namespace App\Filament\Resources\OfertasSocios\Pages;

use App\Filament\Resources\OfertasSocios\OfertaSocioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOfertasSocios extends ListRecords
{
    protected static string $resource = OfertaSocioResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nueva oferta')];
    }
}

<?php

namespace App\Filament\Resources\Abonados\Pages;

use App\Filament\Resources\Abonados\AbonadoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAbonados extends ListRecords
{
    protected static string $resource = AbonadoResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nuevo abonado')];
    }
}

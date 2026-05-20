<?php

namespace App\Filament\Resources\Banners\Pages;

use App\Filament\Resources\Banners\BannerPublicidadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBanners extends ListRecords
{
    protected static string $resource = BannerPublicidadResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nuevo banner')];
    }
}

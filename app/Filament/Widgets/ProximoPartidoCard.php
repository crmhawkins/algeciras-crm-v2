<?php

namespace App\Filament\Widgets;

use App\Models\Partido;
use Filament\Widgets\Widget;

class ProximoPartidoCard extends Widget
{
    protected static ?int $sort = 2;

    protected string $view = 'filament.widgets.proximo-partido-card';

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $partido = Partido::query()
            ->where('fecha', '>=', now())
            ->whereIn('estado', [Partido::ESTADO_PROGRAMADO, Partido::ESTADO_APLAZADO])
            ->orderBy('fecha')
            ->with(['local', 'visitante'])
            ->first();

        return [
            'partido' => $partido,
            'countdownIso' => $partido?->fecha?->toIso8601String(),
        ];
    }
}

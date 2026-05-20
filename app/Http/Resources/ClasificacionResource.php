<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClasificacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'posicion' => $this->posicion,
            'temporada' => $this->temporada,
            'competicion' => $this->competicion,
            'equipo' => $this->whenLoaded('equipo', fn () => [
                'id' => $this->equipo?->id,
                'nombre' => $this->equipo?->nombre,
                'escudo' => $this->equipo?->escudo,
            ]),
            'partidos_jugados' => $this->partidos_jugados,
            'victorias' => $this->victorias,
            'empates' => $this->empates,
            'derrotas' => $this->derrotas,
            'goles_favor' => $this->goles_favor,
            'goles_contra' => $this->goles_contra,
            'diferencia_goles' => $this->diferencia_goles,
            'puntos' => $this->puntos,
            'actualizado_en' => optional($this->actualizado_en)?->toIso8601String(),
        ];
    }
}

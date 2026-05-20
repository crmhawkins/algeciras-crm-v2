<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JugadorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre_completo' => $this->nombre_completo,
            'dorsal' => $this->dorsal,
            'posicion' => $this->posicion,
            'fecha_nacimiento' => optional($this->fecha_nacimiento)?->toDateString(),
            'nacionalidad' => $this->nacionalidad,
            'foto' => $this->foto,
            'biografia' => $this->biografia,
            'altura_cm' => $this->altura_cm,
            'peso_kg' => $this->peso_kg,
            'equipo' => $this->equipo,
            'temporada' => $this->temporada,
            'estadisticas' => [
                'goles' => $this->goles,
                'asistencias' => $this->asistencias,
                'minutos_jugados' => $this->minutos_jugados,
                'partidos_jugados' => $this->partidos_jugados,
                'tarjetas_amarillas' => $this->tarjetas_amarillas,
                'tarjetas_rojas' => $this->tarjetas_rojas,
            ],
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'temporada' => $this->temporada,
            'jornada' => $this->jornada,
            'competicion' => $this->competicion,
            'fecha' => optional($this->fecha)?->toIso8601String(),
            'estadio' => $this->estadio,
            'arbitro' => $this->arbitro,
            'estado' => $this->estado,
            'local' => [
                'id' => $this->local_id,
                'nombre' => $this->local_nombre,
                'escudo' => $this->local_escudo,
                'goles' => $this->goles_local,
            ],
            'visitante' => [
                'id' => $this->visitante_id,
                'nombre' => $this->visitante_nombre,
                'escudo' => $this->visitante_escudo,
                'goles' => $this->goles_visitante,
            ],
            'entradas' => [
                'evento_id' => $this->compralaentrada_evento_id,
                'sesion_id' => $this->compralaentrada_sesion_id,
            ],
            'resumen_url' => $this->resumen_url,
        ];
    }
}

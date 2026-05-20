<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatrocinadorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'logo' => $this->logo,
            'enlace_web' => $this->enlace_web,
            'descripcion' => $this->descripcion,
            'tipo' => $this->tipo,
            'orden' => $this->orden,
        ];
    }
}

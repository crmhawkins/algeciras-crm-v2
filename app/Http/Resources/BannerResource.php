<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'imagen_desktop' => $this->imagen_desktop,
            'imagen_mobile' => $this->imagen_mobile,
            'enlace' => $this->enlace,
            'posicion' => $this->posicion,
            'orden' => $this->orden,
        ];
    }
}

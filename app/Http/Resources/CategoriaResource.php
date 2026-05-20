<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'slug' => $this->slug,
            'descripcion' => $this->descripcion,
            'imagen' => $this->imagen,
            'orden' => $this->orden,
            'parent_id' => $this->parent_id,
            'hijas' => $this->whenLoaded('hijas', fn () => self::collection($this->hijas)),
        ];
    }
}

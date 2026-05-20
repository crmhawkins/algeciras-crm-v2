<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoticiaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'titulo' => $this->titulo,
            'extracto' => $this->extracto,
            'contenido' => $this->when($request->routeIs('api.noticias.show'), $this->contenido),
            'imagen_destacada' => $this->imagen_destacada,
            'etiquetas' => $this->etiquetas,
            'destacada_home' => $this->destacada_home,
            'vistas' => $this->vistas,
            'publicada_en' => optional($this->publicada_en)?->toIso8601String(),
            'categoria' => $this->whenLoaded('categoria', fn () => [
                'id' => $this->categoria?->id,
                'nombre' => $this->categoria?->nombre,
                'slug' => $this->categoria?->slug,
                'color' => $this->categoria?->color,
            ]),
            'autor' => $this->whenLoaded('autor', fn () => [
                'id' => $this->autor?->id,
                'nombre' => $this->autor?->name,
            ]),
            'meta_titulo' => $this->meta_titulo,
            'meta_descripcion' => $this->meta_descripcion,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'descripcion_corta' => $this->descripcion_corta,
            'descripcion_larga' => $this->descripcion_larga,
            'marca' => $this->marca,
            'precio_base' => (float) $this->precio_base,
            'precio_oferta' => $this->precio_oferta !== null ? (float) $this->precio_oferta : null,
            'precio_final' => $this->precio_final,
            'en_oferta' => $this->en_oferta,
            'iva_pct' => (float) $this->iva_pct,
            'stock' => (int) $this->stock,
            'es_destacado' => $this->es_destacado,
            'es_novedad' => $this->es_novedad,
            'requiere_envio' => $this->requiere_envio,
            'meta_titulo' => $this->meta_titulo,
            'meta_descripcion' => $this->meta_descripcion,
            'categoria' => $this->whenLoaded('categoria', fn () => [
                'id' => $this->categoria->id,
                'nombre' => $this->categoria->nombre,
                'slug' => $this->categoria->slug,
            ]),
            'imagenes' => $this->whenLoaded('imagenes', fn () => $this->imagenes->map(fn ($img) => [
                'id' => $img->id,
                'ruta' => $img->ruta,
                'alt' => $img->alt,
                'es_principal' => $img->es_principal,
                'orden' => $img->orden,
            ])),
            'variantes' => $this->whenLoaded('variantes', fn () => $this->variantes->map(fn ($v) => [
                'id' => $v->id,
                'nombre' => $v->nombre,
                'sku' => $v->sku,
                'atributos' => $v->atributos,
                'precio_extra' => (float) $v->precio_extra,
                'stock' => (int) $v->stock,
                'imagen' => $v->imagen,
            ])),
        ];
    }
}

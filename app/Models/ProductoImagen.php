<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoImagen extends Model
{
    protected $table = 'producto_imagenes';

    protected $fillable = [
        'producto_id',
        'variante_id',
        'ruta',
        'alt',
        'orden',
        'es_principal',
    ];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
            'es_principal' => 'boolean',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function variante(): BelongsTo
    {
        return $this->belongsTo(ProductoVariante::class, 'variante_id');
    }
}

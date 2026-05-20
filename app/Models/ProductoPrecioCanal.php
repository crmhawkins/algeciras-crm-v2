<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoPrecioCanal extends Model
{
    protected $table = 'producto_precios_canal';

    protected $fillable = [
        'producto_id',
        'variante_id',
        'canal',
        'precio',
        'descuento_pct',
        'descuento_socio_pct',
        'desde',
        'hasta',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'descuento_pct' => 'decimal:2',
            'descuento_socio_pct' => 'decimal:2',
            'desde' => 'datetime',
            'hasta' => 'datetime',
            'activo' => 'boolean',
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

    public function scopeActivo(Builder $q): Builder
    {
        $now = now();

        return $q->where('activo', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('desde')->orWhere('desde', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('hasta')->orWhere('hasta', '>=', $now);
            });
    }

    public function scopeCanal(Builder $q, string $canal): Builder
    {
        return $q->where('canal', $canal);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductoVariante extends Model
{
    use HasFactory;

    protected $table = 'producto_variantes';

    protected $fillable = [
        'producto_id',
        'nombre',
        'sku',
        'atributos',
        'precio_extra',
        'stock',
        'imagen',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'atributos' => 'array',
            'precio_extra' => 'decimal:2',
            'stock' => 'integer',
            'orden' => 'integer',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ProductoImagen::class, 'variante_id');
    }

    public function movimientosStock(): HasMany
    {
        return $this->hasMany(StockMovimiento::class, 'variante_id');
    }
}

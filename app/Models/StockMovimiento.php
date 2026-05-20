<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovimiento extends Model
{
    public $timestamps = false;

    protected $table = 'stock_movimientos';

    protected $fillable = [
        'producto_id',
        'variante_id',
        'tipo',
        'cantidad',
        'stock_antes',
        'stock_despues',
        'motivo',
        'referencia_tipo',
        'referencia_id',
        'usuario_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'stock_antes' => 'integer',
            'stock_despues' => 'integer',
            'created_at' => 'datetime',
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

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function referencia(): MorphTo
    {
        return $this->morphTo(null, 'referencia_tipo', 'referencia_id');
    }
}

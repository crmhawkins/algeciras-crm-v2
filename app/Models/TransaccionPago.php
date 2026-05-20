<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaccionPago extends Model
{
    protected $table = 'transacciones_pago';

    protected $fillable = [
        'pedido_id',
        'gateway',
        'referencia',
        'monto',
        'moneda',
        'estado',
        'respuesta_gateway',
        'error_mensaje',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'respuesta_gateway' => 'array',
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}

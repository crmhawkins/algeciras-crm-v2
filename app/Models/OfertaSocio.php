<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfertaSocio extends Model
{
    protected $table = 'ofertas_socios';

    protected $fillable = [
        'negocio_id',
        'titulo',
        'descripcion',
        'imagen',
        'descuento_pct',
        'condiciones',
        'valido_desde',
        'valido_hasta',
        'activo',
        'clicks',
    ];

    protected function casts(): array
    {
        return [
            'descuento_pct' => 'decimal:2',
            'valido_desde' => 'date',
            'valido_hasta' => 'date',
            'activo' => 'boolean',
            'clicks' => 'integer',
        ];
    }

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(NegocioLocal::class, 'negocio_id');
    }

    public function scopeActivo(Builder $q): Builder
    {
        $today = now()->toDateString();

        return $q->where('activo', true)
            ->where('valido_desde', '<=', $today)
            ->where('valido_hasta', '>=', $today);
    }
}

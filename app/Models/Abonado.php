<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Abonado extends Model
{
    protected $table = 'abonados';

    protected $fillable = [
        'user_id',
        'numero_socio',
        'temporada',
        'tipo_abono',
        'grada',
        'sector',
        'fila',
        'asiento',
        'valido_desde',
        'valido_hasta',
        'activo',
        'precio_pagado',
    ];

    protected function casts(): array
    {
        return [
            'valido_desde' => 'date',
            'valido_hasta' => 'date',
            'activo' => 'boolean',
            'precio_pagado' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActivo(Builder $q): Builder
    {
        $today = now()->toDateString();

        return $q->where('activo', true)
            ->where('valido_desde', '<=', $today)
            ->where('valido_hasta', '>=', $today);
    }

    public function scopeTemporada(Builder $q, string $temporada): Builder
    {
        return $q->where('temporada', $temporada);
    }
}

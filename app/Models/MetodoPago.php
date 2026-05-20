<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodos_pago';

    protected $fillable = [
        'codigo',
        'nombre',
        'activo',
        'disponible_web',
        'disponible_app',
        'disponible_tpv',
        'comision_pct',
        'configuracion',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'disponible_web' => 'boolean',
            'disponible_app' => 'boolean',
            'disponible_tpv' => 'boolean',
            'comision_pct' => 'decimal:2',
            'configuracion' => 'array',
            'orden' => 'integer',
        ];
    }

    public function scopeActivo(Builder $q): Builder
    {
        return $q->where('activo', true);
    }

    public function scopeDisponibleEnCanal(Builder $q, string $canal): Builder
    {
        return match ($canal) {
            'web' => $q->where('disponible_web', true),
            'app' => $q->where('disponible_app', true),
            'tpv' => $q->where('disponible_tpv', true),
            default => $q,
        };
    }
}

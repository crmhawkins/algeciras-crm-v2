<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    protected $table = 'cupones';

    protected $fillable = [
        'codigo',
        'descripcion',
        'tipo',
        'valor',
        'compra_minima',
        'usos_max',
        'usos_actual',
        'valido_desde',
        'valido_hasta',
        'solo_socios',
        'canales',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'compra_minima' => 'decimal:2',
            'usos_max' => 'integer',
            'usos_actual' => 'integer',
            'valido_desde' => 'datetime',
            'valido_hasta' => 'datetime',
            'solo_socios' => 'boolean',
            'canales' => 'array',
            'activo' => 'boolean',
        ];
    }

    public function scopeActivo(Builder $q): Builder
    {
        $now = now();

        return $q->where('activo', true)
            ->where('valido_desde', '<=', $now)
            ->where('valido_hasta', '>=', $now)
            ->where(function ($q) {
                $q->whereNull('usos_max')->orWhereColumn('usos_actual', '<', 'usos_max');
            });
    }

    public function disponibleEnCanal(string $canal): bool
    {
        if (empty($this->canales)) {
            return true;
        }

        return in_array($canal, $this->canales, true);
    }

    public function calcularDescuento(float $subtotal): float
    {
        if ($this->compra_minima && $subtotal < (float) $this->compra_minima) {
            return 0;
        }

        return $this->tipo === 'porcentaje'
            ? round($subtotal * ((float) $this->valor / 100), 2)
            : min((float) $this->valor, $subtotal);
    }
}

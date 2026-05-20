<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerPublicidad extends Model
{
    use HasFactory;

    protected $table = 'banners_publicidad';

    protected $fillable = [
        'titulo',
        'imagen_desktop',
        'imagen_mobile',
        'enlace',
        'posicion',
        'desde',
        'hasta',
        'orden',
        'clicks',
        'impresiones',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'desde' => 'datetime',
            'hasta' => 'datetime',
            'orden' => 'integer',
            'clicks' => 'integer',
            'impresiones' => 'integer',
            'activo' => 'boolean',
        ];
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

    public function scopePosicion(Builder $q, string $posicion): Builder
    {
        return $q->where('posicion', $posicion);
    }
}

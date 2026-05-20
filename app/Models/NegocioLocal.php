<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NegocioLocal extends Model
{
    protected $table = 'negocios_locales';

    protected $fillable = [
        'nombre',
        'descripcion',
        'logo',
        'direccion',
        'telefono',
        'web',
        'categoria',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function ofertas(): HasMany
    {
        return $this->hasMany(OfertaSocio::class, 'negocio_id');
    }

    public function scopeActivo(Builder $q): Builder
    {
        return $q->where('activo', true);
    }
}

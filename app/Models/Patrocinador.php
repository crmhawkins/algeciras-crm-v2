<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patrocinador extends Model
{
    use HasFactory;

    protected $table = 'patrocinadores';

    protected $fillable = [
        'nombre',
        'logo',
        'enlace_web',
        'descripcion',
        'tipo',
        'desde',
        'hasta',
        'orden',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'desde' => 'date',
            'hasta' => 'date',
            'orden' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function scopeActivo(Builder $q): Builder
    {
        return $q->where('activo', true);
    }

    public function scopePorTipo(Builder $q, string $tipo): Builder
    {
        return $q->where('tipo', $tipo);
    }

    public function scopeOrdenado(Builder $q): Builder
    {
        return $q->orderBy('orden')->orderBy('nombre');
    }
}

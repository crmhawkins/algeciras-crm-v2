<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Clasificacion extends Model
{
    protected $table = 'clasificacion';

    protected $fillable = [
        'temporada',
        'competicion',
        'equipo_id',
        'posicion',
        'partidos_jugados',
        'victorias',
        'empates',
        'derrotas',
        'goles_favor',
        'goles_contra',
        'puntos',
        'actualizado_en',
    ];

    protected function casts(): array
    {
        return [
            'posicion' => 'integer',
            'partidos_jugados' => 'integer',
            'victorias' => 'integer',
            'empates' => 'integer',
            'derrotas' => 'integer',
            'goles_favor' => 'integer',
            'goles_contra' => 'integer',
            'puntos' => 'integer',
            'actualizado_en' => 'datetime',
        ];
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function scopeTemporada(Builder $q, string $temporada): Builder
    {
        return $q->where('temporada', $temporada);
    }

    public function scopeCompeticion(Builder $q, string $competicion): Builder
    {
        return $q->where('competicion', $competicion);
    }

    public function scopeOrdenada(Builder $q): Builder
    {
        return $q->orderBy('posicion');
    }

    public function getDiferenciaGolesAttribute(): int
    {
        return ($this->goles_favor ?? 0) - ($this->goles_contra ?? 0);
    }
}

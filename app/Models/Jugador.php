<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jugador extends Model
{
    use HasFactory;

    protected $table = 'jugadores';

    protected $fillable = [
        'nombre_completo',
        'dorsal',
        'posicion',
        'fecha_nacimiento',
        'nacionalidad',
        'foto',
        'biografia',
        'altura_cm',
        'peso_kg',
        'equipo',
        'temporada',
        'sofascore_id',
        'goles',
        'asistencias',
        'minutos_jugados',
        'partidos_jugados',
        'tarjetas_amarillas',
        'tarjetas_rojas',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'dorsal' => 'integer',
            'fecha_nacimiento' => 'date',
            'altura_cm' => 'integer',
            'peso_kg' => 'integer',
            'goles' => 'integer',
            'asistencias' => 'integer',
            'minutos_jugados' => 'integer',
            'partidos_jugados' => 'integer',
            'tarjetas_amarillas' => 'integer',
            'tarjetas_rojas' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function scopeActivo(Builder $q): Builder
    {
        return $q->where('activo', true);
    }

    public function scopeTemporada(Builder $q, string $temporada): Builder
    {
        return $q->where('temporada', $temporada);
    }

    public function scopeEquipo(Builder $q, string $equipo): Builder
    {
        return $q->where('equipo', $equipo);
    }

    public function scopePorPosicion(Builder $q, string $posicion): Builder
    {
        return $q->where('posicion', $posicion);
    }
}

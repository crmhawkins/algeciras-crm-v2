<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Partido extends Model
{
    use HasFactory;

    public const ESTADO_PROGRAMADO = 'programado';
    public const ESTADO_EN_JUEGO = 'en_juego';
    public const ESTADO_FINALIZADO = 'finalizado';
    public const ESTADO_APLAZADO = 'aplazado';
    public const ESTADO_CANCELADO = 'cancelado';

    protected $table = 'partidos';

    protected $fillable = [
        'temporada',
        'jornada',
        'competicion',
        'local_id',
        'visitante_id',
        'local_nombre',
        'visitante_nombre',
        'local_escudo',
        'visitante_escudo',
        'goles_local',
        'goles_visitante',
        'fecha',
        'estadio',
        'arbitro',
        'estado',
        'compralaentrada_evento_id',
        'compralaentrada_sesion_id',
        'resumen_url',
    ];

    protected function casts(): array
    {
        return [
            'jornada' => 'integer',
            'goles_local' => 'integer',
            'goles_visitante' => 'integer',
            'fecha' => 'datetime',
        ];
    }

    public function local(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'local_id');
    }

    public function visitante(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'visitante_id');
    }

    public function scopeProximos(Builder $q): Builder
    {
        return $q->where('fecha', '>=', now())
            ->whereIn('estado', [self::ESTADO_PROGRAMADO, self::ESTADO_APLAZADO])
            ->orderBy('fecha');
    }

    public function scopeUltimos(Builder $q): Builder
    {
        return $q->where('fecha', '<', now())
            ->where('estado', self::ESTADO_FINALIZADO)
            ->orderByDesc('fecha');
    }

    public function scopeTemporada(Builder $q, string $temporada): Builder
    {
        return $q->where('temporada', $temporada);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipo extends Model
{
    protected $table = 'equipos';

    protected $fillable = [
        'nombre',
        'escudo',
        'ciudad',
        'sofascore_id',
    ];

    public function partidosLocal(): HasMany
    {
        return $this->hasMany(Partido::class, 'local_id');
    }

    public function partidosVisitante(): HasMany
    {
        return $this->hasMany(Partido::class, 'visitante_id');
    }

    public function clasificaciones(): HasMany
    {
        return $this->hasMany(Clasificacion::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaginaWeb extends Model
{
    protected $table = 'paginas_web';

    protected $fillable = [
        'slug',
        'titulo',
        'contenido',
        'meta_titulo',
        'meta_descripcion',
        'imagen_destacada',
        'publicada',
        'orden_menu',
        'padre_id',
    ];

    protected function casts(): array
    {
        return [
            'publicada' => 'boolean',
            'orden_menu' => 'integer',
        ];
    }

    public function padre(): BelongsTo
    {
        return $this->belongsTo(self::class, 'padre_id');
    }

    public function hijas(): HasMany
    {
        return $this->hasMany(self::class, 'padre_id');
    }

    public function scopePublicadas(Builder $q): Builder
    {
        return $q->where('publicada', true);
    }
}

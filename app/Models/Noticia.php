<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Noticia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'noticias';

    protected $fillable = [
        'slug',
        'titulo',
        'extracto',
        'contenido',
        'categoria_id',
        'autor_id',
        'imagen_destacada',
        'meta_titulo',
        'meta_descripcion',
        'etiquetas',
        'publicada',
        'publicada_en',
        'vistas',
        'destacada_home',
    ];

    protected function casts(): array
    {
        return [
            'etiquetas' => 'array',
            'publicada' => 'boolean',
            'publicada_en' => 'datetime',
            'vistas' => 'integer',
            'destacada_home' => 'boolean',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(NoticiaCategoria::class, 'categoria_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    public function scopePublicadas(Builder $q): Builder
    {
        return $q->where('publicada', true)
            ->where(function ($q) {
                $q->whereNull('publicada_en')->orWhere('publicada_en', '<=', now());
            });
    }

    public function scopeDestacadas(Builder $q): Builder
    {
        return $q->where('destacada_home', true);
    }
}

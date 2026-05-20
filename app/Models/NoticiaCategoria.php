<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NoticiaCategoria extends Model
{
    protected $table = 'noticias_categorias';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'color',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
        ];
    }

    public function noticias(): HasMany
    {
        return $this->hasMany(Noticia::class, 'categoria_id');
    }
}

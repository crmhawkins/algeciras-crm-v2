<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'parent_id',
        'imagen',
        'orden',
        'visible_web',
        'visible_app',
        'visible_tpv',
    ];

    protected function casts(): array
    {
        return [
            'visible_web' => 'boolean',
            'visible_app' => 'boolean',
            'visible_tpv' => 'boolean',
            'orden' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function hijas(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function scopeVisibleEnWeb(Builder $q): Builder
    {
        return $q->where('visible_web', true);
    }

    public function scopeVisibleEnApp(Builder $q): Builder
    {
        return $q->where('visible_app', true);
    }

    public function scopeVisibleEnTpv(Builder $q): Builder
    {
        return $q->where('visible_tpv', true);
    }

    public function scopeRoot(Builder $q): Builder
    {
        return $q->whereNull('parent_id');
    }

    public function scopeOrdenado(Builder $q): Builder
    {
        return $q->orderBy('orden')->orderBy('nombre');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion_corta',
        'descripcion_larga',
        'sku',
        'categoria_id',
        'marca',
        'precio_base',
        'precio_oferta',
        'coste',
        'iva_pct',
        'stock',
        'stock_minimo',
        'peso_gramos',
        'es_destacado',
        'es_novedad',
        'visible_web',
        'visible_app',
        'visible_tpv',
        'requiere_envio',
        'meta_titulo',
        'meta_descripcion',
    ];

    protected function casts(): array
    {
        return [
            'precio_base' => 'decimal:2',
            'precio_oferta' => 'decimal:2',
            'coste' => 'decimal:2',
            'iva_pct' => 'decimal:2',
            'stock' => 'integer',
            'stock_minimo' => 'integer',
            'peso_gramos' => 'integer',
            'es_destacado' => 'boolean',
            'es_novedad' => 'boolean',
            'visible_web' => 'boolean',
            'visible_app' => 'boolean',
            'visible_tpv' => 'boolean',
            'requiere_envio' => 'boolean',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function variantes(): HasMany
    {
        return $this->hasMany(ProductoVariante::class);
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ProductoImagen::class)->orderBy('orden');
    }

    public function imagenPrincipal()
    {
        return $this->hasOne(ProductoImagen::class)->where('es_principal', true);
    }

    public function preciosCanal(): HasMany
    {
        return $this->hasMany(ProductoPrecioCanal::class);
    }

    public function movimientosStock(): HasMany
    {
        return $this->hasMany(StockMovimiento::class);
    }

    public function pedidoItems(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
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

    public function scopeVisibleEnCanal(Builder $q, string $canal): Builder
    {
        return match ($canal) {
            'web' => $q->where('visible_web', true),
            'app' => $q->where('visible_app', true),
            'tpv' => $q->where('visible_tpv', true),
            default => $q,
        };
    }

    public function scopeDestacados(Builder $q): Builder
    {
        return $q->where('es_destacado', true);
    }

    public function scopeNovedades(Builder $q): Builder
    {
        return $q->where('es_novedad', true);
    }

    public function scopeConStock(Builder $q): Builder
    {
        return $q->where('stock', '>', 0);
    }

    public function scopeBuscar(Builder $q, ?string $busqueda): Builder
    {
        if (! $busqueda) {
            return $q;
        }

        return $q->where(function ($q) use ($busqueda) {
            $q->where('nombre', 'like', "%{$busqueda}%")
                ->orWhere('descripcion_corta', 'like', "%{$busqueda}%")
                ->orWhere('sku', 'like', "%{$busqueda}%");
        });
    }

    public function getPrecioFinalAttribute(): float
    {
        return (float) ($this->precio_oferta ?? $this->precio_base);
    }

    public function getEnOfertaAttribute(): bool
    {
        return $this->precio_oferta !== null && $this->precio_oferta < $this->precio_base;
    }

    public function getStockBajoAttribute(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }
}

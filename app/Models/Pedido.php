<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use HasFactory, SoftDeletes;

    public const ESTADO_BORRADOR = 'borrador';
    public const ESTADO_PENDIENTE_PAGO = 'pendiente_pago';
    public const ESTADO_PAGADO = 'pagado';
    public const ESTADO_PREPARANDO = 'preparando';
    public const ESTADO_ENVIADO = 'enviado';
    public const ESTADO_ENTREGADO = 'entregado';
    public const ESTADO_CANCELADO = 'cancelado';
    public const ESTADO_DEVUELTO = 'devuelto';

    public const CANAL_WEB = 'web';
    public const CANAL_APP = 'app';
    public const CANAL_TPV = 'tpv';

    protected $table = 'pedidos';

    protected $fillable = [
        'codigo',
        'canal',
        'estado',
        'cliente_id',
        'nombre_cliente',
        'email_cliente',
        'telefono_cliente',
        'direccion_envio',
        'cp_envio',
        'ciudad_envio',
        'provincia_envio',
        'pais_envio',
        'subtotal',
        'descuento',
        'iva',
        'gastos_envio',
        'total',
        'metodo_pago',
        'referencia_pago',
        'pagado_en',
        'cancelado_en',
        'notas_internas',
        'notas_cliente',
        'cajero_id',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'descuento' => 'decimal:2',
            'iva' => 'decimal:2',
            'gastos_envio' => 'decimal:2',
            'total' => 'decimal:2',
            'pagado_en' => 'datetime',
            'cancelado_en' => 'datetime',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function cajero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cajero_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function transacciones(): HasMany
    {
        return $this->hasMany(TransaccionPago::class);
    }

    public function scopeCanal(Builder $q, string $canal): Builder
    {
        return $q->where('canal', $canal);
    }

    public function scopeEstado(Builder $q, string $estado): Builder
    {
        return $q->where('estado', $estado);
    }

    public function scopePagados(Builder $q): Builder
    {
        return $q->whereIn('estado', [
            self::ESTADO_PAGADO,
            self::ESTADO_PREPARANDO,
            self::ESTADO_ENVIADO,
            self::ESTADO_ENTREGADO,
        ]);
    }

    public function esPagado(): bool
    {
        return in_array($this->estado, [
            self::ESTADO_PAGADO,
            self::ESTADO_PREPARANDO,
            self::ESTADO_ENVIADO,
            self::ESTADO_ENTREGADO,
        ], true);
    }

    public function estaCancelado(): bool
    {
        return in_array($this->estado, [
            self::ESTADO_CANCELADO,
            self::ESTADO_DEVUELTO,
        ], true);
    }

    public static function generarCodigo(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->lockForUpdate()->count();

        return sprintf('ACF-%d-%06d', $year, $last + 1);
    }
}

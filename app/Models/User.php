<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'telefono',
        'dni',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    public function ventasComoTpv(): HasMany
    {
        return $this->hasMany(Pedido::class, 'cajero_id');
    }

    public function noticias(): HasMany
    {
        return $this->hasMany(Noticia::class, 'autor_id');
    }

    public function abonado(): HasMany
    {
        return $this->hasMany(Abonado::class);
    }

    public function abonadoActivo()
    {
        return $this->hasOne(Abonado::class)->where('activo', true);
    }

    public function esSocio(): bool
    {
        return $this->abonadoActivo()->exists();
    }
}

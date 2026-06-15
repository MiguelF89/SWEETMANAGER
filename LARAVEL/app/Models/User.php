<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Encomenda;
use App\Models\Cliente;
use App\Models\Produto;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    // ── Relacionamentos ──────────────────────────────────────────────────────

    public function encomendas()
    {
        return $this->hasMany(Encomenda::class);
    }

    /**
     * ADICIONADO: necessário para StoreVendaRequest e UpdateVendaRequest
     * verificarem se o cliente pertence ao usuário logado.
     */
    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    /**
     * ADICIONADO: necessário para StoreVendaRequest e UpdateVendaRequest
     * verificarem se o produto pertence ao usuário logado.
     */
    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }
}
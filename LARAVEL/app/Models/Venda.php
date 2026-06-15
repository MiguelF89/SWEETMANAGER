<?php

namespace App\Models;

use App\Models\Cliente;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'cliente_id', 'produto_id', 'quantidade', 'valor_total'];

    /**
     * Aplica automaticamente o filtro do usuário logado em todas as queries.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('user', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('vendas.user_id', Auth::id());
            }
        });

        static::creating(function (Venda $venda) {
            if (Auth::check() && empty($venda->user_id)) {
                $venda->user_id = Auth::id();
            }
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class)->withoutGlobalScopes();
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class)->withoutGlobalScopes();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
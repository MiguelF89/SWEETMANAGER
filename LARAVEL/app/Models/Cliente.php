<?php

namespace App\Models;

use App\Helpers\BrasilHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = ['user_id', 'nome', 'contato', 'cpf'];

    // ─── Mutators: normaliza antes de salvar ──────────────────────────────────

    public function setCpfAttribute(?string $value): void
    {
        $this->attributes['cpf'] = BrasilHelper::normalizeCpf($value);
    }

    public function setContatoAttribute(?string $value): void
    {
        $this->attributes['contato'] = BrasilHelper::normalizePhone($value);
    }

    // ─── Accessors: formata ao ler ────────────────────────────────────────────

    public function getCpfFormattedAttribute(): string
    {
        return BrasilHelper::formatCpf($this->attributes['cpf'] ?? '');
    }

    public function getContatoFormattedAttribute(): string
    {
        return BrasilHelper::formatPhone($this->attributes['contato'] ?? '');
    }

    // ─── Scopes e relacionamentos ─────────────────────────────────────────────

    protected static function booted(): void
    {
        static::addGlobalScope('user', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('user_id', Auth::id());
            }
        });

        static::creating(function (Cliente $cliente) {
            if (Auth::check() && empty($cliente->user_id)) {
                $cliente->user_id = Auth::id();
            }
        });
    }

    public function vendas()
    {
        return $this->hasMany(Venda::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
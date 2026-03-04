<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as AuthenticatableUser;

class CatalogoOpcion extends Model
{
    protected $table = 'catalogo_opciones';

    protected $fillable = [
        'categoria',
        'nombre',
        'valor',
        'valor_vinculado',
        'valor_freelance',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'valor' => 'decimal:2',
        'valor_vinculado' => 'decimal:2',
        'valor_freelance' => 'decimal:2',
        'orden' => 'integer',
    ];

    public function valorParaUsuario(?AuthenticatableUser $user = null): float
    {
        $tipoUsuario = $user?->tipo_usuario;

        $valor = match ($tipoUsuario) {
            'vinculado' => $this->valor_vinculado ?? $this->valor,
            'freelance' => $this->valor_freelance ?? $this->valor,
            default => $this->valor,
        };

        return (float) ($valor ?? 0);
    }

    public function getValorEfectivoAttribute(): float
    {
        /** @var AuthenticatableUser|null $user */
        $user = auth()->user();

        return $this->valorParaUsuario($user);
    }

    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'empresa_opcion', 'opcion_id', 'empresa_id')->withTimestamps();
    }
}

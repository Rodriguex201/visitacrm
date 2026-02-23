<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    protected $table = 'empresas';

    protected $fillable = [
        'nombre',
        'nit',
        'ciudad',
        'telefono',
        'email',
        'direccion',
        'notas',
        'sector_id',
        'user_id',
    ];

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class);
    }

    public function contactos(): HasMany
    {
        return $this->hasMany(Contacto::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function opciones(): BelongsToMany
    {
        return $this->belongsToMany(CatalogoOpcion::class, 'empresa_opcion', 'empresa_id', 'opcion_id')->withTimestamps();
    }

    public function empresaAcciones(): HasMany
    {
        return $this->hasMany(EmpresaAccion::class, 'empresa_id');
    }
}

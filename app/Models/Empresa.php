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
        'contacto_nombre',
        'nit',
        'ciudad',
        'telefono',
        'email',
        'direccion',
        'notas',
        'sector_id',
        'user_id',
        'responsable_user_id',
        'referida_at',
    ];

    protected $casts = [
        'referida_at' => 'datetime',
        'cotizacion_enviada' => 'boolean',
        'cotizacion_enviada_at' => 'datetime',
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
        return $this->belongsTo(User::class, 'user_id');
    }


    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_user_id');
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

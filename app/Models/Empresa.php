<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class);
    }
}

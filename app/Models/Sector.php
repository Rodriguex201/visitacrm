<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    protected $table = 'sectores';

    protected $fillable = [
        'nombre',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    public function empresas(): HasMany
    {
        return $this->hasMany(Empresa::class, 'sector_id');
    }
}

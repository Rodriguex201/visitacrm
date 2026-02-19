<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    protected $table = 'sectores';

    protected $fillable = [
        'nombre',
    ];

    public function empresas(): HasMany
    {
        return $this->hasMany(Empresa::class, 'sector_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmpresaComoLlegoOpcion extends Model
{
    protected $table = 'empresa_como_llego_opciones';

    protected $fillable = [
        'nombre',
        'activo',
        'orden',
        'requiere_texto',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'requiere_texto' => 'boolean',
    ];

    public function empresasComoLlego(): HasMany
    {
        return $this->hasMany(EmpresaComoLlego::class, 'opcion_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accion extends Model
{
    protected $table = 'acciones';

    protected $fillable = [
        'nombre',
        'descr',
        'icono',
        'color',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function empresaAcciones(): HasMany
    {
        return $this->hasMany(EmpresaAccion::class, 'accion_id');
    }
}

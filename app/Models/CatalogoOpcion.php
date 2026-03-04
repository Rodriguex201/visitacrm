<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CatalogoOpcion extends Model
{
    protected $table = 'catalogo_opciones';

    protected $fillable = [
        'categoria',
        'nombre',
        'valor',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'valor' => 'decimal:2',
        'orden' => 'integer',
    ];

    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'empresa_opcion', 'opcion_id', 'empresa_id')->withTimestamps();
    }
}

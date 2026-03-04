<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaCategoriaNota extends Model
{
    protected $table = 'empresa_categoria_notas';

    protected $fillable = [
        'empresa_id',
        'categoria',
        'nota',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}


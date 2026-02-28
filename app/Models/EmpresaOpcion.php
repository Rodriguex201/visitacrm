<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaOpcion extends Model
{
    protected $table = 'empresa_opcion';

    protected $fillable = [
        'empresa_id',
        'opcion_id',
        'nota',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function opcion(): BelongsTo
    {
        return $this->belongsTo(CatalogoOpcion::class, 'opcion_id');
    }
}

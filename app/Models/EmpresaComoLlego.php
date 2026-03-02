<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaComoLlego extends Model
{
    protected $table = 'empresa_como_llego';

    public $timestamps = false;

    protected $fillable = [
        'empresa_id',
        'opcion_id',
        'texto',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function opcion(): BelongsTo
    {
        return $this->belongsTo(EmpresaComoLlegoOpcion::class, 'opcion_id');
    }
}

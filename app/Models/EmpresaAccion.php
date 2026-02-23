<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaAccion extends Model
{
    protected $table = 'empresa_acciones';

    protected $fillable = [
        'empresa_id',
        'accion_id',
        'user_id',
        'nota',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function accion(): BelongsTo
    {
        return $this->belongsTo(Accion::class, 'accion_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

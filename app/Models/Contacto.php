<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contacto extends Model
{
    protected $table = 'contactos';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'cargo',
        'telefono',
        'email',
        'es_principal',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}

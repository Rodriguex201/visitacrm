<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visita extends Model
{
    protected $table = 'visitas';

    protected $fillable = [
        'empresa_id',
        'user_id',
        'fecha_hora',
        'estado',
        'resultado',
        'notas',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

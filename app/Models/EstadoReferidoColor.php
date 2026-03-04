<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoReferidoColor extends Model
{
    protected $table = 'estado_referido_colores';

    protected $fillable = [
        'estado',
        'bg_color',
        'text_color',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}


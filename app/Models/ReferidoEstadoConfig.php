<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferidoEstadoConfig extends Model
{
    protected $table = 'referido_estado_config';

    public $timestamps = false;

    protected $fillable = [
        'estado',
        'color_bg',
        'color_text',
    ];
}

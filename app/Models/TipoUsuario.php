<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoUsuario extends Model
{
    protected $table = 'tipos_usuario';

    protected $fillable = [
        'nombre',
        'color',
        'bg_color',
        'text_color',

    ];
}


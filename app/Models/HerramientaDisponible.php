<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HerramientaDisponible extends Model
{
    use HasFactory;

    protected $table = 'herramientas_disponibles';

    protected $fillable = [
        'nombre',
        'descripcion',
        'url',
        'icono',
        'color_fondo',
        'color_texto',
        'orden',
        'activo',
        'abrir_en_nueva_pestana',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'abrir_en_nueva_pestana' => 'boolean',
        'orden' => 'integer',
    ];
}

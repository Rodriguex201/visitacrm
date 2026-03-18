<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionSistema extends Model
{
    protected $table = 'configuraciones_sistema';

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
    ];

    public static function valor(string $clave, ?string $default = null): ?string
    {
        return static::query()
            ->where('clave', $clave)
            ->value('valor') ?? $default;
    }
}

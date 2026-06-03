<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class HerramientaOfrecer extends Model
{
    use HasFactory;

    protected $table = 'herramientas_ofrecer';

    protected $fillable = [
        'titulo',
        'descripcion',
        'imagen',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    protected $appends = [
        'imagen_url',
    ];

    public function getImagenUrlAttribute(): ?string
    {
        if (! $this->imagen) {
            return null;
        }

        $ruta = ltrim((string) $this->imagen, '/');

        if (str_starts_with($ruta, 'storage/')) {
            $ruta = substr($ruta, strlen('storage/'));
        }

        if (str_starts_with($ruta, 'public/')) {
            $ruta = substr($ruta, strlen('public/'));
        }

        if (File::exists(public_path('storage/' . $ruta))) {
            return Storage::url($ruta);
        }

        if (File::exists(public_path($ruta))) {
            return asset($ruta);
        }

        return Storage::url($ruta);
    }
}

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

        if (File::exists(public_path($ruta))) {
            return asset($ruta);
        }

        $rutaPublicaExterna = $this->rutaPublicaDominioExterno($ruta);

        if ($rutaPublicaExterna && File::exists($rutaPublicaExterna)) {
            return asset($ruta);
        }

        if (File::exists(public_path('storage/' . $ruta))) {
            return Storage::url($ruta);
        }

        return Storage::url($ruta);
    }

    private function rutaPublicaDominioExterno(string $rutaRelativa): ?string
    {
        $publicDomainPath = trim((string) config('app.public_domain_path', ''));

        if ($publicDomainPath === '') {
            return null;
        }

        return rtrim($publicDomainPath, DIRECTORY_SEPARATOR . '/\\')
            . DIRECTORY_SEPARATOR
            . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($rutaRelativa, '/\\'));
    }
}

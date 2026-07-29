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
        'video',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    protected $appends = [
        'imagen_url',
        'video_url',
        'has_demo_video',
        'demo_video',
    ];

    public function getImagenUrlAttribute(): ?string
    {
        return $this->resolvePublicAssetUrl($this->imagen);
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->videoUrl();
    }

    public function getHasDemoVideoAttribute(): bool
    {
        return $this->hasDemoVideo();
    }

    public function getDemoVideoAttribute(): ?array
    {
        return $this->demoVideo();
    }

    public function videoUrl(): ?string
    {
        return $this->resolvePublicAssetUrl($this->video);
    }

    public function hasDemoVideo(): bool
    {
        return $this->videoUrl() !== null;
    }

    public function demoVideo(): ?array
    {
        $videoUrl = $this->videoUrl();

        if (! $videoUrl) {
            return null;
        }

        return [
            'url' => $videoUrl,
            'mime_type' => 'video/mp4',
        ];
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

    private function resolvePublicAssetUrl(?string $rutaOriginal): ?string
    {
        if (! $rutaOriginal) {
            return null;
        }

        $ruta = ltrim((string) $rutaOriginal, '/');

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
}

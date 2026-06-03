<?php

use App\Http\Controllers\ConfiguracionController;
use App\Models\HerramientaOfrecer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

test('procesar imagen de ofrecer la guarda en storage y en public', function () {
    $controller = app(ConfiguracionController::class);
    $method = new ReflectionMethod($controller, 'procesarImagenOfrecer');
    $method->setAccessible(true);

    $ruta = $method->invoke(
        $controller,
        UploadedFile::fake()->image('ofrecer-demo.png', 1600, 900)
    );

    expect($ruta)->toStartWith('ofrecer/');
    expect(File::exists(storage_path('app/public/' . $ruta)))->toBeTrue();
    expect(File::exists(public_path($ruta)))->toBeTrue();

    File::delete(storage_path('app/public/' . $ruta));
    File::delete(public_path($ruta));
});

test('imagen_url usa la ruta publica directa cuando no hay storage link disponible', function () {
    $ruta = 'ofrecer/test-fallback.png';
    $rutaPublica = public_path($ruta);
    $rutaStorage = public_path('storage/' . $ruta);
    $directorio = dirname($rutaPublica);

    if (! File::exists($directorio)) {
        File::ensureDirectoryExists($directorio);
    }

    File::put($rutaPublica, 'fallback');

    if (File::exists($rutaStorage)) {
        File::delete($rutaStorage);
    }

    $item = new HerramientaOfrecer([
        'imagen' => $ruta,
    ]);

    expect($item->imagen_url)->toBe(asset($ruta));

    File::delete($rutaPublica);
});

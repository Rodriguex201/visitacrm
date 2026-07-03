<?php

use App\Http\Controllers\ConfiguracionController;
use App\Models\HerramientaOfrecer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

test('procesar imagen de ofrecer la guarda en storage y en public', function () {
    config()->set('app.public_domain_path', null);

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

test('procesar imagen de ofrecer tambien la copia al dominio publico externo configurado', function () {
    $externalRoot = base_path('tests/tmp/public-domain');
    config()->set('app.public_domain_path', $externalRoot);

    if (File::exists($externalRoot)) {
        File::deleteDirectory($externalRoot);
    }

    $controller = app(ConfiguracionController::class);
    $method = new ReflectionMethod($controller, 'procesarImagenOfrecer');
    $method->setAccessible(true);

    $ruta = $method->invoke(
        $controller,
        UploadedFile::fake()->image('ofrecer-externo.png', 1200, 800)
    );

    $rutaExterna = $externalRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $ruta);

    expect(File::exists($rutaExterna))->toBeTrue();
    expect(File::size($rutaExterna))->toBeGreaterThan(0);

    File::delete(storage_path('app/public/' . $ruta));
    File::delete(public_path($ruta));
    File::delete($rutaExterna);
    File::deleteDirectory($externalRoot);
});

test('imagen_url usa la ruta publica directa cuando no hay storage link disponible', function () {
    config()->set('app.public_domain_path', null);

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

test('imagen_url usa /ofrecer cuando existe la copia en el dominio publico externo', function () {
    $externalRoot = base_path('tests/tmp/public-domain');
    config()->set('app.public_domain_path', $externalRoot);

    $ruta = 'ofrecer/test-external-fallback.png';
    $rutaExterna = $externalRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $ruta);
    $directorioExterno = dirname($rutaExterna);
    $rutaLocalPublica = public_path($ruta);
    $rutaStorage = public_path('storage/' . $ruta);

    if (! File::exists($directorioExterno)) {
        File::ensureDirectoryExists($directorioExterno);
    }

    File::put($rutaExterna, 'external-fallback');

    if (File::exists($rutaLocalPublica)) {
        File::delete($rutaLocalPublica);
    }

    if (File::exists($rutaStorage)) {
        File::delete($rutaStorage);
    }

    $item = new HerramientaOfrecer([
        'imagen' => $ruta,
    ]);

    expect($item->imagen_url)->toBe(asset($ruta));

    File::delete($rutaExterna);
    File::deleteDirectory($externalRoot);
});

<?php

use App\Models\ConfiguracionSistema;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

function adminForLogoTests(): User
{
    return User::factory()->create([
        'codigo' => 'A-LOGO',
        'tipo_usuario' => 'administracion',
    ]);
}

test('admin can replace sidebar logo and old file is removed', function () {
    $admin = adminForLogoTests();

    $logoDir = public_path('imagenes/logo');

    if (! File::exists($logoDir)) {
        File::ensureDirectoryExists($logoDir);
    }

    $logoAnteriorRelativo = 'imagenes/logo/test-logo-anterior.png';
    $logoAnteriorAbsoluto = public_path($logoAnteriorRelativo);

    File::put($logoAnteriorAbsoluto, 'anterior');

    ConfiguracionSistema::query()->create([
        'clave' => 'logo_sidebar',
        'valor' => $logoAnteriorRelativo,
        'descripcion' => 'Ruta del logo principal mostrado en el sidebar',
    ]);

    $response = $this
        ->actingAs($admin)
        ->from(route('configuracion.index', ['tab' => 'logo']))
        ->post(route('configuracion.logo.update', ['tab' => 'logo']), [
            'logo' => UploadedFile::fake()->image('nuevo-logo.png', 320, 120),
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('configuracion.index', ['tab' => 'logo']));

    $rutaNueva = ConfiguracionSistema::valor('logo_sidebar');

    expect($rutaNueva)->not->toBeNull();
    expect($rutaNueva)->toStartWith('imagenes/logo/sidebar-logo-');

    $rutaNuevaAbsoluta = public_path($rutaNueva);

    expect(File::exists($rutaNuevaAbsoluta))->toBeTrue();
    expect(File::size($rutaNuevaAbsoluta))->toBeGreaterThan(0);
    expect(File::exists($logoAnteriorAbsoluto))->toBeFalse();

    File::delete($rutaNuevaAbsoluta);
});

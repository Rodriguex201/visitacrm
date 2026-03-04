<?php

use App\Http\Controllers\AccionesController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitaController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'admin'])->name('dashboard');

    Route::resource('empresas', EmpresaController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::post('/empresas/{empresa}/contactos', [ContactoController::class, 'store'])->name('empresas.contactos.store');

    Route::patch('/empresas/{empresa}/contactos/{contacto}', [ContactoController::class, 'update'])->name('empresas.contactos.update');
    Route::patch('/empresas/{empresa}/asignar-usuario', [EmpresaController::class, 'asignarUsuario'])->name('empresas.asignar-usuario');
    Route::patch('/empresas/{empresa}/opciones', [EmpresaController::class, 'guardarOpciones'])->name('empresas.opciones.update');
    Route::post('/empresas/{empresa}/categoria-notas', [EmpresaController::class, 'guardarCategoriaNota'])->name('empresas.categoria-notas.store');
    Route::patch('/empresas/{empresa}/cotizacion', [EmpresaController::class, 'actualizarCotizacion'])->name('empresas.cotizacion');
    Route::post('/empresas/{empresa}/referido-estado', [EmpresaController::class, 'updateReferidoEstado'])->name('empresas.referido.update');
    Route::patch('/empresa-opcion/{empresaOpcion}/nota', [EmpresaController::class, 'updateNota'])->name('empresa-opcion.nota');
    Route::post('/catalogo-opciones', [EmpresaController::class, 'storeCatalogoOpcion'])->name('catalogo-opciones.store');
    Route::get('/usuarios/buscar', [EmpresaController::class, 'searchUsuarios'])->name('usuarios.buscar');

    Route::post('/empresas/{empresa}/acciones', [EmpresaController::class, 'storeAccion'])->name('empresas.acciones.store');
    Route::patch('/empresas/{empresa}/acciones/{empresaAccion}', [EmpresaController::class, 'actualizarEmpresaAccion'])->name('empresas.acciones.update');
    Route::patch('/empresas/{empresa}/acciones/{empresaAccion}/nota', [EmpresaController::class, 'updateNotaEmpresaAccion'])->name('empresas.acciones.nota');
    Route::delete('/empresas/{empresa}/acciones/{empresaAccion}', [EmpresaController::class, 'eliminarEmpresaAccion'])->name('empresas.acciones.destroy');
    Route::get('/empresas/{empresa}/actividad/partial', [EmpresaController::class, 'actividadPartial'])->name('empresas.actividad.partial');
    Route::get('/empresas/{empresa}/visitas/partial', [EmpresaController::class, 'visitasPartial'])->name('empresas.visitas.partial');
    Route::post('/visitas', [VisitaController::class, 'store'])->name('visitas.store');
    Route::patch('/visitas/{visita}/resultado', [VisitaController::class, 'updateResultado'])->name('visitas.update-resultado');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/acciones/gestionar', [AccionesController::class, 'manage'])->name('acciones.manage');
        Route::post('/acciones', [AccionesController::class, 'store'])->name('acciones.store');
        Route::patch('/acciones/{accion}', [AccionesController::class, 'update'])->name('acciones.update');
        Route::delete('/acciones/{accion}', [AccionesController::class, 'destroy'])->name('acciones.destroy');

        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/{user}/referidos', [UsuarioController::class, 'referidos'])->name('usuarios.referidos');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{user}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::put('/usuarios/tipos/{tipoUsuario}', [UsuarioController::class, 'updateTipoUsuario'])->name('usuarios.tipos.update');


        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::get('/configuracion/sectores', [ConfiguracionController::class, 'sectores'])->name('configuracion.sectores.index');
        Route::post('/configuracion/sectores', [ConfiguracionController::class, 'storeSector'])->name('configuracion.sectores.store');
        Route::patch('/configuracion/sectores/{sector}', [ConfiguracionController::class, 'updateSector'])->name('configuracion.sectores.update');
        Route::delete('/configuracion/sectores/{sector}', [ConfiguracionController::class, 'destroySector'])->name('configuracion.sectores.destroy');

        Route::get('/configuracion/catalogo/{categoria}', [ConfiguracionController::class, 'catalogo'])->name('configuracion.catalogo.index');
        Route::post('/configuracion/catalogo', [ConfiguracionController::class, 'storeCatalogo'])->name('configuracion.catalogo.store');
        Route::patch('/configuracion/catalogo/{catalogoOpcion}', [ConfiguracionController::class, 'updateCatalogo'])->name('configuracion.catalogo.update');
        Route::delete('/configuracion/catalogo/{catalogoOpcion}', [ConfiguracionController::class, 'destroyCatalogo'])->name('configuracion.catalogo.destroy');
    });

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

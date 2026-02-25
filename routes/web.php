<?php

use App\Http\Controllers\AccionesController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitaController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('empresas', EmpresaController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::post('/empresas/{empresa}/contactos', [ContactoController::class, 'store'])->name('empresas.contactos.store');

    Route::patch('/empresas/{empresa}/contactos/{contacto}', [ContactoController::class, 'update'])->name('empresas.contactos.update');
    Route::patch('/empresas/{empresa}/asignar-usuario', [EmpresaController::class, 'asignarUsuario'])->name('empresas.asignar-usuario');
    Route::patch('/empresas/{empresa}/opciones', [EmpresaController::class, 'guardarOpciones'])->name('empresas.opciones.update');
    Route::patch('/empresas/{empresa}/cotizacion', [EmpresaController::class, 'actualizarCotizacion'])->name('empresas.cotizacion');
    Route::post('/catalogo-opciones', [EmpresaController::class, 'storeCatalogoOpcion'])->name('catalogo-opciones.store');
    Route::get('/usuarios/buscar', [EmpresaController::class, 'searchUsuarios'])->name('usuarios.buscar');

    Route::post('/empresas/{empresa}/acciones', [EmpresaController::class, 'storeAccion'])->name('empresas.acciones.store');
    Route::patch('/empresas/{empresa}/acciones/{empresaAccion}', [EmpresaController::class, 'actualizarEmpresaAccion'])->name('empresas.acciones.update');
    Route::delete('/empresas/{empresa}/acciones/{empresaAccion}', [EmpresaController::class, 'eliminarEmpresaAccion'])->name('empresas.acciones.destroy');
    Route::get('/empresas/{empresa}/actividad/partial', [EmpresaController::class, 'actividadPartial'])->name('empresas.actividad.partial');
    Route::get('/empresas/{empresa}/visitas/partial', [EmpresaController::class, 'visitasPartial'])->name('empresas.visitas.partial');
    Route::get('/acciones', [AccionesController::class, 'index'])->name('acciones.index');
    Route::patch('/acciones/{accion}', [AccionesController::class, 'update'])->name('acciones.update');

    Route::post('/visitas', [VisitaController::class, 'store'])->name('visitas.store');
    Route::patch('/visitas/{visita}/resultado', [VisitaController::class, 'updateResultado'])->name('visitas.update-resultado');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/{user}/referidos', [UsuarioController::class, 'referidos'])->name('usuarios.referidos');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{user}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::put('/usuarios/tipos/{tipoUsuario}', [UsuarioController::class, 'updateTipoUsuario'])->name('usuarios.tipos.update');
    });

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

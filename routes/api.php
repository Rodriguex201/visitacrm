<?php

use App\Http\Controllers\CiudadController;
use App\Http\Controllers\EmpresaController;
use Illuminate\Support\Facades\Route;

Route::get('/ciudades', [CiudadController::class, 'search'])->name('api.ciudades.search');

Route::get('/empresas', [EmpresaController::class, 'search'])->name('api.empresas.search');

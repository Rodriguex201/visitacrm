<?php

use App\Http\Controllers\CiudadController;
use Illuminate\Support\Facades\Route;

Route::get('/ciudades', [CiudadController::class, 'search'])->name('api.ciudades.search');

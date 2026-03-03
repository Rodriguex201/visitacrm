<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\View\View;

class ConfiguracionController extends Controller
{
    public function index(): View
    {
        $sectores = Sector::query()
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'activo', 'orden']);

        return view('configuracion.index', compact('sectores'));
    }
}

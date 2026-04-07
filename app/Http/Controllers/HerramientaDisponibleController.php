<?php

namespace App\Http\Controllers;

use App\Models\HerramientaDisponible;
use App\Models\HerramientaOfrecer;
use Illuminate\View\View;

class HerramientaDisponibleController extends Controller
{
    public function index(): View
    {
        return view('herramientas-disponibles.index', [
            'herramientas' => HerramientaDisponible::query()
                ->where('activo', 1)
                ->orderBy('orden')
                ->orderBy('id')
                ->get(),
            'ofrecerItems' => HerramientaOfrecer::query()
                ->where('activo', 1)
                ->orderBy('orden')
                ->orderBy('id')
                ->get(),
        ]);
    }
}

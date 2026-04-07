<?php

namespace App\Http\Controllers;

use App\Models\HerramientaDisponible;
use App\Models\HerramientaOfrecer;
use Illuminate\View\View;

class HerramientaDisponibleController extends Controller
{
    public function redes(): View
    {
        return view('herramientas-disponibles.redes', [
            'herramientas' => HerramientaDisponible::query()
                ->where('activo', 1)
                ->orderBy('orden')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function ofrecer(): View
    {
        return view('herramientas-disponibles.ofrecer', [
            'ofrecerItems' => HerramientaOfrecer::query()
                ->where('activo', 1)
                ->orderBy('orden')
                ->orderBy('id')
                ->get(),
        ]);
    }
}

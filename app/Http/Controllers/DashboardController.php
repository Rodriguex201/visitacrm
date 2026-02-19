<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Visita;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $now = Carbon::now();

        $countEmpresas = Empresa::query()->count();

        $countHoy = Visita::query()
            ->whereDate('fecha_hora', $now->toDateString())
            ->count();

        $countSemana = Visita::query()
            ->whereBetween('fecha_hora', [
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek(),
            ])
            ->count();

        $proximasVisitas = Visita::query()
            ->with('empresa')
            ->where('fecha_hora', '>=', $now)
            ->orderBy('fecha_hora')
            ->limit(5)
            ->get();

        $visitasRecientes = Visita::query()
            ->with('empresa')
            ->where('fecha_hora', '<=', $now)
            ->orderByDesc('fecha_hora')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'countHoy',
            'countSemana',
            'countEmpresas',
            'proximasVisitas',
            'visitasRecientes',
        ));
    }
}

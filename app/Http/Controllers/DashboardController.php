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

        $latestVisitasPorEmpresa = Visita::query()
            ->selectRaw('empresa_id, MAX(COALESCE(resultado_at, fecha_hora)) as latest_result_at')
            ->whereNotNull('resultado')
            ->groupBy('empresa_id');

        $enSeguimientoCount = Visita::query()
            ->joinSub($latestVisitasPorEmpresa, 'latest_visitas', function ($join) {
                $join->on('visitas.empresa_id', '=', 'latest_visitas.empresa_id')
                    ->whereRaw('COALESCE(visitas.resultado_at, visitas.fecha_hora) = latest_visitas.latest_result_at');
            })
            ->where('visitas.resultado', 'en_seguimiento')
            ->distinct()
            ->count('visitas.empresa_id');

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
            'enSeguimientoCount',
            'proximasVisitas',
            'visitasRecientes',
        ));
    }
}

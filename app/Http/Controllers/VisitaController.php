<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VisitaController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'empresa_id' => ['required', 'exists:empresas,id'],
            'fecha_hora' => ['required', 'date'],
            'estado' => ['required', 'in:programada,realizada,cancelada'],
            'resultado' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string'],
        ]);

        $data['user_id'] = auth()->id();

        Visita::query()->create($data);

        return back()->with('status', 'Visita guardada correctamente.');
    }

    public function updateResultado(Request $request, Visita $visita): JsonResponse
    {
        $user = $request->user();

        if ((int) $visita->user_id !== (int) $user->id && $user->tipo_usuario !== 'administracion') {
            abort(403, 'No tienes permisos para actualizar esta visita.');
        }

        if ($visita->fecha_hora?->isFuture()) {
            return response()->json([
                'message' => 'Solo puedes actualizar visitas que ya ocurrieron.',
            ], 422);
        }

        $validated = $request->validate([
            'resultado' => ['required', 'in:venta_realizada,en_seguimiento,sin_interes,no_disponible'],
            'nivel_interes' => ['nullable', 'in:alto,medio,bajo,sin_interes'],
        ]);

        if (
            in_array($validated['resultado'], ['venta_realizada', 'en_seguimiento'], true)
            && (($validated['nivel_interes'] ?? null) === 'sin_interes')
        ) {
            return response()->json([
                'message' => 'El nivel de interés no puede ser "sin interés" para este resultado.',
                'errors' => [
                    'nivel_interes' => ['Selecciona Alto, Medio o Bajo para este resultado.'],
                ],
            ], 422);
        }

        $nivelInteres = $validated['nivel_interes'] ?? null;

        if ($validated['resultado'] === 'sin_interes') {
            $nivelInteres = 'sin_interes';
        }

        if ($validated['resultado'] === 'no_disponible') {
            $nivelInteres = null;
        }

        $visita->resultado = $validated['resultado'];
        $visita->nivel_interes = $nivelInteres;
        $visita->resultado_at = now();
        $visita->save();

        return response()->json([
            'message' => 'Resultado actualizado correctamente.',
            'visita' => [
                'id' => $visita->id,
                'resultado' => $visita->resultado,
                'nivel_interes' => $visita->nivel_interes,
                'resultado_label' => $this->resultadoLabel($visita->resultado),
                'nivel_interes_label' => $this->nivelInteresLabel($visita->nivel_interes),
                'resultado_badge_class' => $this->resultadoBadgeClass($visita->resultado),
            ],
        ]);
    }

    private function resultadoLabel(?string $resultado): ?string
    {
        return match ($resultado) {
            'venta_realizada' => 'Venta realizada',
            'en_seguimiento' => 'En seguimiento',
            'sin_interes' => 'Sin interés',
            'no_disponible' => 'No disponible',
            default => null,
        };
    }

    private function nivelInteresLabel(?string $nivelInteres): ?string
    {
        return match ($nivelInteres) {
            'alto' => 'Alto',
            'medio' => 'Medio',
            'bajo' => 'Bajo',
            'sin_interes' => 'Sin interés',
            default => null,
        };
    }

    private function resultadoBadgeClass(?string $resultado): string
    {
        return match ($resultado) {
            'venta_realizada' => 'bg-emerald-100 text-emerald-700',
            'en_seguimiento' => 'bg-amber-100 text-amber-700',
            'sin_interes' => 'bg-rose-100 text-rose-700',
            'no_disponible' => 'bg-slate-200 text-slate-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }
}

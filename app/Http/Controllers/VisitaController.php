<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Visita;
use App\Support\VisitaCatalogos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VisitaController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'empresa_id' => ['required', 'exists:empresas,id'],
            'fecha_hora' => ['required', 'date'],
            'estado' => ['required', VisitaCatalogos::estadoRule()],
            'notas' => ['nullable', 'string'],
        ]);

        $empresa = Empresa::query()->findOrFail((int) $data['empresa_id']);
        $this->authorize('update', $empresa);

        $data['user_id'] = auth()->id();

        $visita = Visita::query()->create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Visita guardada correctamente.',
                'visita' => [
                    'id' => $visita->id,
                    'empresa_id' => $visita->empresa_id,
                ],
            ]);
        }

        return back()->with('status', 'Visita guardada correctamente.');
    }

    public function updateResultado(Request $request, Visita $visita): JsonResponse
    {
        $visita->loadMissing('empresa');
        $this->authorize('update', $visita->empresa);

        if ($visita->fecha_hora?->isFuture()) {
            return response()->json([
                'message' => 'Solo puedes actualizar visitas que ya ocurrieron.',
            ], 422);
        }

        $validated = $request->validate([
            'resultado' => ['required', VisitaCatalogos::resultadoRule()],
            'nivel_interes' => ['nullable', VisitaCatalogos::nivelInteresRule()],
        ]);

        if (
            VisitaCatalogos::resultadoRequiereNivelDistintoDeSinInteres($validated['resultado'])
            && (($validated['nivel_interes'] ?? null) === VisitaCatalogos::nivelInteresSinInteres())
        ) {
            return response()->json([
                'message' => 'El nivel de interés no puede ser "sin interés" para este resultado.',
                'errors' => [
                    'nivel_interes' => ['Selecciona Alto, Medio o Bajo para este resultado.'],
                ],
            ], 422);
        }

        $nivelInteres = $validated['nivel_interes'] ?? null;

        if (VisitaCatalogos::resultadoImponeNivelSinInteres($validated['resultado'])) {
            $nivelInteres = VisitaCatalogos::nivelInteresSinInteres();
        }

        if (VisitaCatalogos::resultadoLimpiaNivelInteres($validated['resultado'])) {
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
        return VisitaCatalogos::resultadoLabel($resultado);
    }

    private function nivelInteresLabel(?string $nivelInteres): ?string
    {
        return VisitaCatalogos::nivelInteresLabel($nivelInteres);
    }

    private function resultadoBadgeClass(?string $resultado): string
    {
        return VisitaCatalogos::resultadoBadgeClass($resultado);
    }
}


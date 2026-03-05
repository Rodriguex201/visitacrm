<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\User;
use App\Models\Visita;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user?->tipo_usuario === 'administracion';

        $empresas = $this->visibleEmpresasQuery($request)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $responsables = collect();

        if ($isAdmin) {
            $responsables = User::query()
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return view('agenda.index', [
            'empresas' => $empresas,
            'responsables' => $responsables,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function events(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
            'empresa' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', Rule::in(['programada', 'realizada', 'cancelada', 'en_seguimiento'])],
            'responsable_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $visitas = $this->agendaVisitasQuery($request)
            ->with('empresa')
            ->whereBetween('fecha_hora', [$validated['start'], $validated['end']])
            ->when(! empty($validated['empresa']), function (Builder $query) use ($validated) {
                $query->whereHas('empresa', function (Builder $empresaQuery) use ($validated) {
                    $empresaQuery->where('nombre', 'like', '%'.$validated['empresa'].'%');
                });
            })
            ->when(! empty($validated['estado']), fn (Builder $query) => $query->where('estado', $validated['estado']))
            ->when(! empty($validated['responsable_id']), fn (Builder $query) => $query->where('user_id', $validated['responsable_id']))
            ->get();

        $payload = $visitas->map(function (Visita $visita) {
            $start = $visita->fecha_hora;
            $durationMin = (int) ($visita->duracion_min ?? 60);
            $end = $start?->copy()->addMinutes($durationMin);

            return [
                'id' => $visita->id,
                'title' => $visita->empresa?->nombre ?? 'Empresa sin nombre',
                'start' => $start?->toIso8601String(),
                'end' => $end?->toIso8601String(),
                'extendedProps' => [
                    'empresa_id' => $visita->empresa_id,
                    'estado' => $visita->estado,
                    'notas' => $visita->notas,
                    'resultado' => $visita->resultado,
                    'nivel_interes' => $visita->nivel_interes,
                    'user_id' => $visita->user_id,
                    'duracion_min' => $durationMin,
                ],
            ];
        })->values();

        return response()->json($payload);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedVisitaData($request);

        $empresa = $this->visibleEmpresasQuery($request)
            ->whereKey((int) $data['empresa_id'])
            ->firstOrFail();

        $visita = Visita::query()->create([
            ...$data,
            'empresa_id' => $empresa->id,
            'user_id' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Visita creada correctamente.',
            'id' => $visita->id,
        ], 201);
    }

    public function update(Request $request, Visita $visita): JsonResponse
    {
        $this->authorizeVisita($request, $visita);

        $data = $this->validatedVisitaData($request);

        $this->visibleEmpresasQuery($request)
            ->whereKey((int) $data['empresa_id'])
            ->firstOrFail();

        $visita->update($data);

        return response()->json([
            'message' => 'Visita actualizada correctamente.',
        ]);
    }

    public function move(Request $request, Visita $visita): JsonResponse
    {
        $this->authorizeVisita($request, $visita);

        $data = $request->validate([
            'fecha_hora' => ['required', 'date'],
            'duracion_min' => ['nullable', 'integer', 'min:1', 'max:1440'],
        ]);

        $visita->fecha_hora = $data['fecha_hora'];

        if (array_key_exists('duracion_min', $data)) {
            $visita->duracion_min = $data['duracion_min'] ?: 60;
        }

        $visita->save();

        return response()->json([
            'message' => 'Visita reprogramada correctamente.',
        ]);
    }

    public function destroy(Request $request, Visita $visita): JsonResponse
    {
        $this->authorizeVisita($request, $visita);

        $visita->delete();

        return response()->json([
            'message' => 'Visita eliminada correctamente.',
        ]);
    }

    private function validatedVisitaData(Request $request): array
    {
        return $request->validate([
            'empresa_id' => ['required', 'integer', 'exists:empresas,id'],
            'fecha_hora' => ['required', 'date'],
            'estado' => ['required', Rule::in(['programada', 'realizada', 'cancelada', 'en_seguimiento'])],
            'notas' => ['nullable', 'string'],
            'duracion_min' => ['nullable', 'integer', 'min:1', 'max:1440'],
        ]);
    }

    private function authorizeVisita(Request $request, Visita $visita): void
    {
        $exists = $this->agendaVisitasQuery($request)
            ->whereKey($visita->id)
            ->exists();

        abort_unless($exists, 404);
    }

    private function agendaVisitasQuery(Request $request): Builder
    {
        $user = $request->user();
        $isAdmin = $user?->tipo_usuario === 'administracion';

        return Visita::query()
            ->when(! $isAdmin, function (Builder $query) use ($user) {
                $userId = (int) $user?->id;

                $query->whereHas('empresa', function (Builder $empresaQuery) use ($userId) {
                    $empresaQuery->where('user_id', $userId)
                        ->orWhere('responsable_user_id', $userId);
                });
            });
    }

    private function visibleEmpresasQuery(Request $request): Builder
    {
        $user = $request->user();
        $isAdmin = $user?->tipo_usuario === 'administracion';

        return Empresa::query()
            ->when(! $isAdmin, function (Builder $query) use ($user) {
                $userId = (int) $user?->id;

                $query->where(function (Builder $empresaQuery) use ($userId) {
                    $empresaQuery->where('user_id', $userId)
                        ->orWhere('responsable_user_id', $userId);
                });
            });
    }
}

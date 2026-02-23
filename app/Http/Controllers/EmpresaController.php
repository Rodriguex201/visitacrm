<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Carbon\Carbon;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmpresaController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'q' => ['nullable', 'string'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date'],
        ]);

        $q = trim((string) $request->query('q', ''));
        $desdeInput = $request->query('desde');
        $hastaInput = $request->query('hasta');

        $desde = $desdeInput ? Carbon::parse((string) $desdeInput)->startOfDay() : null;
        $hasta = $hastaInput ? Carbon::parse((string) $hastaInput)->endOfDay() : null;
        $usaRangoPersonalizado = $desde !== null || $hasta !== null;

        $empresasQuery = Empresa::query()
            ->with(['sector', 'user'])
            ->latest('id');

        if ($q !== '') {
            $empresasQuery->where(function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%")
                    ->orWhere('ciudad', 'like', "%{$q}%");
            });
        }

        if (! $usaRangoPersonalizado) {
            $empresasQuery->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ]);
        } else {
            if ($desde !== null) {
                $empresasQuery->where('created_at', '>=', $desde);
            }

            if ($hasta !== null) {
                $empresasQuery->where('created_at', '<=', $hasta);
            }
        }

        $empresas = $empresasQuery
            ->paginate(10)
            ->appends($request->query());

        $sectores = Sector::query()
            ->orderBy('nombre')
            ->get();

        return view('empresas.index', compact(
            'empresas',
            'sectores',
            'q',
            'desdeInput',
            'hastaInput',
            'usaRangoPersonalizado',
            'desde',
            'hasta',
        ));
    }

    public function show(Request $request, Empresa $empresa): View
    {
        $range = $request->query('range', 'todo');

        if (!in_array($range, ['hoy', '7d', 'todo'], true)) {
            $range = 'todo';
        }

        $empresa->load(['sector', 'user', 'contactos' => fn ($query) => $query->orderByDesc('es_principal')->latest()]);

        $visitasQuery = $empresa->visitas()->latest('fecha_hora');

        if ($range === 'hoy') {
            $visitasQuery->whereBetween('fecha_hora', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay(),
            ]);
        }

        if ($range === '7d') {
            $visitasQuery->where('fecha_hora', '>=', Carbon::now()->subDays(6)->startOfDay());
        }

        $visitas = $visitasQuery->get();

        $contactos = $empresa->contactos;

        return view('empresas.show', compact('empresa', 'visitas', 'range', 'contactos'));
    }

    public function asignarUsuario(Request $request, Empresa $empresa): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $empresa->user_id = $validated['user_id'] ?? null;
        $empresa->save();
        $empresa->load('user');

        return response()->json([
            'ok' => true,
            'empresa' => [
                'id' => $empresa->id,
                'user_id' => $empresa->user_id,
                'user' => $empresa->user ? [
                    'id' => $empresa->user->id,
                    'codigo' => $empresa->user->codigo,
                    'name' => $empresa->user->name ?? $empresa->user->nombre,
                    'nombre' => $empresa->user->nombre ?? $empresa->user->name,
                    'telefono' => $empresa->user->telefono,
                ] : null,
            ],
        ]);
    }

    public function searchUsuarios(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('query', ''));

        $usuarios = User::query()
            ->select(['id', 'codigo', 'name', 'telefono'])
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($inner) use ($query) {
                    $inner->where('codigo', 'like', "%{$query}%")
                        ->orWhere('name', 'like', "%{$query}%")
                        ->orWhere('telefono', 'like', "%{$query}%");
                });
            })
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json($usuarios);
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('query', ''));

        if ($query === '') {
            return response()->json([]);
        }

        $empresas = Empresa::query()
            ->select(['id', 'nombre', 'ciudad'])
            ->where(function ($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                    ->orWhere('ciudad', 'like', "%{$query}%");
            })
            ->orderBy('nombre')
            ->limit(15)
            ->get();

        return response()->json($empresas);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateEmpresa($request);

        Empresa::query()->create($data);

        return redirect()
            ->route('empresas.index')
            ->with('status', 'Empresa creada correctamente.');
    }

    public function update(Request $request, Empresa $empresa): RedirectResponse
    {
        $data = $this->validateEmpresa($request);

        $empresa->update($data);

        return redirect()
            ->route('empresas.index')
            ->with('status', 'Empresa actualizada correctamente.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateEmpresa(Request $request): array
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'nit' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string'],
            'notas' => ['nullable', 'string', 'max:5000'],
            'sector_id' => ['nullable', 'exists:sectores,id'],
            'modal_mode' => ['nullable', Rule::in(['create', 'edit'])],
            'empresa_id' => ['nullable', 'integer'],
        ]);

        unset($validated['modal_mode'], $validated['empresa_id']);

        return $validated;
    }
}

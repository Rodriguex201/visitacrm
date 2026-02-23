<?php

namespace App\Http\Controllers;

use App\Models\CatalogoOpcion;
use App\Models\Empresa;
use Carbon\Carbon;
use App\Models\Sector;
use App\Models\Accion;
use App\Models\EmpresaAccion;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
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
        $actRange = (string) $request->query('act_range', 'todo');
        $visRange = (string) $request->query('vis_range', 'todo');

        if ($actRange === '7d') {
            $actRange = '7';
        }

        if ($visRange === '7d') {
            $visRange = '7';
        }

        if (!in_array($actRange, ['hoy', '7', 'todo'], true)) {
            $actRange = 'todo';
        }

        if (!in_array($visRange, ['hoy', '7', 'todo'], true)) {
            $visRange = 'todo';
        }

        $empresa->load(['sector', 'user', 'contactos' => fn ($query) => $query->orderByDesc('es_principal')->latest()]);

        $visitasQuery = $empresa->visitas()->latest('fecha_hora');

        if ($visRange === 'hoy') {
            $visitasQuery->whereBetween('fecha_hora', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay(),
            ]);
        }

        if ($visRange === '7') {
            $visitasQuery->where('fecha_hora', '>=', Carbon::now()->subDays(6)->startOfDay());
        }

        $visitas = $visitasQuery->get();

        $acciones = Accion::query()
            ->where('activo', 1)
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        $accionesActividadQuery = $empresa->empresaAcciones()
            ->with('accion')
            ->latest('created_at');


        if ($actRange === 'hoy') {

            $accionesActividadQuery->whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay(),
            ]);
        }


        if ($actRange === '7') {

            $accionesActividadQuery->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay());
        }

        $accionesActividad = $accionesActividadQuery->get();

        $contactos = $empresa->contactos;

        $categoriasOpciones = [
            'Estado Actual',
            'Aplicativos',
            'Procesos Electrónicos',
            'Equipos',
        ];

        $catalogoOpciones = CatalogoOpcion::query()
            ->whereIn('categoria', $categoriasOpciones)
            ->where('activo', 1)
            ->orderBy('categoria')
            ->orderBy('nombre')
            ->get(['id', 'categoria', 'nombre'])
            ->groupBy('categoria');

        $opcionesSeleccionadas = $empresa->opciones()->pluck('catalogo_opciones.id')->map(fn ($id) => (int) $id)->values();


        return view('empresas.show', compact('empresa', 'visitas', 'actRange', 'visRange', 'contactos', 'categoriasOpciones', 'catalogoOpciones', 'opcionesSeleccionadas', 'acciones', 'accionesActividad'));

    }


    public function guardarOpciones(Request $request, Empresa $empresa): JsonResponse
    {
        $validated = $request->validate([
            'opciones' => ['nullable', 'array'],
            'opciones.*' => ['integer', 'exists:catalogo_opciones,id'],
        ]);

        $opciones = collect($validated['opciones'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();

        if ($opciones->isNotEmpty()) {
            $opcionesActivas = CatalogoOpcion::query()
                ->whereIn('id', $opciones)
                ->where('activo', 1)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values();

            if ($opcionesActivas->count() !== $opciones->count()) {
                return response()->json([
                    'message' => 'Una o más opciones no existen o están inactivas.',
                ], 422);
            }
        }

        $empresa->opciones()->sync($opciones->all());

        return response()->json([
            'ok' => true,
            'message' => 'Opciones guardadas correctamente.',
            'opciones' => $opciones,
        ]);
    }

    public function storeCatalogoOpcion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'categoria' => ['required', 'string', Rule::in(['Estado Actual', 'Aplicativos', 'Procesos Electrónicos', 'Equipos'])],
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $nombre = trim($validated['nombre']);
        if ($nombre === '') {
            return response()->json([
                'message' => 'El nombre es obligatorio.',
                'errors' => ['nombre' => ['El nombre es obligatorio.']],
            ], 422);
        }

        $exists = CatalogoOpcion::query()
            ->where('categoria', $validated['categoria'])
            ->whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombre)])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe una opción con ese nombre en la categoría seleccionada.',
                'errors' => ['nombre' => ['Ya existe una opción con ese nombre en la categoría seleccionada.']],
            ], 422);
        }

        $opcion = DB::transaction(function () use ($validated, $nombre) {
            return CatalogoOpcion::query()->create([
                'categoria' => $validated['categoria'],
                'nombre' => $nombre,
                'activo' => 1,
            ]);
        });

        return response()->json([
            'id' => $opcion->id,
            'categoria' => $opcion->categoria,
            'nombre' => $opcion->nombre,
        ], 201);
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


    public function storeAccion(Request $request, Empresa $empresa): JsonResponse
    {
        $validated = $request->validate([
            'accion_id' => ['required', 'integer', 'exists:acciones,id'],
            'nota' => ['nullable', 'string'],
        ]);

        $accion = Accion::query()
            ->where('id', $validated['accion_id'])
            ->where('activo', 1)
            ->first();

        if (! $accion) {
            return response()->json([
                'message' => 'La acción seleccionada no está activa.',
            ], 422);
        }

        $empresaAccion = EmpresaAccion::query()->create([
            'empresa_id' => $empresa->id,
            'accion_id' => $accion->id,
            'user_id' => (int) auth()->id(),
            'nota' => $validated['nota'] ?? null,
        ]);

        return response()->json([
            'ok' => true,
            'empresa_accion' => [
                'id' => $empresaAccion->id,
                'empresa_id' => $empresaAccion->empresa_id,
                'accion_id' => $empresaAccion->accion_id,
                'user_id' => $empresaAccion->user_id,
                'nota' => $empresaAccion->nota,
                'created_at' => $empresaAccion->created_at?->toIso8601String(),
            ],
            'accion' => [
                'id' => $accion->id,
                'nombre' => $accion->nombre,
                'icono' => $accion->icono,
                'color' => $accion->color,
            ],
        ], 201);
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

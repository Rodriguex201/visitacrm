<?php

namespace App\Http\Controllers;

use App\Models\CatalogoOpcion;
use App\Models\Empresa;
use Carbon\Carbon;
use App\Models\Sector;
use App\Models\Accion;
use App\Models\EmpresaAccion;
use App\Models\Visita;
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
        $this->authorize('viewAny', Empresa::class);

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

        $esAdministracion = ($request->user()?->tipo_usuario ?? null) === 'administracion';

        $empresasQuery = Empresa::query()
            ->with(['sector', 'creador', 'responsable'])
            ->latest('id');

        if (! $esAdministracion) {

            $userId = (int) $request->user()->id;

            $empresasQuery->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('responsable_user_id', $userId);
            });

        }

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
        $usuario = $request->user();
        $esAdministracion = ($usuario?->tipo_usuario ?? null) === 'administracion';

        if (! $esAdministracion) {

            $userId = (int) $usuario?->id;
            $esCreador = (int) $empresa->user_id === $userId;
            $esResponsable = (int) $empresa->responsable_user_id === $userId;

            if (! $esCreador && ! $esResponsable) {

                abort(404);
            }

            $empresa->load('sector');

            return view('empresas.show_basic', compact('empresa'));
        }

        $this->authorize('view', $empresa);

        $actRange = (string) $request->query('act_range', '7');
        $visRange = (string) $request->query('vis_range', '7');

        $actRange = $this->normalizarRango($actRange);
        $visRange = $this->normalizarRango($visRange);


        $actFrom = $this->rangoFecha($actRange);
        $visFrom = $this->rangoFecha($visRange);


        $empresa->load(['sector', 'creador', 'responsable', 'contactos' => fn ($query) => $query->orderByDesc('es_principal')->latest()]);

        $visitas = Visita::query()
            ->where('empresa_id', $empresa->id)
            ->when($visFrom, fn ($query) => $query->where('fecha_hora', '>=', $visFrom))
            ->orderByDesc('fecha_hora')
            ->get();

        $accionesCatalogo = Accion::query()
            ->where('activo', 1)
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        $acciones = EmpresaAccion::query()
            ->with('accion')
            ->where('empresa_id', $empresa->id)
            ->when($actFrom, fn ($query) => $query->where('created_at', '>=', $actFrom))
            ->orderByDesc('created_at')
            ->get();

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


        return view('empresas.show', compact('empresa', 'visitas', 'actRange', 'visRange', 'contactos', 'categoriasOpciones', 'catalogoOpciones', 'opcionesSeleccionadas', 'acciones', 'accionesCatalogo'));
    }

    public function actividadPartial(Request $request, Empresa $empresa): View
    {
        $this->authorize('view', $empresa);

        $actRange = $this->normalizarRango((string) $request->query('act_range', '7'));
        $actFrom = $this->rangoFecha($actRange);

        $acciones = EmpresaAccion::query()
            ->with('accion')
            ->where('empresa_id', $empresa->id)
            ->when($actFrom, fn ($query) => $query->where('created_at', '>=', $actFrom))
            ->orderByDesc('created_at')
            ->get();

        return view('empresas.partials.actividad_list', compact('acciones', 'empresa'));
    }

    public function visitasPartial(Request $request, Empresa $empresa): View
    {
        $this->authorize('view', $empresa);

        $visRange = $this->normalizarRango((string) $request->query('vis_range', '7'));
        $visFrom = $this->rangoFecha($visRange);

        $visitas = Visita::query()
            ->where('empresa_id', $empresa->id)
            ->when($visFrom, fn ($query) => $query->where('fecha_hora', '>=', $visFrom))
            ->orderByDesc('fecha_hora')
            ->get();

        return view('empresas.partials.visitas_list', compact('visitas', 'empresa'));
    }

    private function normalizarRango(string $rango): string
    {
        if ($rango === '7d') {
            $rango = '7';
        }

        if (!in_array($rango, ['hoy', '7', 'todo'], true)) {
            return '7';
        }

        return $rango;
    }

    private function rangoFecha(string $rango): ?Carbon
    {
        return match ($rango) {
            'hoy' => Carbon::now()->startOfDay(),
            '7' => Carbon::now()->subDays(7),
            default => null,
        };
    }


    public function guardarOpciones(Request $request, Empresa $empresa): JsonResponse
    {
        $this->authorize('update', $empresa);

        $validated = $request->validate([
            'opciones' => ['nullable', 'array'],
            'opciones.*' => ['integer', 'exists:catalogo_opciones,id'],
            'cotizacion_enviada' => ['nullable', 'boolean'],
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

        if (array_key_exists('cotizacion_enviada', $validated) && ($request->user()?->tipo_usuario ?? null) === 'administracion') {
            $cotizacionEnviadaNueva = (bool) $validated['cotizacion_enviada'];
            $cotizacionEnviadaActual = (bool) $empresa->cotizacion_enviada;

            if ($cotizacionEnviadaNueva !== $cotizacionEnviadaActual) {
                $empresa->cotizacion_enviada_at = $cotizacionEnviadaNueva ? now() : null;
            }

            $empresa->cotizacion_enviada = $cotizacionEnviadaNueva;
            $empresa->save();
        }

        return response()->json([
            'ok' => true,
            'message' => 'Opciones guardadas correctamente.',
            'opciones' => $opciones,
            'empresa' => [
                'cotizacion_enviada' => (bool) $empresa->cotizacion_enviada,
                'cotizacion_enviada_at' => optional($empresa->cotizacion_enviada_at)->toIso8601String(),
            ],
        ]);
    }

    public function actualizarCotizacion(Request $request, Empresa $empresa): JsonResponse
    {
        if (($request->user()?->tipo_usuario ?? null) !== 'administracion') {
            abort(403);
        }

        $validated = $request->validate([
            'cotizacion_enviada' => ['required', 'boolean'],
        ]);

        $cotizacionEnviada = (bool) $validated['cotizacion_enviada'];

        $empresa->cotizacion_enviada = $cotizacionEnviada;
        $empresa->cotizacion_enviada_at = $cotizacionEnviada ? now() : null;
        $empresa->save();

        return response()->json([
            'ok' => true,
            'message' => $cotizacionEnviada
                ? 'Cotización marcada como enviada.'
                : 'Cotización marcada como no enviada.',
            'empresa' => [
                'cotizacion_enviada' => (bool) $empresa->cotizacion_enviada,
                'cotizacion_enviada_at' => optional($empresa->cotizacion_enviada_at)->toIso8601String(),
            ],
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
        $this->authorize('update', $empresa);

        $validated = $request->validate([
            'responsable_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $empresa->responsable_user_id = $validated['responsable_user_id'] ?? null;
        $empresa->referida_at = null;

        $empresa->save();

        return response()->json([
            'ok' => true,

            'message' => $empresa->responsable_user_id
                ? 'Usuario vinculado con éxito'
                : 'Usuario desvinculado correctamente',

            'empresa' => $empresa->load('responsable'),
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
        $this->authorize('update', $empresa);

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


    public function actualizarEmpresaAccion(Request $request, Empresa $empresa, EmpresaAccion $empresaAccion): JsonResponse
    {
        $this->authorize('view', $empresa);

        if (($request->user()?->tipo_usuario ?? null) !== 'administracion') {
            abort(403);
        }

        if ((int) $empresaAccion->empresa_id !== (int) $empresa->id) {
            abort(404);
        }

        $validated = $request->validate([
            'accion_id' => ['required', 'integer', 'exists:acciones,id'],
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

        $empresaAccion->accion_id = $accion->id;
        $empresaAccion->save();

        return response()->json([
            'ok' => true,
            'message' => 'Acción actualizada.',
            'empresa_accion' => [
                'id' => $empresaAccion->id,
                'empresa_id' => $empresaAccion->empresa_id,
                'accion_id' => $empresaAccion->accion_id,
                'updated_at' => $empresaAccion->updated_at?->toIso8601String(),
            ],
            'accion' => [
                'id' => $accion->id,
                'nombre' => $accion->nombre,
                'icono' => $accion->icono,
                'color' => $accion->color,
            ],
        ]);
    }

    public function eliminarEmpresaAccion(Request $request, Empresa $empresa, EmpresaAccion $empresaAccion): JsonResponse
    {
        $this->authorize('view', $empresa);

        if (($request->user()?->tipo_usuario ?? null) !== 'administracion') {
            abort(403);
        }

        if ((int) $empresaAccion->empresa_id !== (int) $empresa->id) {
            abort(404);
        }

        $empresaAccion->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Acción eliminada.',
        ]);
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
            ->when(($request->user()?->tipo_usuario ?? null) !== 'administracion', function ($q) use ($request) {
                $q->where('responsable_user_id', (int) $request->user()->id);
            })
            ->orderBy('nombre')
            ->limit(15)
            ->get();

        return response()->json($empresas);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateEmpresa($request);

        $authUser = auth()->user();
        $data['user_id'] = auth()->id();

        if ($authUser && $authUser->tipo_usuario !== 'administracion') {
            $data['responsable_user_id'] = $authUser->id;
            $data['referida_at'] = now();
        } elseif (! empty($data['responsable_user_id'])) {
            $data['referida_at'] = now();
        }

        Empresa::query()->create($data);

        return redirect()
            ->route('empresas.index')
            ->with('status', 'Empresa creada correctamente.');
    }

    public function update(Request $request, Empresa $empresa): RedirectResponse
    {
        $this->authorize('update', $empresa);

        $data = $this->validateEmpresa($request);

        $empresa->update($data);

        return redirect()
            ->route('empresas.index')
            ->with('status', 'Empresa actualizada correctamente.');
    }

    public function destroy(Empresa $empresa): RedirectResponse
    {
        if ((auth()->user()?->tipo_usuario ?? null) !== 'administracion') {
            abort(403);
        }

        $empresa->delete();

        return redirect()
            ->route('empresas.index')
            ->with('status', 'Empresa eliminada correctamente.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateEmpresa(Request $request): array
    {
        $esAdministracion = (auth()->user()?->tipo_usuario ?? null) === 'administracion';

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'contacto_nombre' => ['required', 'string', 'max:255'],
            'nit' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string'],
            'notas' => ['nullable', 'string', 'max:5000'],
            'sector_id' => ['nullable', 'exists:sectores,id'],
            'modal_mode' => ['nullable', Rule::in(['create', 'edit'])],
            'empresa_id' => ['nullable', 'integer'],
            'responsable_user_id' => ['nullable', 'exists:users,id'],
        ]);

        unset($validated['modal_mode'], $validated['empresa_id']);

        if (! $esAdministracion) {
            unset($validated['responsable_user_id']);
        }

        return $validated;
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Configuracion\StoreCatalogoOpcionRequest;
use App\Http\Requests\Configuracion\StoreSectorRequest;
use App\Http\Requests\Configuracion\UpdateCatalogoOpcionRequest;
use App\Http\Requests\Configuracion\UpdateSectorRequest;
use App\Models\Banco;
use App\Models\CatalogoOpcion;
use App\Models\EstadoReferidoColor;
use App\Models\Sector;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfiguracionController extends Controller
{
    private const CATEGORIAS = [
        'estado-actual' => 'Estado Actual',
        'aplicativos' => 'Aplicativos',
        'procesos-electronicos' => 'Procesos Electrónicos',
        'equipos' => 'Equipos',
        'como-llego' => 'Como Llego',
        'cotizaciones' => 'Cotizaciones',
    ];


    private const ESTADOS_REFERIDO = [
        'pendiente' => 'Pendiente',
        'aprobado' => 'Aprobado',
        'rechazado' => 'Rechazado',
    ];

    private const ESTADOS_REFERIDO_DEFAULT_COLORS = [
        'pendiente' => ['bg_color' => '#FEF3C7', 'text_color' => '#92400E'],
        'aprobado' => ['bg_color' => '#D1FAE5', 'text_color' => '#065F46'],
        'rechazado' => ['bg_color' => '#FEE2E2', 'text_color' => '#991B1B'],
    ];

    public function index(): View
    {
        $sectores = $this->sectoresActivos();

        $catalogo = collect(self::CATEGORIAS)
            ->mapWithKeys(fn (string $categoria, string $slug) => [
                $slug => $this->catalogoActivoPorCategoria($categoria),
            ]);

        return view('configuracion.index', [
            'sectores' => $sectores,
            'categorias' => self::CATEGORIAS,
            'catalogoPorCategoria' => $catalogo,
            'bancos' => $this->bancosListado(),
            'estadosReferidoColores' => $this->estadoReferidoColores(),
            'estadosReferidoLabels' => self::ESTADOS_REFERIDO,
        ]);
    }

    public function sectores(): JsonResponse
    {
        return response()->json(['data' => $this->sectoresActivos()]);
    }

    public function storeSector(StoreSectorRequest $request): JsonResponse
    {
        $sector = Sector::query()->create([
            'nombre' => $request->validated('nombre'),
            'orden' => $request->validated('orden') ?? 0,
            'activo' => 1,
        ]);

        return response()->json([
            'message' => 'Sector creado correctamente.',
            'data' => $sector,
        ], 201);
    }

    public function updateSector(UpdateSectorRequest $request, Sector $sector): JsonResponse
    {
        $sector->update([
            'nombre' => $request->validated('nombre'),
            'orden' => $request->validated('orden') ?? 0,
        ]);

        return response()->json([
            'message' => 'Sector actualizado correctamente.',
            'data' => $sector,
        ]);
    }

    public function destroySector(Sector $sector): JsonResponse
    {
        $sector->update(['activo' => 0]);

        return response()->json([
            'message' => 'Sector desactivado correctamente.',
        ]);
    }

    public function catalogo(string $categoria): JsonResponse
    {
        $categoriaNombre = $this->resolveCategoria($categoria);

        return response()->json([
            'data' => $this->catalogoActivoPorCategoria($categoriaNombre),
        ]);
    }

    public function storeCatalogo(StoreCatalogoOpcionRequest $request): JsonResponse
    {
        $categoriaNombre = $this->resolveCategoria($request->validated('categoria'));

        $opcion = CatalogoOpcion::query()->create([
            'categoria' => $categoriaNombre,
            'nombre' => $request->validated('nombre'),
            'orden' => $request->validated('orden') ?? 0,

            'valor' => $request->validated('valor'),
            'valor_vinculado' => $request->validated('valor_vinculado'),
            'valor_freelance' => $request->validated('valor_freelance'),
            'activo' => 1,
        ]);

        return response()->json([
            'message' => 'Opción creada correctamente.',
            'data' => $opcion,
        ], 201);
    }

    public function updateCatalogo(UpdateCatalogoOpcionRequest $request, CatalogoOpcion $catalogoOpcion): JsonResponse
    {
        $categoriaSlug = $request->validated('categoria');
        $categoriaNombre = $this->resolveCategoria($categoriaSlug);

        $catalogoOpcion->update([
            'categoria' => $categoriaNombre,
            'nombre' => $request->validated('nombre'),
            'orden' => $request->validated('orden') ?? 0,

            'valor' => $request->validated('valor'),
            'valor_vinculado' => $request->validated('valor_vinculado'),
            'valor_freelance' => $request->validated('valor_freelance'),
        ]);

        return response()->json([
            'message' => 'Opción actualizada correctamente.',
            'data' => $catalogoOpcion,
        ]);
    }

    public function destroyCatalogo(CatalogoOpcion $catalogoOpcion): JsonResponse
    {
        $catalogoOpcion->update(['activo' => 0]);

        return response()->json([
            'message' => 'Opción desactivada correctamente.',
        ]);
    }



    public function updateEstadoReferidoColor(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validateWithBag('updateEstadoReferidoColor', [
            'estado' => ['required', 'in:pendiente,aprobado,rechazado'],
            'bg_color' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'text_color' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
        ]);

        EstadoReferidoColor::query()->updateOrCreate(
            ['estado' => $validated['estado']],
            [
                'bg_color' => strtoupper($validated['bg_color']),
                'text_color' => strtoupper($validated['text_color']),
                'activo' => 1,
            ]
        );

        return redirect()
            ->route('configuracion.index')
            ->with('success', 'Colores del estado del referido actualizados correctamente.');
    }

    private function bancosListado()
    {
        return Banco::query()
            ->withCount('usuarios')
            ->orderBy('nombre')
            ->get()
            ->map(fn (Banco $banco) => [
                'id' => $banco->id,
                'nombre' => $banco->nombre,
                'activo' => (bool) $banco->activo,
                'usuarios_count' => $banco->usuarios_count,
                'can_delete' => $banco->usuarios_count === 0,
            ]);
    }

    private function sectoresActivos()
    {
        return Sector::query()
            ->where('activo', 1)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'activo', 'orden']);
    }

    private function catalogoActivoPorCategoria(string $categoria)
    {
        return CatalogoOpcion::query()
            ->where('categoria', $categoria)
            ->where('activo', 1)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get(['id', 'categoria', 'nombre', 'valor', 'valor_vinculado', 'valor_freelance', 'orden', 'activo']);
    }


    private function estadoReferidoColores()
    {
        $coloresConfigurados = EstadoReferidoColor::query()
            ->whereIn('estado', array_keys(self::ESTADOS_REFERIDO))
            ->get()
            ->keyBy('estado');

        return collect(self::ESTADOS_REFERIDO)
            ->mapWithKeys(function (string $label, string $estado) use ($coloresConfigurados) {
                $color = $coloresConfigurados->get($estado);
                $default = self::ESTADOS_REFERIDO_DEFAULT_COLORS[$estado];

                return [$estado => [
                    'estado' => $estado,
                    'label' => $label,
                    'bg_color' => $color?->bg_color ?: $default['bg_color'],
                    'text_color' => $color?->text_color ?: $default['text_color'],
                    'activo' => $color ? (bool) $color->activo : false,
                ]];
            });
    }

    private function resolveCategoria(string $slug): string
    {
        abort_unless(array_key_exists($slug, self::CATEGORIAS), 404);

        return self::CATEGORIAS[$slug];
    }
}

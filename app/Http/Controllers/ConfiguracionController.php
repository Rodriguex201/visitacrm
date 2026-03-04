<?php

namespace App\Http\Controllers;

use App\Http\Requests\Configuracion\StoreCatalogoOpcionRequest;
use App\Http\Requests\Configuracion\StoreSectorRequest;
use App\Http\Requests\Configuracion\UpdateCatalogoOpcionRequest;
use App\Http\Requests\Configuracion\UpdateSectorRequest;
use App\Models\CatalogoOpcion;
use App\Models\Sector;
use Illuminate\Http\JsonResponse;
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
            ->get(['id', 'categoria', 'nombre', 'valor', 'orden', 'activo']);
    }

    private function resolveCategoria(string $slug): string
    {
        abort_unless(array_key_exists($slug, self::CATEGORIAS), 404);

        return self::CATEGORIAS[$slug];
    }
}

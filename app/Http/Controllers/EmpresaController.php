<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Carbon\Carbon;
use App\Models\Sector;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmpresaController extends Controller
{
    public function index(): View
    {
        $empresas = Empresa::query()
            ->with('sector')
            ->latest('id')
            ->paginate(10);

        $sectores = Sector::query()
            ->orderBy('nombre')
            ->get();

        return view('empresas.index', compact('empresas', 'sectores'));
    }

    public function show(Request $request, Empresa $empresa): View
    {
        $range = $request->query('range', 'todo');

        if (!in_array($range, ['hoy', '7d', 'todo'], true)) {
            $range = 'todo';
        }

        $empresa->load('sector');

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

        return view('empresas.show', compact('empresa', 'visitas', 'range'));
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

<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Sector;
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

    public function show(Empresa $empresa): View
    {
        $empresa->load('sector');

        return view('empresas.show', compact('empresa'));
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
            'sector_id' => ['nullable', 'exists:sectores,id'],
            'modal_mode' => ['nullable', Rule::in(['create', 'edit'])],
            'empresa_id' => ['nullable', 'integer'],
        ]);

        unset($validated['modal_mode'], $validated['empresa_id']);

        return $validated;
    }
}

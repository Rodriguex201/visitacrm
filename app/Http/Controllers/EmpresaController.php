<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmpresaController extends Controller
{
    public function index(): View
    {
        return view('empresas.index');
    }

    public function show(int $id): View
    {
        $empresas = [
            1 => ['id' => 1, 'nombre' => 'Lalanela', 'ciudad' => 'Pereira', 'sector' => null, 'fecha' => '21/01/2026'],
            2 => ['id' => 2, 'nombre' => 'Mazda minuto', 'ciudad' => 'Armenia', 'sector' => null, 'fecha' => '22/01/2026'],
            3 => ['id' => 3, 'nombre' => 'Mundial armenia', 'ciudad' => 'Armenia', 'sector' => null, 'fecha' => '23/01/2026'],
        ];

        abort_unless(isset($empresas[$id]), 404);

        $empresa = $empresas[$id];

        return view('empresas.show', compact('empresa'));
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('empresas.index')->with('status', 'Empresa creada (demo).');
    }
}

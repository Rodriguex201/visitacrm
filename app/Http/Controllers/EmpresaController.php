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

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('empresas.index')->with('status', 'Empresa creada (demo).');
    }
}

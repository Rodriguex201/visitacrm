<?php

namespace App\Http\Controllers;

use App\Models\Empresa;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::orderBy('id', 'desc')->get();
        return view('empresas.index', compact('empresas'));
    }

    public function create() {}
    public function store(\Illuminate\Http\Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(\Illuminate\Http\Request $request, string $id) {}
    public function destroy(string $id) {}
}

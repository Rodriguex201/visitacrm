<?php

namespace App\Http\Controllers;

use App\Models\Accion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccionesController extends Controller
{
    public function index(): View
    {
        $acciones = Accion::query()->orderBy('orden')->orderBy('id')->get();

        return view('acciones.index', compact('acciones'));
    }

    public function update(Request $request, Accion $accion): JsonResponse
    {
        $validated = $request->validate([
            'activo' => ['nullable', 'boolean'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ]);

        if (array_key_exists('activo', $validated)) {
            $accion->activo = (bool) $validated['activo'];
        }

        if (array_key_exists('orden', $validated)) {
            $accion->orden = (int) $validated['orden'];
        }

        $accion->save();

        return response()->json([
            'ok' => true,
            'accion' => $accion,
        ]);
    }
}

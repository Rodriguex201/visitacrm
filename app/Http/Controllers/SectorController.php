<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SectorController extends Controller
{
    public function index(): JsonResponse
    {
        $sectores = Sector::query()
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'activo', 'orden']);

        return response()->json(['data' => $sectores]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:sectores,nombre'],
            'orden' => ['nullable', 'integer', 'min:1'],
        ]);

        $sector = Sector::query()->create([
            'nombre' => trim($validated['nombre']),
            'orden' => $validated['orden'] ?? null,
            'activo' => 1,
        ]);

        return response()->json([
            'message' => 'Sector creado correctamente.',
            'data' => $sector,
        ], 201);
    }

    public function update(Request $request, Sector $sector): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sectores', 'nombre')->ignore($sector->id),
            ],
            'activo' => ['required', 'boolean'],
            'orden' => ['nullable', 'integer', 'min:1'],
        ]);

        $sector->update([
            'nombre' => trim($validated['nombre']),
            'activo' => (int) $validated['activo'],
            'orden' => $validated['orden'] ?? null,
        ]);

        return response()->json([
            'message' => 'Sector actualizado correctamente.',
            'data' => $sector->fresh(['empresas']),
        ]);
    }

    public function destroy(Sector $sector): JsonResponse
    {
        $enUso = $sector->empresas()->exists();

        if ($enUso) {
            $sector->update(['activo' => 0]);

            return response()->json([
                'message' => 'El sector está en uso y fue marcado como inactivo.',
                'data' => $sector->fresh(),
                'soft_deleted' => true,
            ]);
        }

        $sector->delete();

        return response()->json([
            'message' => 'Sector eliminado correctamente.',
            'soft_deleted' => false,
        ]);
    }
}

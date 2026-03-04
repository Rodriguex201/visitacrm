<?php

namespace App\Http\Controllers;

use App\Http\Requests\Configuracion\StoreBancoRequest;
use App\Http\Requests\Configuracion\UpdateBancoRequest;
use App\Models\Banco;
use Illuminate\Http\JsonResponse;

class ConfiguracionBancoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->bancosListado(),
        ]);
    }

    public function store(StoreBancoRequest $request): JsonResponse
    {
        $banco = Banco::query()->create([
            'nombre' => $request->validated('nombre'),
            'activo' => 1,
        ]);

        return response()->json([
            'message' => 'Banco creado correctamente.',
            'data' => $this->normalizarBanco($banco->loadCount('usuarios')),
        ], 201);
    }

    public function update(UpdateBancoRequest $request, Banco $banco): JsonResponse
    {
        $banco->update([
            'nombre' => $request->validated('nombre'),
        ]);

        return response()->json([
            'message' => 'Banco actualizado correctamente.',
            'data' => $this->normalizarBanco($banco->loadCount('usuarios')),
        ]);
    }

    public function activar(Banco $banco): JsonResponse
    {
        $banco->update(['activo' => 1]);

        return response()->json([
            'message' => 'Banco activado correctamente.',
            'data' => $this->normalizarBanco($banco->refresh()->loadCount('usuarios')),
        ]);
    }

    public function desactivar(Banco $banco): JsonResponse
    {
        $banco->update(['activo' => 0]);

        return response()->json([
            'message' => 'Banco desactivado correctamente.',
            'data' => $this->normalizarBanco($banco->refresh()->loadCount('usuarios')),
        ]);
    }

    public function destroy(Banco $banco): JsonResponse
    {
        $banco->loadCount('usuarios');

        if ($banco->usuarios_count > 0) {
            return response()->json([
                'message' => 'No se puede eliminar un banco que está en uso.',
            ], 422);
        }

        $banco->delete();

        return response()->json([
            'message' => 'Banco eliminado correctamente.',
        ]);
    }

    private function bancosListado()
    {
        return Banco::query()
            ->withCount('usuarios')
            ->orderBy('nombre')
            ->get()
            ->map(fn (Banco $banco) => $this->normalizarBanco($banco));
    }

    private function normalizarBanco(Banco $banco): array
    {
        return [
            'id' => $banco->id,
            'nombre' => $banco->nombre,
            'activo' => (bool) $banco->activo,
            'usuarios_count' => $banco->usuarios_count ?? 0,
            'can_delete' => ($banco->usuarios_count ?? 0) === 0,
        ];
    }
}

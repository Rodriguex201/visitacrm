<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Empresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactoController extends Controller
{
    public function store(Request $request, Empresa $empresa): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'cargo' => ['nullable', 'string', 'max:120'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
            'es_principal' => ['nullable', 'boolean'],
        ]);

        $esPrincipal = (bool) ($validated['es_principal'] ?? false);

        $contacto = DB::transaction(function () use ($empresa, $validated, $esPrincipal) {
            if ($esPrincipal) {
                Contacto::query()
                    ->where('empresa_id', $empresa->id)
                    ->update(['es_principal' => 0]);
            }

            return Contacto::query()->create([
                'empresa_id' => $empresa->id,
                'nombre' => $validated['nombre'],
                'cargo' => $validated['cargo'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'email' => $validated['email'] ?? null,
                'es_principal' => $esPrincipal,
            ]);
        });

        if ($request->expectsJson()) {
            $contactos = $empresa->contactos()
                ->orderByDesc('es_principal')
                ->latest()
                ->get();

            return response()->json([
                'message' => 'Contacto agregado correctamente.',
                'contacto' => $contacto,
                'contactos_html' => view('empresas.partials.contactos-list', [
                    'contactos' => $contactos,
                ])->render(),
            ]);
        }

        return redirect()
            ->back()
            ->with('status', 'Contacto agregado correctamente.');
    }
}

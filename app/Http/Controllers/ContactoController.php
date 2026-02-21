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

        $validated = $this->validateContacto($request);
        $esPrincipal = (bool) ($validated['es_principal'] ?? false);
        DB::transaction(function () use ($empresa, $validated, $esPrincipal): void {
            if ($esPrincipal) {
                Contacto::query()
                    ->where('empresa_id', $empresa->id)
                    ->update(['es_principal' => 0]);
            }

            Contacto::query()->create([
                'empresa_id' => $empresa->id,
                'nombre' => $validated['nombre'],
                'cargo' => $validated['cargo'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'email' => $validated['email'] ?? null,
                'es_principal' => $esPrincipal,
            ]);
        });

        return $this->response($request, $empresa, 'Contacto agregado correctamente.');
    }

    public function update(Request $request, Empresa $empresa, Contacto $contacto): JsonResponse|RedirectResponse
    {
        abort_unless($contacto->empresa_id === $empresa->id, 404);

        $validated = $this->validateContacto($request);
        $esPrincipal = (bool) ($validated['es_principal'] ?? false);

        DB::transaction(function () use ($empresa, $contacto, $validated, $esPrincipal): void {

            if ($esPrincipal) {
                Contacto::query()
                    ->where('empresa_id', $empresa->id)
                    ->update(['es_principal' => 0]);
            }


            $contacto->update([

                'nombre' => $validated['nombre'],
                'cargo' => $validated['cargo'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'email' => $validated['email'] ?? null,
                'es_principal' => $esPrincipal,
            ]);
        });


        return $this->response($request, $empresa, 'Contacto actualizado correctamente.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateContacto(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'cargo' => ['nullable', 'string', 'max:120'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
            'es_principal' => ['nullable', 'boolean'],
        ]);
    }

    private function response(Request $request, Empresa $empresa, string $message): JsonResponse|RedirectResponse
    {

        if ($request->expectsJson()) {
            $contactos = $empresa->contactos()
                ->orderByDesc('es_principal')
                ->latest()
                ->get();

            return response()->json([

                'message' => $message,

                'contactos_html' => view('empresas.partials.contactos-list', [
                    'contactos' => $contactos,
                ])->render(),
            ]);
        }


        return redirect()->back()->with('status', $message);

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Accion;
use App\Models\EmpresaAccion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccionesController extends Controller
{
    private const ICONOS_PERMITIDOS = [
        'phone',
        'globe',
        'video',
        'users',
        'building-2',
        'user-minus',
        'calendar',
        'mail',
        'message-circle',
        'file-text',
        'check',
        'x',
        'shopping-bag',
        'clipboard',
        'map-pin',
    ];

    public function manage(Request $request): View
    {
        if (($request->user()?->tipo_usuario ?? null) !== 'administracion') {
            abort(403);
        }

        $acciones = Accion::query()
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        return view('acciones.manage', [
            'acciones' => $acciones,
            'iconosPermitidos' => self::ICONOS_PERMITIDOS,
            'nextOrden' => ((int) (Accion::query()->max('orden') ?? 0)) + 1,
            'defaultIcono' => self::ICONOS_PERMITIDOS[0] ?? 'phone',
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if (($request->user()?->tipo_usuario ?? null) !== 'administracion') {
            abort(403);
        }

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'icono' => ['required', 'string', 'max:50', Rule::in(self::ICONOS_PERMITIDOS)],
            'color' => ['required', 'regex:/^#(?:[0-9a-fA-F]{6})$/'],
            'orden' => ['nullable', 'integer', 'min:1'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $orden = isset($validated['orden']) && $validated['orden'] !== null
            ? (int) $validated['orden']
            : ((int) (Accion::query()->max('orden') ?? 0)) + 1;

        $accion = Accion::query()->create([
            'nombre' => $validated['nombre'],
            'icono' => $validated['icono'],
            'color' => $validated['color'],
            'orden' => $orden,
            'activo' => (bool) ($validated['activo'] ?? false),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Acción creada.',
                'accion' => $accion,
            ], 201);
        }

        return redirect()
            ->route('acciones.manage')
            ->with('status', 'Acción creada.');
    }

    public function update(Request $request, Accion $accion): JsonResponse|RedirectResponse
    {
        if (($request->user()?->tipo_usuario ?? null) !== 'administracion') {
            abort(403);
        }

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'icono' => ['required', 'string', 'max:50', Rule::in(self::ICONOS_PERMITIDOS)],
            'color' => ['nullable', 'string', 'max:30'],
            'orden' => ['required', 'integer', 'min:1'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $accion->update([
            'nombre' => $validated['nombre'],
            'icono' => $validated['icono'],
            'color' => $validated['color'] ?? null,
            'orden' => (int) $validated['orden'],
            'activo' => (bool) ($validated['activo'] ?? false),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Acción actualizada.',
                'accion' => $accion,
            ]);
        }

        return redirect()
            ->route('acciones.manage')
            ->with('status', 'Acción actualizada.');
    }

    public function destroy(Request $request, Accion $accion): RedirectResponse|JsonResponse
    {
        if (($request->user()?->tipo_usuario ?? null) !== 'administracion') {
            abort(403);
        }

        $estaEnUso = EmpresaAccion::query()
            ->where('accion_id', $accion->id)
            ->exists();

        if ($estaEnUso) {
            $accion->activo = false;
            $accion->save();

            $message = 'Acción en uso: se desactivó';
        } else {
            $accion->delete();
            $message = 'Acción eliminada.';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
            ]);
        }

        return redirect()
            ->route('acciones.manage')
            ->with('status', $message);
    }
}

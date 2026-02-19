<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VisitaController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'empresa_id' => ['required', 'exists:empresas,id'],
            'fecha_hora' => ['required', 'date'],
            'estado' => ['required', 'in:programada,realizada,cancelada'],
            'resultado' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string'],
        ]);

        $data['user_id'] = auth()->id();

        Visita::query()->create($data);

        return back()->with('status', 'Visita guardada correctamente.');
    }
}

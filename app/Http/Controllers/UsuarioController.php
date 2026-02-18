<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(): View
    {
        $usuarios = User::orderByDesc('created_at')->paginate(10);

        return view('usuarios.index', compact('usuarios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'tipo_usuario' => ['required', 'in:freelance,vinculado,administracion'],
        ]);

        User::create([
            'name' => $validated['name'],
            'telefono' => $validated['telefono'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($request->password),
            'tipo_usuario' => $validated['tipo_usuario'],
        ]);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }
}

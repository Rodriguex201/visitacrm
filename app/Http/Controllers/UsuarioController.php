<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rule;

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
        $validated = $request->validateWithBag('createUser', [
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
            'password' => Hash::make($validated['password']),
            'tipo_usuario' => $validated['tipo_usuario'],
        ]);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validateWithBag('updateUser', [
            'name' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'tipo_usuario' => ['required', 'in:freelance,vinculado,administracion'],
        ]);

        $user->name = $validated['name'];
        $user->telefono = $validated['telefono'] ?? null;
        $user->email = $validated['email'];
        $user->tipo_usuario = $validated['tipo_usuario'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');

    }
}

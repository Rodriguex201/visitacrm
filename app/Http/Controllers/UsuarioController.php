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
            'codigo' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9]+$/', 'unique:users,codigo'],
            'name' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'tipo_usuario' => ['required', 'in:freelance,vinculado,administracion'],
            'cta_banco' => ['nullable', 'string', 'max:60'],
            'ciudad' => ['nullable', 'string', 'max:255'],
        ]);

        User::create([
            'codigo' => strtoupper($validated['codigo']),
            'name' => $validated['name'],
            'telefono' => $validated['telefono'] ?? null,
            'direccion' => $validated['direccion'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'tipo_usuario' => $validated['tipo_usuario'],
            'cta_banco' => $validated['cta_banco'] ?? null,
            'ciudad' => $validated['ciudad'] ?? null,
        ]);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validateWithBag('updateUser', [
            'codigo' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Za-z0-9]+$/',
                Rule::unique('users', 'codigo')->ignore($user->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'tipo_usuario' => ['required', 'in:freelance,vinculado,administracion'],
            'cta_banco' => ['nullable', 'string', 'max:60'],
            'ciudad' => ['nullable', 'string', 'max:255'],
        ]);

        $user->codigo = strtoupper($validated['codigo']);
        $user->name = $validated['name'];
        $user->telefono = $validated['telefono'] ?? null;
        $user->direccion = $validated['direccion'] ?? null;
        $user->email = $validated['email'];
        $user->tipo_usuario = $validated['tipo_usuario'];
        $user->cta_banco = $validated['cta_banco'] ?? null;
        $user->ciudad = $validated['ciudad'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');

    }
}

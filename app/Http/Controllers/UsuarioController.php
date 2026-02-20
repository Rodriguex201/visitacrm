<?php

namespace App\Http\Controllers;

use App\Models\Banco;
use App\Models\TipoUsuario;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(): View
    {
        $usuarios = User::query()
            ->with('banco')
            ->orderByDesc('created_at')
            ->paginate(10);

        $bancos = Banco::query()
            ->where('activo', 1)
            ->orderBy('nombre')
            ->get();

        $tipos = TipoUsuario::query()
            ->get()
            ->keyBy('nombre');

        return view('usuarios.index', compact('usuarios', 'bancos', 'tipos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('createUser', [
            'name' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'tipo_usuario' => ['required', 'in:freelance,vinculado,administracion'],
            'banco_id' => ['nullable', 'exists:bancos,id'],
            'cta_banco' => ['nullable', 'string', 'max:60'],
            'ciudad' => ['nullable', 'string', 'max:255'],
        ]);

        $maxAttempts = 3;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                DB::transaction(function () use ($validated): void {
                    $codigo = $this->generateCodigo($validated['tipo_usuario']);

                    User::create([
                        'codigo' => $codigo,
                        'name' => $validated['name'],
                        'telefono' => $validated['telefono'] ?? null,
                        'direccion' => $validated['direccion'] ?? null,
                        'email' => $validated['email'],
                        'password' => Hash::make($validated['password']),
                        'tipo_usuario' => $validated['tipo_usuario'],
                        'banco_id' => $validated['banco_id'] ?? null,
                        'cta_banco' => $validated['cta_banco'] ?? null,
                        'ciudad' => $validated['ciudad'] ?? null,
                    ]);
                });

                return redirect()
                    ->route('usuarios.index')
                    ->with('success', 'Usuario creado correctamente.');
            } catch (QueryException $exception) {
                if ($attempt < $maxAttempts && $this->isDuplicateKeyException($exception)) {
                    continue;
                }

                throw $exception;
            }
        }

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
            'banco_id' => ['nullable', 'exists:bancos,id'],
            'cta_banco' => ['nullable', 'string', 'max:60'],
            'ciudad' => ['nullable', 'string', 'max:255'],
        ]);

        $user->codigo = strtoupper($validated['codigo']);
        $user->name = $validated['name'];
        $user->telefono = $validated['telefono'] ?? null;
        $user->direccion = $validated['direccion'] ?? null;
        $user->email = $validated['email'];
        $user->tipo_usuario = $validated['tipo_usuario'];
        $user->banco_id = $validated['banco_id'] ?? null;
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

    private function generateCodigo(string $tipoUsuario): string
    {
        $prefix = $this->codigoPrefix($tipoUsuario);

        $lockName = "users_codigo_{$prefix}";
        DB::select('SELECT GET_LOCK(?, 10)', [$lockName]);

        try {
            $ultimoCodigo = User::query()
                ->where('codigo', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderByRaw('CAST(SUBSTRING(codigo, 3) AS UNSIGNED) DESC')
                ->value('codigo');

            $consecutivo = 1;

            if ($ultimoCodigo && preg_match('/^' . preg_quote($prefix, '/') . '(\d{4})$/', $ultimoCodigo, $matches)) {
                $consecutivo = ((int) $matches[1]) + 1;
            }

            return $prefix . str_pad((string) $consecutivo, 4, '0', STR_PAD_LEFT);
        } finally {
            DB::select('SELECT RELEASE_LOCK(?)', [$lockName]);
        }
    }

    private function codigoPrefix(string $tipoUsuario): string
    {
        return match ($tipoUsuario) {
            'vinculado' => 'V-',
            'freelance' => 'F-',
            'administracion' => 'A-',
            default => 'F-',
        };
    }

    private function isDuplicateKeyException(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);

        return in_array($sqlState, ['23000', '23505'], true)
            || in_array($driverCode, [1062, 19], true);
    }
}

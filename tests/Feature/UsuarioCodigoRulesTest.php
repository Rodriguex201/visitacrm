<?php

use App\Models\User;

function adminForCodigoRulesTests(): User
{
    return User::factory()->create([
        'codigo' => 'A-9001',
        'tipo_usuario' => 'administracion',
    ]);
}

function validUpdateUserPayload(User $user, array $overrides = []): array
{
    return array_merge([
        'codigo' => $user->codigo,
        'name' => $user->name,
        'telefono' => $user->telefono,
        'direccion' => $user->direccion,
        'email' => $user->email,
        'password' => '',
        'tipo_usuario' => $user->tipo_usuario,
        'banco_id' => $user->banco_id,
        'usuario_de_id' => $user->usuario_de_id,
        'cta_banco' => $user->cta_banco,
        'ciudad' => $user->ciudad,
    ], $overrides);
}

test('freelance user update accepts F prefix codes', function () {
    $admin = adminForCodigoRulesTests();
    $user = User::factory()->create([
        'codigo' => 'F-0003',
        'tipo_usuario' => 'freelance',
        'email' => 'freelance@dominio.com',
    ]);

    $response = $this
        ->actingAs($admin)
        ->from('/usuarios')
        ->put(route('usuarios.update', $user), validUpdateUserPayload($user, [
            'name' => 'Freelance Actualizado',
        ]));

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('usuarios.index'));

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'codigo' => 'F-0003',
        'name' => 'Freelance Actualizado',
    ]);
});

test('user update rejects codes with unsupported prefixes', function () {
    $admin = adminForCodigoRulesTests();
    $user = User::factory()->create([
        'codigo' => 'V-0007',
        'tipo_usuario' => 'vinculado',
        'email' => 'vinculado@dominio.com',
    ]);

    $response = $this
        ->actingAs($admin)
        ->from('/usuarios')
        ->put(route('usuarios.update', $user), validUpdateUserPayload($user, [
            'codigo' => 'X-0007',
        ]));

    $response
        ->assertSessionHasErrorsIn('updateUser', ['codigo'])
        ->assertRedirect('/usuarios');
});

test('user creation generates codes from the configured prefix map', function (string $tipoUsuario, string $codigoEsperado) {
    $admin = adminForCodigoRulesTests();

    $response = $this
        ->actingAs($admin)
        ->from('/usuarios')
        ->post('/usuarios', [
            'name' => "Usuario {$tipoUsuario}",
            'telefono' => '3001234567',
            'direccion' => 'Calle 123',
            'email' => "{$tipoUsuario}@dominio.com",
            'password' => 'secret123',
            'tipo_usuario' => $tipoUsuario,
            'banco_id' => null,
            'usuario_de_id' => null,
            'cta_banco' => null,
            'ciudad' => 'Bogota',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/usuarios');

    $this->assertDatabaseHas('users', [
        'email' => "{$tipoUsuario}@dominio.com",
        'tipo_usuario' => $tipoUsuario,
        'codigo' => $codigoEsperado,
    ]);
})->with([
    'freelance' => ['freelance', 'F-0001'],
    'vinculado' => ['vinculado', 'V-0001'],
    'administracion' => ['administracion', 'A-0001'],
]);

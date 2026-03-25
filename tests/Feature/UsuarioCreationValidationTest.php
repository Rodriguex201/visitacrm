<?php

use App\Models\User;

function adminUser(): User
{
    return User::factory()->create([
        'codigo' => 'A-0001',
        'tipo_usuario' => 'administracion',
    ]);
}

function validUserPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Usuario Prueba',
        'telefono' => '3001234567',
        'direccion' => 'Calle 123',
        'email' => 'usuario@dominio.com',
        'password' => 'secret123',
        'tipo_usuario' => 'freelance',
        'banco_id' => null,
        'usuario_de_id' => null,
        'cta_banco' => null,
        'ciudad' => 'Bogotá',
    ], $overrides);
}

test('user creation accepts common email domains', function (string $email) {
    $admin = adminUser();

    $response = $this
        ->actingAs($admin)
        ->from('/usuarios')
        ->post('/usuarios', validUserPayload(['email' => $email]));

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/usuarios');

    $this->assertDatabaseHas('users', [
        'email' => $email,
        'name' => 'Usuario Prueba',
    ]);
})->with([
    'dominio .es' => ['usuario@empresa.es'],
    'dominio .co' => ['usuario@empresa.co'],
    'dominio .com' => ['usuario@empresa.com'],
    'dominio .com.co' => ['usuario@empresa.com.co'],
]);

test('invalid email stops user creation and does not persist record', function () {
    $admin = adminUser();
    $invalidEmail = 'correo-invalido@dominio';

    $response = $this
        ->actingAs($admin)
        ->from('/usuarios')
        ->post('/usuarios', validUserPayload(['email' => $invalidEmail]));

    $response
        ->assertSessionHasErrorsIn('createUser', ['email'])
        ->assertRedirect('/usuarios');

    $this->assertDatabaseMissing('users', [
        'email' => $invalidEmail,
    ]);
});

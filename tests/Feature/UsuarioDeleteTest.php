<?php

use App\Models\User;

function adminForDeleteTests(): User
{
    return User::factory()->create([
        'codigo' => 'A-9000',
        'tipo_usuario' => 'administracion',
    ]);
}

test('admin can delete a user without related records', function () {
    $admin = adminForDeleteTests();
    $user = User::factory()->create([
        'codigo' => 'F-0001',
        'tipo_usuario' => 'freelance',
    ]);

    $response = $this
        ->actingAs($admin)
        ->delete(route('usuarios.destroy', $user));

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('usuarios.index'));

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

test('admin cannot delete own authenticated user', function () {
    $admin = adminForDeleteTests();

    $response = $this
        ->actingAs($admin)
        ->delete(route('usuarios.destroy', $admin));

    $response
        ->assertRedirect(route('usuarios.index'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('users', [
        'id' => $admin->id,
    ]);
});


test('admin cannot delete user with related child users', function () {
    $admin = adminForDeleteTests();

    $parent = User::factory()->create([
        'codigo' => 'F-0100',
        'tipo_usuario' => 'freelance',
    ]);

    User::factory()->create([
        'codigo' => 'F-0101',
        'tipo_usuario' => 'freelance',
        'usuario_de_id' => $parent->id,
    ]);

    $response = $this
        ->actingAs($admin)
        ->delete(route('usuarios.destroy', $parent));

    $response
        ->assertRedirect(route('usuarios.index'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('users', [
        'id' => $parent->id,
    ]);
});

<?php

namespace App\Policies;

use App\Models\Empresa;
use App\Models\User;

class EmpresaPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->tipo_usuario, ['administracion', 'vinculado', 'freelance'], true);
    }

    public function view(User $user, Empresa $empresa): bool
    {
        if ($user->tipo_usuario === 'administracion') {
            return true;
        }

        return (int) $empresa->responsable_user_id === (int) $user->id;
    }

    public function update(User $user, Empresa $empresa): bool
    {
        return $this->view($user, $empresa);
    }
}

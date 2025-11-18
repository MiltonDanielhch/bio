<?php

namespace App\Policies;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmpresaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('browse_empresas');
    }

    public function view(User $user, Empresa $empresa)
    {
        return $user->hasPermission('read_empresas');
    }

    public function create(User $user)
    {
        return $user->hasPermission('add_empresas');
    }

    public function update(User $user, Empresa $empresa)
    {
        return $user->hasPermission('edit_empresas');
    }

    public function delete(User $user, Empresa $empresa)
    {
        return $user->hasPermission('delete_empresas');
    }
}

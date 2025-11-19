<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('browse_users');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasPermission('read_users');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('add_users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Un usuario no puede editarse a sí mismo a través de este CRUD
        // y no se puede editar al super admin (ID 1)
        if ($user->id === $model->id || $model->id === 1) {
            return false;
        }
        return $user->hasPermission('edit_users');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Un usuario no puede eliminarse a sí mismo
        // y no se puede eliminar al super admin (ID 1)
        if ($user->id === $model->id || $model->id === 1) {
            return false;
        }
        return $user->hasPermission('delete_users');
    }
}

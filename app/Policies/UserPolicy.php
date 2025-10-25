<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['chairman', 'md', 'general_manager', 'department_head', 'hr_officer']);
    }

    public function view(User $user, User $model): bool
    {
        // Can view self, subordinates, or if HR/management
        return $user->id === $model->id ||
               $model->reports_to === $user->id ||
               in_array($user->role->name, ['chairman', 'md', 'general_manager', 'department_head', 'hr_officer']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role->name, ['md', 'hr_officer']);
    }

    public function update(User $user, User $model): bool
    {
        // Can update self (limited fields) or if HR/MD
        if ($user->id === $model->id) {
            return true; // Limited fields only
        }

        return in_array($user->role->name, ['md', 'hr_officer']);
    }

    public function delete(User $user, User $model): bool
    {
        return in_array($user->role->name, ['md', 'hr_officer']);
    }

    public function updateRole(User $user, User $model): bool
    {
        return $user->role->name === 'md';
    }
}

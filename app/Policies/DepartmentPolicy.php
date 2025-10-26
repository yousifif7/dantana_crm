<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Department;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['chairman', 'md', 'general_manager', 'department_head', 'hr_officer']);
    }

    public function view(User $user, Department $department): bool
    {
        return in_array($user->role->name, ['chairman', 'md', 'general_manager', 'department_head', 'hr_officer'])
            || $user->department_id === $department->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role->name, ['md', 'hr_officer']);
    }

    public function update(User $user, Department $department): bool
    {
        return in_array($user->role->name, ['md', 'hr_officer']);
    }

    public function delete(User $user, Department $department): bool
    {
        return in_array($user->role->name, ['md', 'hr_officer']);
    }
}

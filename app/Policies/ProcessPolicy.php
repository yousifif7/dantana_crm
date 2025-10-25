<?php

namespace App\Policies;

use App\Models\Process;
use App\Models\User;

class ProcessPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All users can view processes
    }

    public function view(User $user, Process $process): bool
    {
        // Can view if assigned, created, or has management role
        return $process->assigned_to === $user->id ||
               $process->created_by === $user->id ||
               in_array($user->role->name, ['chairman', 'md', 'general_manager', 'department_head']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role->name, ['md', 'general_manager', 'department_head', 'executive']);
    }

    public function update(User $user, Process $process): bool
    {
        // Assigned user can update status, creator can update details
        return $process->assigned_to === $user->id ||
               $process->created_by === $user->id ||
               in_array($user->role->name, ['department_head', 'general_manager', 'md']);
    }

    public function delete(User $user, Process $process): bool
    {
        return $process->created_by === $user->id ||
               in_array($user->role->name, ['md', 'general_manager']);
    }
}


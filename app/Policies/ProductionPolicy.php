<?php

namespace App\Policies;

use App\Models\ProductionRecord;
use App\Models\User;

class ProductionPolicy
{
    public function viewAny(User $user): bool
    {
        if (in_array(strtolower($user->role->name), ['md', 'managing_director'])) {
            return true;
        }

        return $user->hasPermission('production.view', 'view');
    }

    public function view(User $user, ProductionRecord $record): bool
    {
        if (in_array(strtolower($user->role->name), ['md', 'managing_director'])) {
            return true;
        }

        return $user->hasPermission('production.view', 'view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('production.create', 'edit');
    }

    public function update(User $user, ProductionRecord $record): bool
    {
        if ($record->status !== 'pending') {
            return false;
        }

        return $record->created_by === $user->id || 
               $user->hasPermission('production.edit', 'edit');
    }

    public function delete(User $user, ProductionRecord $record): bool
    {
        return in_array($user->role->name, ['md', 'general_manager']);
    }

    public function approve(User $user, ProductionRecord $record): bool
    {
        return $user->canApprove() && $user->hasPermission('production.approve', 'approve');
    }
}
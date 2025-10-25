<?php

namespace App\Policies;

use App\Models\InventoryItem;
use App\Models\User;

class InventoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('inventory.view', 'view');
    }

    public function view(User $user, InventoryItem $item): bool
    {
        return $user->hasPermission('inventory.view', 'view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('inventory.create', 'edit');
    }

    public function update(User $user, InventoryItem $item): bool
    {
        return $user->hasPermission('inventory.edit', 'edit');
    }

    public function delete(User $user, InventoryItem $item): bool
    {
        return in_array($user->role->name, ['md', 'general_manager', 'department_head']);
    }

    public function adjustStock(User $user, InventoryItem $item): bool
    {
        return $user->hasPermission('inventory.adjust', 'edit');
    }
}

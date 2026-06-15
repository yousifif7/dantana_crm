<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        if (in_array(strtolower($user->role->name), ['md', 'managing_director', 'chairman'])) {
            return true;
        }

        return $user->hasPermission('procurement.view', 'view');
    }

    public function view(User $user, PurchaseOrder $order): bool
    {
        if (in_array(strtolower($user->role->name), ['md', 'managing_director', 'chairman'])) {
            return true;
        }

        return $user->hasPermission('procurement.view', 'view')
            || $order->requested_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('procurement.create', 'edit');
    }

    public function update(User $user, PurchaseOrder $order): bool
    {
        if (!in_array($order->status, ['draft', 'pending'])) {
            return false;
        }

        return $order->requested_by === $user->id
            || $user->hasPermission('procurement.edit', 'edit');
    }

    public function delete(User $user, PurchaseOrder $order): bool
    {
        return in_array($user->role->name, ['md', 'procurement_officer'])
            && in_array($order->status, ['draft', 'pending', 'cancelled']);
    }

    public function approve(User $user, PurchaseOrder $order): bool
    {
        if (in_array(strtolower($user->role->name), ['md', 'managing_director', 'chairman'])) {
            return $user->canApprove();
        }

        return $user->hasPermission('procurement.approve', 'approve');
    }
}

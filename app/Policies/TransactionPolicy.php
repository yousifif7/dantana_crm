<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        // Allow MD to view all transactions regardless of granular permissions
        if (in_array(strtolower($user->role->name), ['md', 'managing_director'])) {
            return true;
        }

        return $user->hasPermission('transactions.view', 'view');
    }

    public function view(User $user, Transaction $transaction): bool
    {
        if (in_array(strtolower($user->role->name), ['md', 'managing_director'])) {
            return true;
        }

        return $user->hasPermission('transactions.view', 'view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('transactions.create', 'edit');
    }

    public function update(User $user, Transaction $transaction): bool
    {
        // Can only update if pending and created by user, or has edit permission
        if ($transaction->status !== 'pending') {
            return false;
        }

        return $transaction->created_by === $user->id || 
               $user->hasPermission('transactions.edit', 'edit');
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        // Only MD or creator can delete pending transactions
        return ($transaction->status === 'pending' && $transaction->created_by === $user->id) ||
               in_array($user->role->name, ['md', 'chairman']);
    }

    public function approve(User $user, Transaction $transaction): bool
    {
        if (in_array(strtolower($user->role->name), ['md', 'managing_director', 'chairman'])) {
            return $user->canApprove();
        }

        return $user->canApprove() && $user->hasPermission('transactions.approve', 'approve');
    }
}

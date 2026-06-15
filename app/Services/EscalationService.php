<?php

namespace App\Services;

use App\Events\EscalationCreated;
use App\Models\Escalation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EscalationNotification;

class EscalationService
{
    public function escalate(
        Model $model,
        User $fromUser,
        string $reason,
        ?string $description = null
    ): ?Escalation {
        $toUser = $this->getEscalationTarget($fromUser);

        if (!$toUser) {
            return null;
        }

        $escalation = Escalation::create([
            'escalatable_type' => get_class($model),
            'escalatable_id' => $model->id,
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'reason' => $reason,
            'description' => $description,
            'status' => 'pending',
            'escalated_at' => now(),
        ]);

        // Send notification
        $toUser->notify(new EscalationNotification($escalation));

        event(new EscalationCreated($escalation));

        return $escalation;
    }

    public function resolve(Escalation $escalation, User $user): bool
    {
        if ($escalation->to_user_id !== $user->id) {
            return false;
        }

        $escalation->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        return true;
    }

    private function getEscalationTarget(User $user): ?User
    {
        // Escalation hierarchy
        $escalationMap = [
            'support_staff' => 'executive',
            'executive' => 'department_head',
            'officer' => 'department_head',
            'department_head' => 'general_manager',
            'general_manager' => 'md',
            'md' => 'chairman',
        ];

        $targetRole = $escalationMap[$user->role->name] ?? null;

        if (!$targetRole) {
            return null;
        }

        // First try to find in same department
        if ($user->department_id) {
            $target = User::whereHas('role', function ($query) use ($targetRole) {
                $query->where('name', $targetRole);
            })
            ->where('department_id', $user->department_id)
            ->where('is_active', true)
            ->first();

            if ($target) {
                return $target;
            }
        }

        // Otherwise find any user with target role
        return User::whereHas('role', function ($query) use ($targetRole) {
            $query->where('name', $targetRole);
        })
        ->where('is_active', true)
        ->first();
    }

    public function checkForOverdueApprovals()
    {
        // This would be called via scheduled task
        // Check transactions pending > 24 hours
        $this->checkOverdueTransactions();
        
        // Check production records pending > 24 hours
        $this->checkOverdueProduction();
        
        // Check processes overdue
        $this->checkOverdueProcesses();
    }

    private function checkOverdueTransactions()
    {
        $overdueTransactions = \App\Models\Transaction::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->whereDoesntHave('escalations', function ($query) {
                $query->where('created_at', '>=', now()->subHours(24));
            })
            ->get();

        foreach ($overdueTransactions as $transaction) {
            $this->escalate(
                $transaction,
                $transaction->creator,
                'Transaction pending approval for over 24 hours'
            );
        }
    }

    private function checkOverdueProduction()
    {
        $overdueRecords = \App\Models\ProductionRecord::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        foreach ($overdueRecords as $record) {
            $this->escalate(
                $record,
                $record->creator,
                'Production record pending approval for over 24 hours'
            );
        }
    }

    private function checkOverdueProcesses()
    {
        $overdueProcesses = \App\Models\Process::where('status', '!=', 'completed')
            ->where('due_date', '<', now())
            ->get();

        foreach ($overdueProcesses as $process) {
            $this->escalate(
                $process,
                $process->assignedUser,
                'Process is overdue'
            );
        }
    }
}
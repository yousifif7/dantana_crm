<?php

namespace App\Traits;

use App\Models\Escalation;

trait HasEscalations
{
    public function escalations()
    {
        return $this->morphMany(Escalation::class, 'escalatable');
    }

    public function hasActiveEscalation(): bool
    {
        return $this->escalations()
            ->where('status', 'pending')
            ->exists();
    }

    public function latestEscalation()
    {
        return $this->escalations()
            ->latest()
            ->first();
    }
}


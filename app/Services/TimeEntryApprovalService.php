<?php

namespace App\Services;

use App\Enums\TimeEntryStatus;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use LogicException;

class TimeEntryApprovalService
{
    public function submit( TimeEntry $entry, User $actor, ): TimeEntry
    {
        Gate::forUser($actor)->authorize('submit', $entry);

        $this->transition( $entry, TimeEntryStatus::Submitted);
        return $entry->fresh();
    }

    public function approve( TimeEntry $entry, User $actor, ): TimeEntry
    {
        Gate::forUser($actor)->authorize('approve', $entry);

        $this->transition( $entry, TimeEntryStatus::Approved);

        return $entry->fresh();
    }

    public function reject( TimeEntry $entry, User $actor, ?string $reason = null ): TimeEntry
    {
        Gate::forUser($actor)->authorize('reject', $entry);

        $this->transition( $entry, TimeEntryStatus::Rejected, [ 'rejection_reason' => $reason ] );

        return $entry->fresh();
    }

    private function transition( TimeEntry $entry, TimeEntryStatus $to, array $attributes = [] ): void
    {
        if (! $entry->status->canTransitionTo($to)) {
            throw new LogicException( "Invalid transition from {$entry->status->value} to {$to->value}." );
        }

        $entry->update([ 'status' => $to, ...$attributes ]);
    }
}

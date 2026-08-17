<?php

namespace App\Policies;

use App\Enums\TimeEntryStatus;
use App\Models\TimeEntry;
use App\Models\User;

class TimeEntryPolicy
{
    public function view(User $user, TimeEntry $timeEntry): bool
    {
        return $user->id === $timeEntry->user_id
            || $user->can('view all time entries');
    }
    public function create(User $user): bool
    {
        return $user->can('manage own time entries');
    }
    public function update(User $user, TimeEntry $timeEntry): bool
    {
        return $user->id === $timeEntry->user_id
            && $user->can('manage own time entries')
            && in_array(
                $timeEntry->status,
                [
                    TimeEntryStatus::Draft,
                    TimeEntryStatus::Rejected,
                ],
            );
    }
    public function approve(User $user, TimeEntry $timeEntry): bool
    {
        return $user->can('approve time entries');
    }
}

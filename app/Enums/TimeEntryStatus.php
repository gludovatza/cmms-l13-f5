<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TimeEntryStatus: string implements HasColor, HasLabel
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => __('fields.time_entry_statuses.draft'),
            self::Submitted => __('fields.time_entry_statuses.submitted'),
            self::Approved => __('fields.time_entry_statuses.approved'),
            self::Rejected => __('fields.time_entry_statuses.rejected'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Submitted => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }

    /**
     * A jóváhagyott bejegyzések zárolva vannak, és csak egy adminisztrátor nyithatja újra őket javítás céljából.
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this === self::Approved;
    }

    /**
     * @return array<int, self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft => [self::Submitted],
            self::Submitted => [self::Approved, self::Rejected],
            self::Rejected => [self::Draft, self::Submitted],
            self::Approved => [],
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return in_array($status, $this->allowedTransitions(), strict: true);
    }
}

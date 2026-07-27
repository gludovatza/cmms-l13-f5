<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum WorksheetPriority: string implements HasLabel, HasColor, HasIcon
{
    case NORMAL = 'normal';
    case SURGOS = 'urgent';
    case LEALLASKOR = 'downtime';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NORMAL => __('fields.priority_levels.normal'),
            self::SURGOS => __('fields.priority_levels.urgent'),
            self::LEALLASKOR => __('fields.priority_levels.downtime'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NORMAL => 'warning',
            self::SURGOS => 'danger',
            self::LEALLASKOR => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::NORMAL => 'heroicon-m-exclamation-circle',
            self::SURGOS => 'heroicon-m-exclamation-triangle',
            self::LEALLASKOR => 'heroicon-m-sun',
        };
    }

    public function getChartColor(): string
    {
        return match ($this) {
            self::NORMAL => 'orange',
            self::SURGOS => 'red',
            self::LEALLASKOR => 'gray',
        };
    }
}

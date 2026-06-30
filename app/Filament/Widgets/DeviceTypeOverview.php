<?php

namespace App\Filament\Widgets;

use App\Models\DeviceType;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DeviceTypeOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('DeviceType', DeviceType::query()->count())->label(__('module_names.device_types.plural_label')),
        ];
    }
}

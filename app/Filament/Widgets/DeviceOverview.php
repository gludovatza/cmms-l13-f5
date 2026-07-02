<?php

namespace App\Filament\Widgets;

use App\Models\Device;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DeviceOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Device', Device::query()->count())->label(__('module_names.devices.plural_label') . ': ' . __('fields.all')),
            Stat::make('Device', Device::query()->where('active', true)->count())->label(__('module_names.devices.plural_label') . ': ' . __('fields.active')),
            Stat::make('Device', Device::query()->where('active', false)->count())->label(__('module_names.devices.plural_label') . ': ' . __('fields.inactive')),
        ];
    }
}

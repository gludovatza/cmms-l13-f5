<?php

namespace App\Filament\Resources\Devices\Pages;

use App\Filament\Resources\Devices\DeviceResource;
use App\Models\Device;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDevices extends ListRecords
{
    protected static string $resource = DeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()->label(__('fields.all'))
                ->icon('heroicon-o-list-bullet')
                ->badge(static fn (): int => Device::query()->count()),
            'active' => Tab::make()->label(__('fields.active'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('active', true))
                ->icon('heroicon-o-check-circle')
                ->badge(static fn (): int => Device::query()->where('active', true)->count())
                ->badgeColor('success'),
            'inactive' => Tab::make()->label(__('fields.inactive'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('active', false))
                ->icon('heroicon-o-x-circle')
                ->badge(static fn (): int => Device::query()->where('active', false)->count())
                ->badgeColor('danger'),
        ];
    }

    public ?string $activeTab = 'active';
}

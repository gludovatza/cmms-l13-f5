<?php

namespace App\Filament\Resources\TimeEntries\Pages;

use App\Filament\Resources\TimeEntries\TimeEntryResource;
use App\Models\TimeEntry;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTimeEntries extends ListRecords
{
    protected static string $resource = TimeEntryResource::class;

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
                ->badge(static fn (): int => TimeEntry::query()->count())
                ->badgeColor('gray'),
            'submitted' => Tab::make()->label(__('fields.time_entry_statuses.submitted'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'submitted'))
                ->icon('heroicon-o-arrow-up-circle')
                ->badge(static fn (): int => TimeEntry::query()->where('status', 'submitted')->count())
                ->badgeColor('warning'),
            'rejected' => Tab::make()->label(__('fields.time_entry_statuses.rejected'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'rejected'))
                ->icon('heroicon-o-x-circle')
                ->badge(static fn (): int => TimeEntry::query()->where('status', 'rejected')->count())
                ->badgeColor('danger'),
            'approved' => Tab::make()->label(__('fields.time_entry_statuses.approved'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'approved'))
                ->icon('heroicon-o-check-circle')
                ->badge(static fn (): int => TimeEntry::query()->where('status', 'approved')->count())
                ->badgeColor('success'),
        ];
    }

    public ?string $activeTab = 'submitted';
}

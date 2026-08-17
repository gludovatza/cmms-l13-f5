<?php

namespace App\Filament\Resources\Worksheets\Pages;

use App\Models\Worksheet;
use App\Enums\WorksheetPriority;
use App\Filament\Resources\Worksheets\WorksheetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListWorksheets extends ListRecords
{
    protected static string $resource = WorksheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make()->label(__('fields.all'))
                ->icon('heroicon-o-list-bullet')
                ->badge(WorksheetResource::getEloquentQuery()->count('*')),
        ];

        $priorities = array_column(WorksheetPriority::cases(), 'value');

        foreach ($priorities as $priority) {
            $tabs[$priority] = Tab::make()->label($priority)
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->where('priority', $priority)
                )
                ->label(WorksheetPriority::tryFrom($priority)?->getLabel())
                ->badge(
                    WorksheetResource::getEloquentQuery()
                        ->where('priority', $priority)
                        ->count('*')
                )
                ->badgeColor(WorksheetPriority::tryFrom($priority)?->getColor())
                ->icon(WorksheetPriority::tryFrom($priority)?->getIcon());
        }
        return $tabs;
    }
}

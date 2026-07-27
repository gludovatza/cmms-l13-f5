<?php

namespace App\Filament\Widgets;

use App\Enums\WorksheetPriority;
use App\Models\Worksheet;
use Filament\Widgets\ChartWidget;

class WorksheetsByPriority extends ChartWidget
{
    protected ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        return auth()->user()->can('update worksheets', Worksheet::class);
    }

    public function getHeading(): ?string
    {
        return __('module_names.widgets.worksheetsbypriority');
    }

    protected function getData(): array
    {
        $counts = Worksheet::query()
            ->selectRaw('priority, count(*) as aggregate')
            ->groupBy('priority')
            ->pluck('aggregate', 'priority');

        return [
            'datasets' => [[
                'label' => __('module_names.widgets.worksheetsbypriority'),
                'data' => array_map(fn(WorksheetPriority $priority): int => (int) $counts->get($priority->value, 0), WorksheetPriority::cases()),
                'backgroundColor' => array_map(fn(WorksheetPriority $priority): string => $priority->getChartColor(), WorksheetPriority::cases()),
            ]],
            'labels' => array_map(fn(WorksheetPriority $priority): string => $priority->getLabel(), WorksheetPriority::cases()),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => __('module_names.worksheets.plural_label') . ' (' . Worksheet::count('*') . ')',
                ]
            ]
        ];
    }
}

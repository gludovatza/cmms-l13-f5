<?php

namespace App\Filament\Exports;

use App\Enums\WorksheetPriority;
use App\Models\Worksheet;
use Carbon\CarbonInterface;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class WorksheetExporter extends Exporter
{
    protected static ?string $model = Worksheet::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->visible(auth()->user()->hasRole('admin')),
            ExportColumn::make('priority')
                ->formatStateUsing(fn(WorksheetPriority $state): string => $state->getLabel())
                ->label(__('fields.priority')),
            ExportColumn::make('description')->label(__('fields.description')),
            ExportColumn::make('due_date')->label(__('fields.due_date')),
            ExportColumn::make('finish_date')->label(__('fields.finish_date')),
            ExportColumn::make('device.name')->label(__('module_names.devices.label')),
            ExportColumn::make('creator.name')->label(__('fields.creator'))
                ->visible(fn() => auth()->user()->can('update worksheets')),
            ExportColumn::make('repairer.name')->label(__('fields.repairer')),
            ExportColumn::make('comment')->label(__('fields.note')),
            ExportColumn::make('created_at')->label(__('fields.created_at'))->formatStateUsing(
                fn(?CarbonInterface $state): ?string =>
                $state?->format('Y-m-d H:i'),
            ),
            ExportColumn::make('updated_at')->label(__('fields.updated_at'))->formatStateUsing(
                fn(?CarbonInterface $state): ?string =>
                $state?->format('Y-m-d H:i'),
            ),
        ];
    }

    // public static function getCompletedNotificationBody(Export $export): string
    // {
    //     $body = 'Your worksheet export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

    //     if ($failedRowsCount = $export->getFailedRowsCount()) {
    //         $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
    //     }

    //     return $body;
    // }
    public static function getCompletedNotificationBody(Export $export): string {
        $body = __('notifications.export_success',
            [
                'count' => number_format($export->successful_rows),
            ],
        );

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . __('notifications.export_failed',
                [
                    'count' => number_format($failedRowsCount),
                ],
            );
        }

        return $body;
    }
}

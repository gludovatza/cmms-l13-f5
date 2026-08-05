<?php

namespace App\Filament\Imports;

use App\Enums\WorksheetPriority;
use App\Models\Device;
use App\Models\User;
use App\Models\Worksheet;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Number;
use Illuminate\Validation\Rule;

class WorksheetImporter extends Importer
{
    protected static ?string $model = Worksheet::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('priority')->label(__('fields.priority'))
                ->requiredMapping()
                ->rules(['required'])
                ->castStateUsing(
                    fn (?string $state): string =>
                        mb_strtolower(trim($state ?? 'normal')),
                )
                ->rules([
                    'required',
                    Rule::enum(WorksheetPriority::class),
                ])
                ->exampleHeader('priority')
                ->example('normal'),
            ImportColumn::make('description')->label(__('fields.description'))
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->exampleHeader('description')
                ->example('Leaking pipe'),
            ImportColumn::make('due_date')->label(__('fields.due_date'))
                ->rules([
                    'nullable',
                    'date_format:Y-m-d',
                ])
                ->exampleHeader('due_date')
                ->example('2023-12-31'),
            ImportColumn::make('finish_date')->label(__('fields.finish_date'))
                ->rules([
                    'nullable',
                    'date_format:Y-m-d',
                ])
                ->exampleHeader('finish_date')
                ->example('2023-12-31'),
            ImportColumn::make('device')->label(__('module_names.devices.label'))
                ->requiredMapping()
                ->relationship(
                    resolveUsing: fn (string $state): ?Device =>
                        Device::query()
                            ->where('name', trim($state))
                            ->first(),
                )
                ->rules(['required'])
                ->exampleHeader('device')
                ->example('Siemens'),
            ImportColumn::make('creator')->label(__('fields.creator'))
                ->requiredMapping()
                ->relationship(
                    resolveUsing: fn (string $state): ?User =>
                        User::query()
                            ->where('email', trim($state))
                            ->first(),
                )
                ->rules(['required'])
                ->exampleHeader('creator')
                ->example('operator@admin.hu'),
            ImportColumn::make('repairer')->label(__('fields.repairer'))
                ->relationship(
                    resolveUsing: fn (?string $state): ?User =>
                        filled($state)
                            ? User::query()
                                ->role('repairer')
                                ->where('email', trim($state))
                                ->first()
                            : null
                )
                ->exampleHeader('repairer')
                ->example('repairer@admin.hu'),
            ImportColumn::make('comment')->label(__('fields.note'))
                ->exampleHeader('comment')
                ->example('Additional notes about the worksheet'),
        ];
    }

    public function resolveRecord(): Worksheet
    {
        return new Worksheet();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your worksheet import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    protected function beforeCreate(): void
    {
        Gate::forUser($this->import->user)
            ->authorize('create', Worksheet::class);

        $this->record->attachments ??= [];
        // $this->record->creator_id = $this->import->user_id;
    }
}

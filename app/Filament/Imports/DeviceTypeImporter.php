<?php

namespace App\Filament\Imports;

use App\Models\DeviceType;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Gate;

class DeviceTypeImporter extends Importer
{
    protected static ?string $model = DeviceType::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->label(__('fields.name'))
                ->requiredMapping()
                ->castStateUsing(
                    fn(?string $state): string => trim($state ?? ''),
                )
                ->rules(['required', 'string', 'max:255'])
                ->exampleHeader('name')
                ->examples(['Kompresszor', 'Szivattyú', 'Esztergagép']),
            ImportColumn::make('note')->label(__('fields.note'))
                ->rules(['nullable', 'string', 'max:255'])
                ->exampleHeader('note')
                ->examples(['Központi sűrített levegős rendszer', 'Megjegyzés', '']),
        ];
    }

    public function resolveRecord(): DeviceType
    {
        return DeviceType::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = __(
            'notifications.import_success',
            [
                'count' => number_format($import->successful_rows),
            ],
        );

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . __(
                'notifications.import_failed',
                [
                    'count' => number_format($failedRowsCount),
                ],
            );
        }

        return $body;
    }

    protected function beforeCreate(): void
    {
        Gate::forUser($this->import->user)
            ->authorize('create', DeviceType::class);
    }

    protected function beforeUpdate(): void
    {
        Gate::forUser($this->import->user)
            ->authorize('update', $this->record);
    }
}

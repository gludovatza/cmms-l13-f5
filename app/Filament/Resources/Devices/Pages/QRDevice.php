<?php

namespace App\Filament\Resources\Devices\Pages;

use App\Filament\Resources\Devices\DeviceResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class QRDevice extends Page
{
    use InteractsWithRecord;

    protected static string $resource = DeviceResource::class;

    protected string $view = 'filament.resources.devices.pages.q-r-device';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
}

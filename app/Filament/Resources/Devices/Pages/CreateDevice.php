<?php

namespace App\Filament\Resources\Devices\Pages;

use App\Filament\Resources\DeviceTypes\DeviceTypeResource;
use App\Filament\Resources\Devices\DeviceResource;
use App\Models\DeviceType;
use Filament\Resources\Pages\CreateRecord;

class CreateDevice extends CreateRecord
{
    protected static string $resource = DeviceResource::class;

    protected function getRedirectUrl(): string
    {
        $deviceTypeId = request()->query('device_type_id') ?: $this->record?->type_id;

        if ($deviceTypeId !== null) {
            $deviceType = DeviceType::find($deviceTypeId);

            if ($deviceType !== null) {
                return DeviceTypeResource::getUrl('edit', ['record' => $deviceType]);
            }
        }

        return $this->getResource()::getUrl('index');
    }
}

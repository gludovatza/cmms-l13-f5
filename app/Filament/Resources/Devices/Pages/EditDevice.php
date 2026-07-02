<?php

namespace App\Filament\Resources\Devices\Pages;

use App\Filament\Resources\Devices\DeviceResource;
use App\Filament\Resources\DeviceTypes\DeviceTypeResource;
use App\Models\DeviceType;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDevice extends EditRecord
{
    protected static string $resource = DeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

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

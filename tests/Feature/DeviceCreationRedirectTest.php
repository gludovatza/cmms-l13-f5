<?php

use App\Filament\Resources\DeviceTypes\DeviceTypeResource;
use App\Filament\Resources\Devices\Pages\CreateDevice;
use App\Models\DeviceType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

it('redirects to the related device type edit page when a device type id is provided', function (): void {
    $deviceType = DeviceType::create([
        'name' => 'Test Type',
    ]);

    $request = Request::create('/test', 'GET', ['device_type_id' => $deviceType->getKey()]);
    app()->instance('request', $request);

    $page = new CreateDevice();
    $method = new ReflectionMethod(CreateDevice::class, 'getRedirectUrl');
    $method->setAccessible(true);

    expect($method->invoke($page))->toBe(
        DeviceTypeResource::getUrl('edit', ['record' => $deviceType])
    );
});

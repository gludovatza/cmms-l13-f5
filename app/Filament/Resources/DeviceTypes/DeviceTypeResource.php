<?php

namespace App\Filament\Resources\DeviceTypes;

use App\Filament\Resources\DeviceTypes\Pages\CreateDeviceType;
use App\Filament\Resources\DeviceTypes\Pages\EditDeviceType;
use App\Filament\Resources\DeviceTypes\Pages\ListDeviceTypes;
use App\Filament\Resources\DeviceTypes\Schemas\DeviceTypeForm;
use App\Filament\Resources\DeviceTypes\Tables\DeviceTypesTable;
use App\Models\DeviceType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeviceTypeResource extends Resource
{
    protected static ?string $model = DeviceType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrench;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string
    {
        return __('module_names.navigation_groups.administration');
    }

    public static function getModelLabel(): string
    {
        return __('module_names.device_types.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('module_names.device_types.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return DeviceTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeviceTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeviceTypes::route('/'),
            'create' => CreateDeviceType::route('/create'),
            'edit' => EditDeviceType::route('/{record}/edit'),
        ];
    }
}

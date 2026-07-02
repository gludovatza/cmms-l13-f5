<?php

namespace App\Filament\Resources\DeviceTypes\RelationManagers;

use App\Filament\Resources\Devices\DeviceResource;
use App\Filament\Resources\DeviceTypes\DeviceTypeResource;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class DevicesRelationManager extends RelationManager
{
    protected static string $relationship = 'devices';

    protected static ?string $relatedResource = DeviceTypeResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label(__('fields.name'))
                    ->searchable()->sortable(),
                TextColumn::make('erp_code')->label(__('fields.erp_code'))
                    ->searchable()->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->url(fn(): string
                    => DeviceResource::getUrl('create')),
            ])
            ->recordActions([
                ViewAction::make()->url(fn(Model $record): string
                    => DeviceResource::getUrl('view', ['record' => $record])),
                EditAction::make()->url(fn(Model $record): string
                    => DeviceResource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([])
            ->emptyStateActions([
                CreateAction::make()->url(fn(): string
                    => DeviceResource::getUrl('create')),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('module_names.devices.plural_label');
    }

    public static function getModelLabel(): string
    {
        return __('module_names.devices.label');
    }
}

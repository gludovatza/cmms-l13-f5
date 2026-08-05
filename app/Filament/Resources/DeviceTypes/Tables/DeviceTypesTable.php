<?php

namespace App\Filament\Resources\DeviceTypes\Tables;

use App\Filament\Imports\DeviceTypeImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeviceTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('name')->label(__('fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('note')->label(__('fields.note'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')->label(__('fields.created_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label(__('fields.updated_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->icon('heroicon-o-arrow-up-tray')
                    ->importer(DeviceTypeImporter::class)
                    ->csvDelimiter(';')
                    ->authorize('import'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

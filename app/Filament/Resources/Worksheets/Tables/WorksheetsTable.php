<?php

namespace App\Filament\Resources\Worksheets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class WorksheetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->sortable()
                    ->visible(auth()->user()->hasRole('admin')), // csak az adminoknak látható, a többi felhasználó számára rejtett
                TextColumn::make('created_at')->label(__('fields.created_at'))
                    ->dateTime('Y-m-d H:i')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('device.name')->label(__('module_names.devices.label'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')->label(__('fields.description'))
                    ->limit(30)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priority')->label(__('fields.priority'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('creator.name')->label(__('fields.creator'))
                    ->searchable()
                    ->sortable()
                    ->visible(auth()->user()->can('update worksheets'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('repairer.name')->label(__('fields.repairer'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('due_date')->label(__('fields.due_date'))
                    ->date('Y-m-d')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('finish_date')->label(__('fields.finish_date'))
                    ->date('Y-m-d')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label(__('fields.updated_at'))
                    ->dateTime('Y-m-d H:i')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->groups([
                Group::make('priority')->label(__('fields.priority'))
                    ->collapsible(),
                Group::make('creator.name')->label(__('fields.creator')),
                Group::make('repairer.name')->label(__('fields.repairer')),
                Group::make('created_at')->label(__('fields.created_at'))
                    ->date()
            ])
            ->defaultGroup('priority')
            ->defaultSort('created_at', 'desc');
    }
}

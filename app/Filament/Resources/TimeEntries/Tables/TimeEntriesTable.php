<?php

namespace App\Filament\Resources\TimeEntries\Tables;

use App\Enums\TimeEntryStatus;
use App\Models\TimeEntry;
use App\Services\TimeEntryApprovalService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TimeEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('worksheet.description')->label(__('module_names.worksheets.label') .' - '. __('fields.description'))
                    ->searchable(),
                TextColumn::make('user.name')->label(__('fields.repairer'))
                    ->searchable(),
                TextColumn::make('work_date')->label(__('fields.work_date'))
                    ->datetime('Y-m-d')
                    ->sortable(),
                TextColumn::make('hours')->label(__('fields.hours'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')->label(__('fields.status'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('created_at')->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label(__('fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('approve')->label(__('actions.approve'))
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible( fn (TimeEntry $record): bool =>
                        $record->status === TimeEntryStatus::Submitted
                    )
                    ->authorize('approve')
                    ->requiresConfirmation()
                    // ->action(function (TimeEntry $record): void {
                    //     if (! $record->status->canTransitionTo( TimeEntryStatus::Approved )) {
                    //         return;
                    //     }
                    //     $record->update([ 'status' => TimeEntryStatus::Approved ]);
                    // }),
                    ->action( fn ( TimeEntry $record, TimeEntryApprovalService $service ) =>
                        $service->approve( $record, auth()->user() )
                    ),
                Action::make('reject')->label(__('actions.reject'))
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible( fn (TimeEntry $record): bool => $record->status === TimeEntryStatus::Submitted )
                    ->authorize('reject')
                    ->requiresConfirmation()
                    // ->action(function (TimeEntry $record): void {
                    //     if (! $record->status->canTransitionTo( TimeEntryStatus::Rejected )) {
                    //         return;
                    //     }
                    //     $record->update([ 'status' => TimeEntryStatus::Rejected ]);
                    // })
                    ->schema([
                        Textarea::make('rejection_reason')->label(__('fields.rejection_reason'))
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function ( TimeEntry $record, array $data, ): void {
                        $record->update([ 'status' => TimeEntryStatus::Rejected, 'rejection_reason' => $data['rejection_reason'] ]);
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('status');
    }
}

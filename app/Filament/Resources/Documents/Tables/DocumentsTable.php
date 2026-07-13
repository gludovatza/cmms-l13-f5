<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Models\Document;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->searchable()->sortable(),
                TextColumn::make('name')->label(__('fields.name'))
                    ->searchable()->sortable(),
                TextColumn::make('device.name')->label(__('module_names.devices.label'))
                    ->searchable()->sortable(),
                TextColumn::make('created_at')->label(__('fields.created_at'))
                    ->dateTime('Y-m-d H:i')
                    ->searchable()->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                // Action::make('download')
                //     ->label(__('actions.download'))
                //     ->action(function ($record) {
                //         if (Storage::exists($record->attachment)) {
                //             return Storage::download($record->attachment);
                //         } else {
                //             throw new \Exception('File not found.');
                //         }
                //     })
                //     ->icon('heroicon-o-document-arrow-down')
                //     ->color('primary')
                //     // ->authorize(fn (Document $record): bool =>
                //     //     auth()->user()->can('download', $record)
                //     // )
                //     ->authorize('download'),
                Action::make('download')
                    ->label(__('actions.download'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->url(fn (Document $record): string =>
                        route('documents.download', $record)
                    )
                    ->authorize('download'),
                Action::make('QR')->label(__('fields.qr_code'))
                    ->modalContent(fn($record): View => view('filament.resources.documents.pages.q-r-document', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->icon('heroicon-o-qr-code')
                    ->color('secondary')
                    ->tooltip(__('actions.print') . ': ' . __('fields.qr_code'))
                    ->authorize('qrCode'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

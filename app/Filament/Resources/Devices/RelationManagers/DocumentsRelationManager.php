<?php

namespace App\Filament\Resources\Devices\RelationManagers;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $relatedResource = DocumentResource::class;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('module_names.documents.plural_label');
    }
    public static function getModelLabel(): string
    {
        return __('module_names.documents.label');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('fields.name'))
                    ->searchable()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->url(fn(): string => DocumentResource::getUrl('create')),
            ])
            ->recordActions([
                ViewAction::make()->url(fn(Model $record): string => DocumentResource::getUrl('view', ['record' => $record])),
                EditAction::make()->url(fn(Model $record): string => DocumentResource::getUrl('edit', ['record' => $record])),
                Action::make('download')
                    ->label(__('actions.download'))
                    ->action(function ($record) {
                        if (Storage::exists($record->attachment)) {
                            return Storage::download($record->attachment);
                        } else {
                            throw new \Exception('File not found.');
                       }
                    })
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary'),
            ]);
    }
}

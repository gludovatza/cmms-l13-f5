<?php

namespace App\Filament\Resources\Worksheets\Schemas;

use App\Enums\WorksheetPriority;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\FileUpload;

class WorksheetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            Section::make(__('module_names.sections.users'))
                ->columns(2)
                ->icon('heroicon-o-user')
                ->schema([
                    Select::make('creator_id')->label(__('fields.creator'))
                        ->relationship('creator', 'name')
                        ->default( ! auth()->user()->can('update worksheets') ? auth()->id() : null)
                        ->disabled(fn ($operation) => ! auth()->user()->can('update worksheets') || $operation === 'edit')
                        ->required(),
                    Select::make('repairer_id')->label(__('fields.repairer'))
                        ->relationship('repairer', 'name', fn (Builder $query) => $query->role('repairer'))
                        ->disabled( ! auth()->user()->can('update worksheets'))
                        ->required(fn ($operation) => $operation === 'edit')
                        ->afterStateHydrated(function (Select $component, $state) {
                            if ($state === null) {
                                $component->state(auth()->id());
                            }
                        }),
                        // ->default(fn ($operation) => $operation === 'edit' ? auth()->user()->id() : null),
            ]),
            Section::make(__('module_names.sections.dates'))
                ->columns(2)
                ->icon('heroicon-o-calendar')
                ->schema([
                    DatePicker::make('due_date')->label(__('fields.due_date'))
                        ->minDate(now()->toDateString())
                        ->visible(auth()->user()->can('update worksheets')),
                    DatePicker::make('finish_date')->label(__('fields.finish_date'))
                        ->minDate(now()->toDateString())
                        ->default(fn() => (auth()->user()->can('update worksheets')) ? now()->toDateString() : null)
                        ->disabled( ! auth()->user()->can('update worksheets')),
            ]),
            Section::make(__('module_names.devices.label'))
                ->columns(2)
                ->icon('heroicon-o-wrench-screwdriver')
                ->schema([
                    Select::make('device_id')->label(__('module_names.devices.label'))
                        ->relationship('device', 'name')
                        ->required(),
                    Select::make('priority')->label(__('fields.priority'))
                        ->options(WorksheetPriority::class)
                        ->default('normal')
                        ->required(),
                    Textarea::make('description')->label(__('fields.description'))
                        ->required()
                        ->maxLength(65535)
                        ->columnSpanFull(),
                    FileUpload::make('attachments')->label(__('fields.attachments'))
                        ->required()
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatioOptions([
                            null,
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->imageEditorEmptyFillColor('#000000')
                        ->imageEditorViewportWidth('1920')
                        ->imageEditorViewportHeight('1080')
                        ->multiple()
                        ->preserveFilenames()
                        ->openable()
                        ->deletable()
                        ->downloadable()
                        ->columnSpanFull(),
                    TextInput::make('comment')->label(__('fields.note'))
                        ->columnSpanFull(),
                ])->columnSpanFull(),
            ]);
    }
}

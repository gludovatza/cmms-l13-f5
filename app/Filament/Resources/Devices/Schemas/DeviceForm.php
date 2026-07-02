<?php

namespace App\Filament\Resources\Devices\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeviceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('name')->label(__('fields.name'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    TextInput::make('erp_code')->label(__('fields.erp_code'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Select::make('type_id')->label(__('fields.type'))
                        ->relationship('type', 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('name')->label(__('fields.name'))
                                ->required()
                                ->unique()
                                ->maxLength(255)
                        ])
                        ->required(),
                    TextInput::make('plant')->label(__('fields.plant'))
                        ->required()
                        ->maxLength(255),
                    Toggle::make('active')->label(__('fields.active'))
                        ->onColor('success')
                        ->offColor('danger')
                        ->columnSpan('full'),
                    TextInput::make('history')->label(__('fields.history'))
                        ->maxLength(255),
                    TextInput::make('note')->label(__('fields.note'))
                        ->maxLength(255),
                ])->columnSpanFull()
            ]);
    }
}

<?php

namespace App\Filament\Resources\DeviceTypes\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeviceTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')->label(__('fields.name'))
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        Textarea::make('note')->label(__('fields.note'))
                            ->maxLength(255),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

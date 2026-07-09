<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')->label(__('fields.name'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Select::make('device_id')->label(__('module_names.devices.label'))
                            ->relationship('device', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        FileUpload::make('attachment')->label(__('fields.attachment'))
                            ->required()
                            ->preserveFilenames()
                            ->openable()
                            ->downloadable()
                            ->maxSize(20000),
                    ])
                    ->columnSpanFull()
                    ->columns(2)
            ]);
    }
}

<?php

namespace App\Filament\Resources\TimeEntries\Schemas;

use App\Enums\TimeEntryStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TimeEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('worksheet_id')
                    ->relationship('worksheet', 'id')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                DatePicker::make('work_date')
                    ->required(),
                TextInput::make('hours')
                    ->required()
                    ->numeric(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(TimeEntryStatus::class)
                    ->default('draft')
                    ->required(),
            ]);
    }
}

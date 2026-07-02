<?php

namespace App\Filament\Resources\Devices\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeviceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextEntry::make('created_at')->label(__('fields.created_at'))
                        ->dateTime('Y-m-d H:i')
                        ->placeholder('-'),
                    TextEntry::make('updated_at')->label(__('fields.updated_at'))
                        ->dateTime('Y-m-d H:i')
                        ->placeholder('-'),
                    TextEntry::make('name')->label(__('fields.name')),
                    TextEntry::make('erp_code')->label(__('fields.erp_code')),
                    TextEntry::make('type.name')->label(__('fields.type')),
                    TextEntry::make('plant')->label(__('fields.plant')),
                    IconEntry::make('active')->label(__('fields.active'))
                        ->boolean(),
                    TextEntry::make('history')->label(__('fields.history')),
                    TextEntry::make('notes')->label(__('fields.note')),
                ])
                ->columns(['md' => 2, 'xxl' => 4])
                ->columnSpanFull()
            ]);
    }
}

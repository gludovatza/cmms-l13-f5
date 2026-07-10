<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
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
                        Select::make('permissions')->label(__('module_names.permissions.plural_label'))
                            ->relationship('permissions', 'name')
                            ->multiple()
                            ->preload()
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

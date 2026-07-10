<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')->label(__('fields.name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')->label(__('fields.email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->password()
                            ->maxLength(255)
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->dehydrateStateUsing(
                                fn(?string $state): ?string =>
                                filled($state) ? Hash::make($state) : null
                            )
                            ->dehydrated(
                                fn(?string $state): bool =>
                                filled($state)
                            )
                            ->label(
                                fn(string $operation): string => ($operation === 'create') ?
                                    __('fields.password') : __('fields.new_password')
                            ),
                        CheckboxList::make('roles')->label(__('module_names.roles.label'))
                            ->columnSpanFull()
                            ->relationship('roles', 'name')
                            ->columns(3)
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Worksheets;

use App\Filament\Resources\Worksheets\Pages\CreateWorksheet;
use App\Filament\Resources\Worksheets\Pages\EditWorksheet;
use App\Filament\Resources\Worksheets\Pages\ViewWorksheet;
use App\Filament\Resources\Worksheets\Pages\ListWorksheets;
use App\Filament\Resources\Worksheets\Schemas\WorksheetForm;
use App\Filament\Resources\Worksheets\Tables\WorksheetsTable;
use App\Models\Worksheet;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorksheetResource extends Resource
{
    protected static ?string $model = Worksheet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentPlus;

protected static ?int $navigationSort = 7;

public static function getNavigationGroup(): string
{
    return __('module_names.navigation_groups.failure_report');
}

public static function getModelLabel(): string
{
    return __('module_names.worksheets.label');
}

public static function getPluralModelLabel(): string
{
    return __('module_names.worksheets.plural_label');
}

    public static function form(Schema $schema): Schema
    {
        return WorksheetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorksheetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorksheets::route('/'),
            'create' => CreateWorksheet::route('/create'),
            'view' => ViewWorksheet::route('/{record}'),
            'edit' => EditWorksheet::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        if ( ! auth()->user()->can('update worksheets')) {
            return parent::getEloquentQuery()->where('creator_id', auth()->id());
        }
        return parent::getEloquentQuery();
    }
}

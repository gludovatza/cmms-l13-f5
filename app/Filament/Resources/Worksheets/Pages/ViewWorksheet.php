<?php

namespace App\Filament\Resources\Worksheets\Pages;

use App\Filament\Resources\Worksheets\WorksheetResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWorksheet extends ViewRecord
{
    protected static string $resource = WorksheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

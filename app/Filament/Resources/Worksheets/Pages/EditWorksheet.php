<?php

namespace App\Filament\Resources\Worksheets\Pages;

use App\Filament\Resources\Worksheets\WorksheetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWorksheet extends EditRecord
{
    protected static string $resource = WorksheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Worksheets\Pages;

use App\Filament\Resources\Worksheets\WorksheetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWorksheet extends CreateRecord
{
    protected static string $resource = WorksheetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ( ! isset($data['creator_id']))
            $data['creator_id'] = auth()->id();
        return $data;
    }
}

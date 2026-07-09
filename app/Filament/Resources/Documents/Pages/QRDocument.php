<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class QRDocument extends Page
{
    use InteractsWithRecord;

    protected static string $resource = DocumentResource::class;

    protected string $view = 'filament.resources.documents.pages.q-r-document';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
}

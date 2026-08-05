<?php

namespace App\Policies;

use App\Models\User;
use Filament\Actions\Imports\Models\Import;

class ImportPolicy
{
    public function view(User $user, Import $import): bool
    {
        return $import->user->can('import');
    }
}

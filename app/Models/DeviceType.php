<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'note'])]
class DeviceType extends Model
{
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'type_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'area', 'description'])]
class WarehouseLocation extends Model
{
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function damages(): HasMany
    {
        return $this->hasMany(Damage::class);
    }
}

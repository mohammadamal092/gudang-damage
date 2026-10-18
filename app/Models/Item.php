<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['sku', 'name', 'category_id', 'warehouse_location_id', 'unit', 'stock', 'min_stock', 'description'])]
class Item extends Model
{
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'warehouse_location_id');
    }

    public function damages(): HasMany
    {
        return $this->hasMany(Damage::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }
}

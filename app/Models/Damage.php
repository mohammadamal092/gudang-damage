<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'ticket_code',
    'item_id',
    'warehouse_location_id',
    'reported_by',
    'quantity',
    'damage_type',
    'status',
    'reported_at',
    'description',
    'photo_path',
    'handling_notes',
])]
class Damage extends Model
{
    public const STATUSES = [
        'dilaporkan' => 'Dilaporkan',
        'diproses' => 'Diproses',
        'diperbaiki' => 'Diperbaiki',
        'diretur' => 'Diretur',
    ];

    public const TYPES = [
        'penyok' => 'Penyok',
        'pecah' => 'Pecah / Patah',
        'basah' => 'Basah / Lembab',
        'kadaluarsa' => 'Kadaluarsa',
        'cacat_produksi' => 'Cacat Produksi',
        'lainnya' => 'Lainnya',
    ];

    protected function casts(): array
    {
        return [
            'reported_at' => 'date',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'warehouse_location_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->damage_type] ?? $this->damage_type;
    }

    public function badgeClass(): string
    {
        return match ($this->status) {
            'dilaporkan' => 'text-bg-warning',
            'diproses' => 'text-bg-info',
            'diperbaiki' => 'text-bg-success',
            'diretur' => 'text-bg-primary',
            'dimusnahkan' => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    }

    public function restoresStock(): bool
    {
        return $this->status === 'diperbaiki';
    }
}

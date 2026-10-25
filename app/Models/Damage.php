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
        'dimusnahkan' => 'Dimusnahkan',
    ];

    public const TYPES = [
        'penyok' => 'Penyok',
        'pecah' => 'Pecah / Patah',
        'basah' => 'Basah / Lembab',
        'kadaluarsa' => 'Kadaluarsa',
        'cacat_produksi' => 'Cacat Produksi',
        'lainnya' => 'Lainnya',
    ];

    /** Status yang dianggap masih perlu ditindaklanjuti. */
    public const OPEN_STATUSES = ['dilaporkan', 'diproses'];

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

    /** Kelas CSS badge "soft" (lihat public/css/gudang.css). */
    public function badgeClass(): string
    {
        return 'badge-soft '.match ($this->status) {
            'dilaporkan' => 'status-dilaporkan',
            'diproses' => 'status-diproses',
            'diperbaiki' => 'status-diperbaiki',
            'diretur' => 'status-diretur',
            'dimusnahkan' => 'status-dimusnahkan',
            default => 'status-default',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this->status, self::OPEN_STATUSES, true);
    }

    /** Berapa hari laporan sudah terbuka (hanya bermakna jika masih open). */
    public function ageInDays(): int
    {
        return (int) $this->reported_at->startOfDay()->diffInDays(now()->startOfDay());
    }

    public function restoresStock(): bool
    {
        return $this->status === 'diperbaiki';
    }
}

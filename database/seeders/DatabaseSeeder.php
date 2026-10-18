<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Damage;
use App\Models\Item;
use App\Models\User;
use App\Models\WarehouseLocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@gudang.test'],
            [
                'name' => 'Administrator Gudang',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'petugas@gudang.test'],
            [
                'name' => 'Petugas Gudang',
                'password' => Hash::make('password'),
                'role' => 'petugas',
            ]
        );

        $categories = collect([
            ['name' => 'Elektronik', 'code' => 'ELK', 'description' => 'Peralatan elektronik gudang'],
            ['name' => 'Kemasan', 'code' => 'KMS', 'description' => 'Kardus, botol, dan kemasan'],
            ['name' => 'Suku Cadang', 'code' => 'SCD', 'description' => 'Spare part mesin dan kendaraan'],
        ])->map(fn ($row) => Category::query()->updateOrCreate(['code' => $row['code']], $row));

        $locations = collect([
            ['name' => 'Rak A1', 'code' => 'A1', 'area' => 'Zona Penerimaan'],
            ['name' => 'Rak B2', 'code' => 'B2', 'area' => 'Zona Penyimpanan'],
            ['name' => 'Area Karantina', 'code' => 'KR1', 'area' => 'Zona Damage'],
        ])->map(fn ($row) => WarehouseLocation::query()->updateOrCreate(['code' => $row['code']], $row));

        $items = [
            [
                'sku' => 'BRG-001',
                'name' => 'Monitor LED 24 Inch',
                'category' => 'ELK',
                'location' => 'A1',
                'unit' => 'unit',
                'stock' => 18,
                'min_stock' => 5,
            ],
            [
                'sku' => 'BRG-002',
                'name' => 'Kardus Packing Medium',
                'category' => 'KMS',
                'location' => 'B2',
                'unit' => 'pcs',
                'stock' => 120,
                'min_stock' => 40,
            ],
            [
                'sku' => 'BRG-003',
                'name' => 'Filter Oli Mesin',
                'category' => 'SCD',
                'location' => 'B2',
                'unit' => 'pcs',
                'stock' => 7,
                'min_stock' => 10,
            ],
            [
                'sku' => 'BRG-004',
                'name' => 'Keyboard Mechanical',
                'category' => 'ELK',
                'location' => 'A1',
                'unit' => 'unit',
                'stock' => 25,
                'min_stock' => 8,
            ],
        ];

        foreach ($items as $row) {
            Item::query()->updateOrCreate(
                ['sku' => $row['sku']],
                [
                    'name' => $row['name'],
                    'category_id' => $categories->firstWhere('code', $row['category'])->id,
                    'warehouse_location_id' => $locations->firstWhere('code', $row['location'])->id,
                    'unit' => $row['unit'],
                    'stock' => $row['stock'],
                    'min_stock' => $row['min_stock'],
                ]
            );
        }

        $monitor = Item::where('sku', 'BRG-001')->first();
        $keyboard = Item::where('sku', 'BRG-004')->first();
        $karantina = $locations->firstWhere('code', 'KR1');

        if ($monitor && ! Damage::where('ticket_code', 'DMG-'.now()->format('Ymd').'-0001')->exists()) {
            Damage::create([
                'ticket_code' => 'DMG-'.now()->format('Ymd').'-0001',
                'item_id' => $monitor->id,
                'warehouse_location_id' => $karantina->id,
                'reported_by' => $admin->id,
                'quantity' => 2,
                'damage_type' => 'penyok',
                'status' => 'dilaporkan',
                'reported_at' => now()->toDateString(),
                'description' => 'Layar penyok pada sudut kiri atas saat unloading.',
            ]);
            $monitor->decrement('stock', 2);
        }

        if ($keyboard && ! Damage::where('ticket_code', 'DMG-'.now()->format('Ymd').'-0002')->exists()) {
            Damage::create([
                'ticket_code' => 'DMG-'.now()->format('Ymd').'-0002',
                'item_id' => $keyboard->id,
                'warehouse_location_id' => $karantina->id,
                'reported_by' => $admin->id,
                'quantity' => 1,
                'damage_type' => 'basah',
                'status' => 'diproses',
                'reported_at' => now()->subDay()->toDateString(),
                'description' => 'Kemasan basah, tombol lengket.',
            ]);
            $keyboard->decrement('stock', 1);
        }
    }
}

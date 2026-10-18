<?php

namespace App\Http\Controllers;

use App\Models\Damage;
use App\Models\Item;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $recentDamages = Damage::with(['item', 'location', 'reporter'])
            ->latest()
            ->limit(8)
            ->get();

        $lowStockItems = Item::with(['category', 'location'])
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->limit(6)
            ->get();

        $statusCounts = Damage::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboard', [
            'stats' => [
                'items' => Item::count(),
                'damages' => Damage::count(),
                'open' => Damage::whereIn('status', ['dilaporkan', 'diproses'])->count(),
                'low_stock' => Item::whereColumn('stock', '<=', 'min_stock')->count(),
            ],
            'statusCounts' => $statusCounts,
            'recentDamages' => $recentDamages,
            'lowStockItems' => $lowStockItems,
        ]);
    }
}

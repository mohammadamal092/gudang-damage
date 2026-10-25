<?php

namespace App\Http\Controllers;

use App\Models\Damage;
use App\Models\Item;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Laporan terbuka lebih dari N hari dianggap perlu ditindaklanjuti. */
    private const OVERDUE_DAYS = 3;

    public function __invoke(): View
    {
        $recentDamages = Damage::with(['item', 'location', 'reporter'])
            ->latest('reported_at')
            ->latest('id')
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

        $typeCounts = Damage::query()
            ->selectRaw('damage_type, SUM(quantity) as total')
            ->groupBy('damage_type')
            ->pluck('total', 'damage_type');

        // Tren 6 bulan terakhir (dihitung di PHP agar aman untuk SQLite maupun MySQL).
        $start = now()->startOfMonth()->subMonths(5);
        $monthly = Damage::where('reported_at', '>=', $start->toDateString())
            ->get(['reported_at', 'quantity'])
            ->groupBy(fn ($d) => $d->reported_at->format('Y-m'))
            ->map(fn ($rows) => $rows->count());

        $trendLabels = [];
        $trendData = [];
        for ($i = 0; $i < 6; $i++) {
            $month = $start->copy()->addMonths($i);
            $trendLabels[] = $month->translatedFormat('M Y');
            $trendData[] = $monthly[$month->format('Y-m')] ?? 0;
        }

        $overdue = Damage::with('item')
            ->whereIn('status', Damage::OPEN_STATUSES)
            ->whereDate('reported_at', '<=', now()->subDays(self::OVERDUE_DAYS)->toDateString())
            ->orderBy('reported_at')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'stats' => [
                'items' => Item::count(),
                'damages' => Damage::count(),
                'open' => Damage::whereIn('status', Damage::OPEN_STATUSES)->count(),
                'low_stock' => Item::whereColumn('stock', '<=', 'min_stock')->count(),
                'units_damaged' => (int) Damage::sum('quantity'),
                'this_month' => Damage::where('reported_at', '>=', now()->startOfMonth()->toDateString())->count(),
            ],
            'statusCounts' => $statusCounts,
            'typeCounts' => $typeCounts,
            'trendLabels' => $trendLabels,
            'trendData' => $trendData,
            'overdue' => $overdue,
            'overdueDays' => self::OVERDUE_DAYS,
            'recentDamages' => $recentDamages,
            'lowStockItems' => $lowStockItems,
        ]);
    }
}

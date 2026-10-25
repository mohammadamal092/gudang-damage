@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan kondisi barang damage — '.now()->translatedFormat('l, d F Y'))

@section('actions')
    <a href="{{ route('damages.create') }}" class="btn btn-accent"><i class="bi bi-plus-lg me-1"></i><span class="d-none d-sm-inline">Catat Damage</span></a>
@endsection

@section('content')
{{-- Kartu statistik --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="stat-icon tone-navy"><i class="bi bi-box-seam"></i></div>
            <div>
                <div class="stat-label">Master Barang</div>
                <div class="stat-value tabular">{{ $stats['items'] }}</div>
                <div class="stat-hint">jenis barang terdaftar</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="stat-icon tone-blue"><i class="bi bi-clipboard2-data"></i></div>
            <div>
                <div class="stat-label">Laporan Damage</div>
                <div class="stat-value tabular">{{ $stats['damages'] }}</div>
                <div class="stat-hint">{{ $stats['this_month'] }} laporan bulan ini · {{ $stats['units_damaged'] }} unit</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="stat-icon tone-orange"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="stat-label">Belum Selesai</div>
                <div class="stat-value tabular">{{ $stats['open'] }}</div>
                <div class="stat-hint">dilaporkan + diproses</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="stat-icon {{ $stats['low_stock'] > 0 ? 'tone-red' : 'tone-green' }}"><i class="bi bi-{{ $stats['low_stock'] > 0 ? 'exclamation-triangle' : 'check2-circle' }}"></i></div>
            <div>
                <div class="stat-label">Stok Menipis</div>
                <div class="stat-value tabular">{{ $stats['low_stock'] }}</div>
                <div class="stat-hint">{{ $stats['low_stock'] > 0 ? 'perlu restock segera' : 'semua stok aman' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Peringatan tindak lanjut --}}
@if ($overdue->isNotEmpty())
    <div class="card p-3 mb-4" style="border-left:4px solid #e0533d">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-bell-fill text-danger"></i>
            <h2 class="card-title-sm">Perlu tindak lanjut — terbuka lebih dari {{ $overdueDays }} hari</h2>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @foreach ($overdue as $d)
                <a href="{{ route('damages.show', $d) }}" class="chip">
                    <i class="bi bi-clock-history"></i>
                    <span class="ticket-code">{{ $d->ticket_code }}</span>
                    <span class="text-muted">{{ \Illuminate\Support\Str::limit($d->item->name, 22) }}</span>
                    <span class="count">{{ $d->ageInDays() }} hari</span>
                </a>
            @endforeach
        </div>
    </div>
@endif

<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="card-title-sm">Tren laporan damage (6 bulan)</h2>
            </div>
            <div style="position:relative;height:260px"><canvas id="trendChart"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h2 class="card-title-sm mb-2">Jenis kerusakan (unit)</h2>
            @if ($typeCounts->sum() > 0)
                <div style="position:relative;height:260px"><canvas id="typeChart"></canvas></div>
            @else
                <div class="empty-state"><i class="bi bi-pie-chart"></i><div class="title">Belum ada data</div></div>
            @endif
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="card-title-sm">Laporan damage terbaru</h2>
                <a href="{{ route('damages.index') }}" class="btn btn-sm btn-soft">Lihat semua <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr><th>Tiket</th><th>Barang</th><th>Status</th><th class="text-end">Qty</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($recentDamages as $damage)
                            <tr>
                                <td>
                                    <a class="ticket-code" href="{{ route('damages.show', $damage) }}">{{ $damage->ticket_code }}</a>
                                    <div class="cell-sub">{{ $damage->reported_at->translatedFormat('d M Y') }}</div>
                                </td>
                                <td>
                                    {{ $damage->item->name }}
                                    <div class="cell-sub">{{ $damage->typeLabel() }} · {{ $damage->location->name }}</div>
                                </td>
                                <td><span class="badge {{ $damage->badgeClass() }}">{{ $damage->statusLabel() }}</span></td>
                                <td class="text-end tabular fw-semibold">{{ $damage->quantity }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><div class="empty-state"><i class="bi bi-inbox"></i><div class="title">Belum ada laporan</div>Catat barang damage pertama Anda.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 mb-3">
            <h2 class="card-title-sm mb-3">Ringkasan status</h2>
            @php $totalDamages = max(1, $statusCounts->sum()); @endphp
            @foreach (\App\Models\Damage::STATUSES as $key => $label)
                @php $n = $statusCounts[$key] ?? 0; @endphp
                <a href="{{ route('damages.index', ['status' => $key]) }}" class="d-block text-reset mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-medium">{{ $label }}</span>
                        <span class="tabular fw-semibold">{{ $n }}</span>
                    </div>
                    <div class="stock-bar"><span style="width:{{ round($n / $totalDamages * 100) }}%;background:var(--gd-navy)"></span></div>
                </a>
            @endforeach
        </div>
        <div class="card p-3">
            <h2 class="card-title-sm mb-3">Stok menipis</h2>
            @forelse ($lowStockItems as $item)
                @php $pct = $item->min_stock > 0 ? min(100, round($item->stock / $item->min_stock * 100)) : 0; @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-medium">{{ $item->name }}</span>
                        <span class="text-danger fw-semibold tabular">{{ $item->stock }} / {{ $item->min_stock }} {{ $item->unit }}</span>
                    </div>
                    <div class="stock-bar low"><span style="width:{{ $pct }}%"></span></div>
                </div>
            @empty
                <div class="empty-state py-4"><i class="bi bi-check2-circle text-success"></i><div class="title">Semua stok aman</div></div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = '"DM Sans", system-ui, sans-serif';
    Chart.defaults.color = '#6b7280';

    new Chart(document.getElementById('trendChart'), {
        type: 'bar',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'Laporan',
                data: @json($trendData),
                backgroundColor: '#f4a261',
                hoverBackgroundColor: '#e08c45',
                borderRadius: 8,
                maxBarThickness: 44,
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f0ebe1' }, border: { display: false } },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });

    @if ($typeCounts->sum() > 0)
    @php
        $typeLabels = $typeCounts->keys()->map(fn ($k) => \App\Models\Damage::TYPES[$k] ?? $k)->values();
    @endphp
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: @json($typeLabels),
            datasets: [{
                data: @json($typeCounts->values()),
                backgroundColor: ['#13253b', '#f4a261', '#2e9d62', '#1d6fb8', '#8e6bd8', '#c0392b'],
                borderWidth: 2, borderColor: '#fff'
            }]
        },
        options: {
            maintainAspectRatio: false, cutout: '62%',
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, padding: 14 } } }
        }
    });
    @endif
</script>
@endpush

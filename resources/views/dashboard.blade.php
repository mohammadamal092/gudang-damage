@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="text-muted small">Master Barang</div>
            <div class="display-6 fw-bold">{{ $stats['items'] }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="text-muted small">Laporan Damage</div>
            <div class="display-6 fw-bold">{{ $stats['damages'] }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="text-muted small">Belum Selesai</div>
            <div class="display-6 fw-bold">{{ $stats['open'] }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="text-muted small">Stok Menipis</div>
            <div class="display-6 fw-bold">{{ $stats['low_stock'] }}</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 mb-0">Laporan damage terbaru</h2>
                <a href="{{ route('damages.create') }}" class="btn btn-sm btn-accent">Catat Damage</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tiket</th>
                            <th>Barang</th>
                            <th>Status</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentDamages as $damage)
                            <tr>
                                <td><a href="{{ route('damages.show', $damage) }}">{{ $damage->ticket_code }}</a></td>
                                <td>{{ $damage->item->name }}</td>
                                <td><span class="badge {{ $damage->badgeClass() }}">{{ $damage->statusLabel() }}</span></td>
                                <td>{{ $damage->quantity }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">Belum ada laporan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 mb-3">
            <h2 class="h6">Ringkasan status</h2>
            @foreach (\App\Models\Damage::STATUSES as $key => $label)
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span>{{ $label }}</span>
                    <strong>{{ $statusCounts[$key] ?? 0 }}</strong>
                </div>
            @endforeach
        </div>
        <div class="card p-3">
            <h2 class="h6">Stok menipis</h2>
            @forelse ($lowStockItems as $item)
                <div class="d-flex justify-content-between py-1">
                    <span>{{ $item->name }}</span>
                    <span class="text-danger fw-semibold">{{ $item->stock }} {{ $item->unit }}</span>
                </div>
            @empty
                <p class="text-muted mb-0">Semua stok aman.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

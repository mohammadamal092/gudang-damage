@extends('layouts.app')
@section('title', 'Barang Damage')
@section('subtitle', 'Daftar laporan barang rusak di gudang')

@section('actions')
    <a href="{{ route('damages.export', request()->query()) }}" class="btn btn-soft"><i class="bi bi-file-earmark-spreadsheet me-1"></i><span class="d-none d-sm-inline">Ekspor CSV</span></a>
    <a href="{{ route('damages.create') }}" class="btn btn-accent"><i class="bi bi-plus-lg me-1"></i><span class="d-none d-sm-inline">Catat Damage</span></a>
@endsection

@section('content')
@php
    $hasFilter = request()->hasAny(['q', 'status', 'type', 'from', 'to']);
    $totalAll = $statusCounts->sum();
@endphp

{{-- Chip status --}}
<div class="d-flex flex-wrap gap-2 mb-3">
    <a href="{{ route('damages.index', request()->except('status', 'page')) }}" class="chip {{ ! request('status') ? 'active' : '' }}">Semua <span class="count">{{ $totalAll }}</span></a>
    @foreach (\App\Models\Damage::STATUSES as $key => $label)
        <a href="{{ route('damages.index', array_merge(request()->except('page'), ['status' => $key])) }}" class="chip {{ request('status') === $key ? 'active' : '' }}">
            {{ $label }} <span class="count">{{ $statusCounts[$key] ?? 0 }}</span>
        </a>
    @endforeach
</div>

<div class="card p-3">
    <form method="GET" class="row g-2 mb-3">
        @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        <div class="col-lg-4">
            <div class="input-icon">
                <i class="bi bi-search"></i>
                <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari tiket, SKU, atau nama barang…">
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <select name="type" class="form-select">
                <option value="">Semua jenis</option>
                @foreach (\App\Models\Damage::TYPES as $key => $label)
                    <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-lg-2"><input type="date" name="from" value="{{ request('from') }}" class="form-control" title="Dari tanggal"></div>
        <div class="col-6 col-lg-2"><input type="date" name="to" value="{{ request('to') }}" class="form-control" title="Sampai tanggal"></div>
        <div class="col-6 col-lg-2 d-flex gap-2">
            <button class="btn btn-navy flex-grow-1"><i class="bi bi-funnel me-1"></i>Terapkan</button>
            @if ($hasFilter)
                <a href="{{ route('damages.index') }}" class="btn btn-soft btn-icon" title="Reset filter"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th>Tiket</th><th>Barang</th><th>Jenis</th><th class="text-end">Qty</th><th>Status</th><th>Pelapor</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
            @forelse ($damages as $damage)
                <tr>
                    <td>
                        <a class="ticket-code" href="{{ route('damages.show', $damage) }}">{{ $damage->ticket_code }}</a>
                        <div class="cell-sub">{{ $damage->reported_at->translatedFormat('d M Y') }}</div>
                    </td>
                    <td>
                        <div class="fw-medium">{{ $damage->item->name }}</div>
                        <div class="cell-sub">{{ $damage->item->sku }} · {{ $damage->location->name }}</div>
                    </td>
                    <td>{{ $damage->typeLabel() }}</td>
                    <td class="text-end tabular fw-semibold">{{ $damage->quantity }}</td>
                    <td>
                        <span class="badge {{ $damage->badgeClass() }}">{{ $damage->statusLabel() }}</span>
                        @if ($damage->isOpen() && $damage->ageInDays() > 3)
                            <div class="cell-sub text-danger"><i class="bi bi-clock-history"></i> {{ $damage->ageInDays() }} hari</div>
                        @endif
                    </td>
                    <td class="cell-sub">{{ $damage->reporter->name }}</td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-icon btn-soft" href="{{ route('damages.show', $damage) }}" title="Detail"><i class="bi bi-eye"></i></a>
                        <a class="btn btn-icon btn-soft-primary" href="{{ route('damages.edit', $damage) }}" title="Ubah"><i class="bi bi-pencil"></i></a>
                        <form class="d-inline" method="POST" action="{{ route('damages.destroy', $damage) }}" onsubmit="return confirm('Hapus laporan ini? Stok akan dikembalikan jika belum diperbaiki.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon btn-soft-danger" title="Hapus"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <div class="title">{{ $hasFilter ? 'Tidak ada laporan yang cocok' : 'Belum ada laporan damage' }}</div>
                        {{ $hasFilter ? 'Coba ubah atau reset filter pencarian.' : 'Mulai dengan mencatat barang damage pertama.' }}
                    </div>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($damages->total() > 0)
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
            <div class="small text-muted">Menampilkan {{ $damages->firstItem() }}–{{ $damages->lastItem() }} dari {{ $damages->total() }} laporan</div>
            {{ $damages->links() }}
        </div>
    @endif
</div>
@endsection

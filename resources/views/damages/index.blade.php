@extends('layouts.app')
@section('title', 'Barang Damage')
@section('content')
<div class="card p-3">
    <div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
        <form class="d-flex flex-wrap gap-2" method="GET">
            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari tiket / barang...">
            <select name="status" class="form-select" style="max-width:200px">
                <option value="">Semua status</option>
                @foreach (\App\Models\Damage::STATUSES as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-secondary">Filter</button>
        </form>
        <a href="{{ route('damages.create') }}" class="btn btn-accent">Catat Damage</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Tiket</th><th>Barang</th><th>Jenis</th><th>Qty</th><th>Status</th><th>Tanggal</th><th></th></tr></thead>
            <tbody>
            @forelse ($damages as $damage)
                <tr>
                    <td>{{ $damage->ticket_code }}</td>
                    <td>{{ $damage->item->name }}</td>
                    <td>{{ $damage->typeLabel() }}</td>
                    <td>{{ $damage->quantity }}</td>
                    <td><span class="badge {{ $damage->badgeClass() }}">{{ $damage->statusLabel() }}</span></td>
                    <td>{{ $damage->reported_at->translatedFormat('d M Y') }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('damages.show', $damage) }}">Detail</a>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('damages.edit', $damage) }}">Ubah</a>
                        <form class="d-inline" method="POST" action="{{ route('damages.destroy', $damage) }}" onsubmit="return confirm('Hapus laporan ini? Stok akan dikembalikan jika belum diperbaiki.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted">Belum ada laporan damage.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $damages->links() }}
</div>
@endsection

@extends('layouts.app')
@section('title', 'Master Barang')
@section('subtitle', 'Data barang, stok, dan lokasi penyimpanan')

@section('actions')
    <a href="{{ route('items.create') }}" class="btn btn-accent"><i class="bi bi-plus-lg me-1"></i><span class="d-none d-sm-inline">Tambah Barang</span></a>
@endsection

@section('content')
@php $hasFilter = request()->hasAny(['q', 'category_id', 'low']); @endphp
<div class="card p-3">
    <form method="GET" class="row g-2 mb-3 align-items-center">
        <div class="col-lg-5">
            <div class="input-icon">
                <i class="bi bi-search"></i>
                <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari SKU atau nama barang…">
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <select name="category_id" class="form-select">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-lg-2">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" name="low" value="1" id="low" @checked(request()->boolean('low'))>
                <label class="form-check-label small" for="low">Stok menipis</label>
            </div>
        </div>
        <div class="col-12 col-lg-2 d-flex gap-2">
            <button class="btn btn-navy flex-grow-1"><i class="bi bi-funnel me-1"></i>Terapkan</button>
            @if ($hasFilter)<a href="{{ route('items.index') }}" class="btn btn-soft btn-icon" title="Reset"><i class="bi bi-x-lg"></i></a>@endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>SKU</th><th>Nama barang</th><th>Kategori</th><th>Lokasi</th><th style="min-width:170px">Stok</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse ($items as $item)
                @php
                    $low = $item->isLowStock();
                    $ratio = $item->min_stock > 0 ? $item->stock / ($item->min_stock * 2) : 1;
                    $pct = max(4, min(100, round($ratio * 100)));
                @endphp
                <tr>
                    <td class="ticket-code">{{ $item->sku }}</td>
                    <td class="fw-medium">{{ $item->name }}</td>
                    <td><span class="chip" style="padding:.2rem .6rem">{{ $item->category->name }}</span></td>
                    <td>{{ $item->location->name }}</td>
                    <td>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="tabular fw-semibold {{ $low ? 'text-danger' : '' }}">{{ $item->stock }} {{ $item->unit }}</span>
                            <span class="text-muted">min {{ $item->min_stock }}</span>
                        </div>
                        <div class="stock-bar {{ $low ? 'low' : '' }}"><span style="width:{{ $pct }}%"></span></div>
                    </td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-icon btn-soft-primary" href="{{ route('items.edit', $item) }}" title="Ubah"><i class="bi bi-pencil"></i></a>
                        <form class="d-inline" method="POST" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('Hapus barang ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon btn-soft-danger" title="Hapus"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-box-seam"></i><div class="title">{{ $hasFilter ? 'Barang tidak ditemukan' : 'Belum ada barang' }}</div>{{ $hasFilter ? 'Coba ubah kata kunci atau filter.' : 'Tambahkan barang pertama Anda.' }}</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($items->total() > 0)
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
            <div class="small text-muted">Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }} barang</div>
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection

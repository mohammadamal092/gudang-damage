@extends('layouts.app')
@section('title', 'Master Barang')
@section('content')
<div class="card p-3">
    <div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
        <form class="d-flex flex-wrap gap-2" method="GET">
            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari SKU / nama...">
            <select name="category_id" class="form-select" style="max-width:220px">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-secondary">Filter</button>
        </form>
        <a href="{{ route('items.create') }}" class="btn btn-accent">Tambah Barang</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>SKU</th><th>Nama</th><th>Kategori</th><th>Lokasi</th><th>Stok</th><th></th></tr></thead>
            <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->sku }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category->name }}</td>
                    <td>{{ $item->location->name }}</td>
                    <td>
                        {{ $item->stock }} {{ $item->unit }}
                        @if ($item->isLowStock())
                            <span class="badge text-bg-danger">menipis</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('items.edit', $item) }}">Ubah</a>
                        <form class="d-inline" method="POST" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('Hapus barang ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted">Belum ada barang.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>
@endsection

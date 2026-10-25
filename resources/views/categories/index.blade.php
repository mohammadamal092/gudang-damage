@extends('layouts.app')
@section('title', 'Kategori Barang')
@section('subtitle', 'Kelompok jenis barang di gudang')

@section('actions')
    <a href="{{ route('categories.create') }}" class="btn btn-accent"><i class="bi bi-plus-lg me-1"></i><span class="d-none d-sm-inline">Tambah Kategori</span></a>
@endsection

@section('content')
<div class="card p-3">
    <form class="row g-2 mb-3" method="GET">
        <div class="col-md-6 col-lg-4">
            <div class="input-icon">
                <i class="bi bi-search"></i>
                <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari kategori…">
            </div>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-navy">Cari</button>
            @if (request('q'))<a href="{{ route('categories.index') }}" class="btn btn-soft btn-icon" title="Reset"><i class="bi bi-x-lg"></i></a>@endif
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Kode</th><th>Nama</th><th class="text-end">Jumlah barang</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td><span class="chip" style="padding:.2rem .6rem;font-weight:600">{{ $category->code }}</span></td>
                    <td>
                        <div class="fw-medium">{{ $category->name }}</div>
                        @if ($category->description)<div class="cell-sub">{{ \Illuminate\Support\Str::limit($category->description, 70) }}</div>@endif
                    </td>
                    <td class="text-end tabular fw-semibold">{{ $category->items_count }}</td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-icon btn-soft-primary" href="{{ route('categories.edit', $category) }}" title="Ubah"><i class="bi bi-pencil"></i></a>
                        <form class="d-inline" method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-icon btn-soft-danger" title="Hapus"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4"><div class="empty-state"><i class="bi bi-tags"></i><div class="title">Belum ada kategori</div></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($categories->total() > 0)
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
            <div class="small text-muted">{{ $categories->total() }} kategori</div>
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection

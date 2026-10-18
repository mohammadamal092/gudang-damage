@extends('layouts.app')
@section('title', 'Kategori Barang')
@section('content')
<div class="card p-3">
    <div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
        <form class="d-flex gap-2" method="GET">
            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari kategori...">
            <button class="btn btn-outline-secondary">Cari</button>
        </form>
        <a href="{{ route('categories.create') }}" class="btn btn-accent">Tambah Kategori</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Kode</th><th>Nama</th><th>Barang</th><th></th></tr></thead>
            <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>{{ $category->code }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->items_count }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('categories.edit', $category) }}">Ubah</a>
                        <form class="d-inline" method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted">Belum ada kategori.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $categories->links() }}
</div>
@endsection

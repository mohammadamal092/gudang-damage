@extends('layouts.app')
@section('title', 'Lokasi Gudang')
@section('content')
<div class="card p-3">
    <div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
        <form class="d-flex gap-2" method="GET">
            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari lokasi...">
            <button class="btn btn-outline-secondary">Cari</button>
        </form>
        <a href="{{ route('locations.create') }}" class="btn btn-accent">Tambah Lokasi</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Kode</th><th>Nama</th><th>Area</th><th>Barang</th><th></th></tr></thead>
            <tbody>
            @forelse ($locations as $location)
                <tr>
                    <td>{{ $location->code }}</td>
                    <td>{{ $location->name }}</td>
                    <td>{{ $location->area }}</td>
                    <td>{{ $location->items_count }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('locations.edit', $location) }}">Ubah</a>
                        <form class="d-inline" method="POST" action="{{ route('locations.destroy', $location) }}" onsubmit="return confirm('Hapus lokasi ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">Belum ada lokasi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $locations->links() }}
</div>
@endsection

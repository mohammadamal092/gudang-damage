@extends('layouts.app')
@section('title', $damage->exists ? 'Ubah Laporan Damage' : 'Catat Barang Damage')
@section('content')
<div class="card p-4">
    <form method="POST" enctype="multipart/form-data" action="{{ $damage->exists ? route('damages.update', $damage) : route('damages.store') }}">
        @csrf
        @if ($damage->exists) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Barang</label>
                <select name="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                    <option value="">Pilih barang</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" @selected(old('item_id', $damage->item_id) == $item->id)>
                            {{ $item->sku }} — {{ $item->name }} (stok {{ $item->stock }})
                        </option>
                    @endforeach
                </select>
                @error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Lokasi temuan</label>
                <select name="warehouse_location_id" class="form-select @error('warehouse_location_id') is-invalid @enderror" required>
                    <option value="">Pilih lokasi</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @selected(old('warehouse_location_id', $damage->warehouse_location_id) == $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
                @error('warehouse_location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Jumlah</label>
                <input type="number" min="1" name="quantity" value="{{ old('quantity', $damage->quantity ?: 1) }}" class="form-control @error('quantity') is-invalid @enderror" required>
                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Jenis kerusakan</label>
                <select name="damage_type" class="form-select" required>
                    @foreach (\App\Models\Damage::TYPES as $key => $label)
                        <option value="{{ $key }}" @selected(old('damage_type', $damage->damage_type) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    @foreach (\App\Models\Damage::STATUSES as $key => $label)
                        <option value="{{ $key }}" @selected(old('status', $damage->status) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tanggal temuan</label>
                <input type="date" name="reported_at" value="{{ old('reported_at', optional($damage->reported_at)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="form-control" required>
            </div>
            <div class="col-md-8">
                <label class="form-label">Foto kerusakan (opsional)</label>
                <input type="file" name="photo" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
                @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Keterangan</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $damage->description) }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Catatan penanganan</label>
                <textarea name="handling_notes" class="form-control" rows="2">{{ old('handling_notes', $damage->handling_notes) }}</textarea>
            </div>
        </div>
        <p class="small text-muted mt-3 mb-0">Stok barang akan berkurang saat status bukan "Diperbaiki". Jika status diubah menjadi diperbaiki, stok dikembalikan.</p>
        <div class="mt-3">
            <button class="btn btn-accent">Simpan</button>
            <a href="{{ route('damages.index') }}" class="btn btn-light">Batal</a>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')
@section('title', $item->exists ? 'Ubah Barang' : 'Tambah Barang')
@section('actions')
    <a href="{{ route('items.index') }}" class="btn btn-soft"><i class="bi bi-arrow-left me-1"></i><span class="d-none d-sm-inline">Kembali</span></a>
@endsection
@section('content')
<div class="card p-4">
    <form method="POST" action="{{ $item->exists ? route('items.update', $item) : route('items.store') }}">
        @csrf
        @if ($item->exists) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">SKU</label>
                <input name="sku" value="{{ old('sku', $item->sku) }}" class="form-control @error('sku') is-invalid @enderror" required>
                @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-8">
                <label class="form-label">Nama barang</label>
                <input name="name" value="{{ old('name', $item->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $item->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Lokasi</label>
                <select name="warehouse_location_id" class="form-select @error('warehouse_location_id') is-invalid @enderror" required>
                    <option value="">Pilih lokasi</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @selected(old('warehouse_location_id', $item->warehouse_location_id) == $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
                @error('warehouse_location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Satuan</label>
                <input name="unit" value="{{ old('unit', $item->unit ?: 'pcs') }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Stok</label>
                <input type="number" min="0" name="stock" value="{{ old('stock', $item->stock ?: 0) }}" class="form-control @error('stock') is-invalid @enderror" required>
                @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Stok minimum</label>
                <input type="number" min="0" name="min_stock" value="{{ old('min_stock', $item->min_stock ?: 0) }}" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $item->description) }}</textarea>
            </div>
        </div>
        <div class="mt-4">
            <button class="btn btn-accent"><i class="bi bi-check2 me-1"></i>Simpan</button>
            <a href="{{ route('items.index') }}" class="btn btn-soft">Batal</a>
        </div>
    </form>
</div>
@endsection

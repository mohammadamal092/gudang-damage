@extends('layouts.app')
@section('title', $location->exists ? 'Ubah Lokasi' : 'Tambah Lokasi')
@section('actions')
    <a href="{{ route('locations.index') }}" class="btn btn-soft"><i class="bi bi-arrow-left me-1"></i><span class="d-none d-sm-inline">Kembali</span></a>
@endsection
@section('content')
<div class="card p-4 col-lg-7">
    <form method="POST" action="{{ $location->exists ? route('locations.update', $location) : route('locations.store') }}">
        @csrf
        @if ($location->exists) @method('PUT') @endif
        <div class="mb-3">
            <label class="form-label">Kode</label>
            <input name="code" value="{{ old('code', $location->code) }}" class="form-control @error('code') is-invalid @enderror" required>
            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input name="name" value="{{ old('name', $location->name) }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Area</label>
            <input name="area" value="{{ old('area', $location->area) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $location->description) }}</textarea>
        </div>
        <button class="btn btn-accent"><i class="bi bi-check2 me-1"></i>Simpan</button>
        <a href="{{ route('locations.index') }}" class="btn btn-soft">Batal</a>
    </form>
</div>
@endsection

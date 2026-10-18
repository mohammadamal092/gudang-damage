@extends('layouts.app')
@section('title', $category->exists ? 'Ubah Kategori' : 'Tambah Kategori')
@section('content')
<div class="card p-4 col-lg-7">
    <form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
        @csrf
        @if ($category->exists) @method('PUT') @endif
        <div class="mb-3">
            <label class="form-label">Kode</label>
            <input name="code" value="{{ old('code', $category->code) }}" class="form-control @error('code') is-invalid @enderror" required>
            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input name="name" value="{{ old('name', $category->name) }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
        </div>
        <button class="btn btn-accent">Simpan</button>
        <a href="{{ route('categories.index') }}" class="btn btn-light">Batal</a>
    </form>
</div>
@endsection

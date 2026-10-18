@extends('layouts.app')
@section('title', 'Detail '.$damage->ticket_code)
@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small">{{ $damage->ticket_code }}</div>
                    <h2 class="h4">{{ $damage->item->name }}</h2>
                    <span class="badge {{ $damage->badgeClass() }}">{{ $damage->statusLabel() }}</span>
                </div>
                <a href="{{ route('damages.edit', $damage) }}" class="btn btn-accent">Ubah</a>
            </div>
            <dl class="row mb-0">
                <dt class="col-sm-4">SKU</dt><dd class="col-sm-8">{{ $damage->item->sku }}</dd>
                <dt class="col-sm-4">Kategori</dt><dd class="col-sm-8">{{ $damage->item->category->name }}</dd>
                <dt class="col-sm-4">Lokasi temuan</dt><dd class="col-sm-8">{{ $damage->location->name }}</dd>
                <dt class="col-sm-4">Jenis kerusakan</dt><dd class="col-sm-8">{{ $damage->typeLabel() }}</dd>
                <dt class="col-sm-4">Jumlah</dt><dd class="col-sm-8">{{ $damage->quantity }} {{ $damage->item->unit }}</dd>
                <dt class="col-sm-4">Tanggal</dt><dd class="col-sm-8">{{ $damage->reported_at->translatedFormat('d M Y') }}</dd>
                <dt class="col-sm-4">Pelapor</dt><dd class="col-sm-8">{{ $damage->reporter->name }}</dd>
                <dt class="col-sm-4">Keterangan</dt><dd class="col-sm-8">{{ $damage->description ?: '-' }}</dd>
                <dt class="col-sm-4">Catatan penanganan</dt><dd class="col-sm-8">{{ $damage->handling_notes ?: '-' }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3">
            <h2 class="h6">Foto</h2>
            @if ($damage->photo_path)
                <img src="{{ asset('storage/'.$damage->photo_path) }}" class="img-fluid rounded" alt="Foto damage">
            @else
                <p class="text-muted mb-0">Tidak ada foto.</p>
            @endif
        </div>
    </div>
</div>
@endsection

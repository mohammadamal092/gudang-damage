@extends('layouts.app')
@section('title', $damage->ticket_code)
@section('subtitle', 'Detail laporan barang damage')

@section('actions')
    <a href="{{ route('damages.index') }}" class="btn btn-soft"><i class="bi bi-arrow-left me-1"></i><span class="d-none d-sm-inline">Kembali</span></a>
    <a href="{{ route('damages.print', $damage) }}" target="_blank" class="btn btn-soft"><i class="bi bi-printer me-1"></i><span class="d-none d-sm-inline">Cetak</span></a>
    <a href="{{ route('damages.edit', $damage) }}" class="btn btn-accent"><i class="bi bi-pencil me-1"></i><span class="d-none d-sm-inline">Ubah</span></a>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card p-4 mb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
                <div>
                    <div class="text-muted small tabular">{{ $damage->ticket_code }}</div>
                    <h2 class="h4 fw-bold mb-2">{{ $damage->item->name }}</h2>
                    <span class="badge {{ $damage->badgeClass() }}">{{ $damage->statusLabel() }}</span>
                    @if ($damage->isOpen() && $damage->ageInDays() > 3)
                        <span class="small text-danger ms-2"><i class="bi bi-clock-history"></i> terbuka {{ $damage->ageInDays() }} hari</span>
                    @endif
                </div>
                <div class="text-end">
                    <div class="text-muted small">Jumlah rusak</div>
                    <div class="fs-2 fw-bold tabular lh-1">{{ $damage->quantity }} <span class="fs-6 fw-medium text-muted">{{ $damage->item->unit }}</span></div>
                </div>
            </div>

            <div class="detail-grid mb-4">
                <div><div class="k">SKU</div><div class="v">{{ $damage->item->sku }}</div></div>
                <div><div class="k">Kategori</div><div class="v">{{ $damage->item->category->name }}</div></div>
                <div><div class="k">Lokasi temuan</div><div class="v">{{ $damage->location->name }}</div></div>
                <div><div class="k">Jenis kerusakan</div><div class="v">{{ $damage->typeLabel() }}</div></div>
                <div><div class="k">Tanggal temuan</div><div class="v">{{ $damage->reported_at->translatedFormat('d F Y') }}</div></div>
                <div><div class="k">Pelapor</div><div class="v">{{ $damage->reporter->name }}</div></div>
            </div>

            <div class="mb-3">
                <div class="k text-uppercase text-muted fw-bold" style="font-size:.72rem;letter-spacing:.08em">Keterangan</div>
                <p class="mb-0">{{ $damage->description ?: '—' }}</p>
            </div>
            <div>
                <div class="k text-uppercase text-muted fw-bold" style="font-size:.72rem;letter-spacing:.08em">Catatan penanganan</div>
                <p class="mb-0">{{ $damage->handling_notes ?: '—' }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Ubah status cepat --}}
        <div class="card p-3 mb-3">
            <h2 class="card-title-sm mb-3"><i class="bi bi-lightning-charge text-warning me-1"></i>Tindak lanjut cepat</h2>
            <form method="POST" action="{{ route('damages.status', $damage) }}">
                @csrf @method('PATCH')
                <div class="mb-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach (\App\Models\Damage::STATUSES as $key => $label)
                            <option value="{{ $key }}" @selected($damage->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan penanganan</label>
                    <textarea name="handling_notes" rows="3" class="form-control" placeholder="Mis. dikirim ke vendor, diganti unit baru…">{{ old('handling_notes', $damage->handling_notes) }}</textarea>
                </div>
                <button class="btn btn-navy w-100"><i class="bi bi-check2 me-1"></i>Simpan status</button>
                <div class="form-text">Stok otomatis kembali jika status “Diperbaiki”.</div>
            </form>
        </div>

        <div class="card p-3">
            <h2 class="card-title-sm mb-3">Foto kerusakan</h2>
            @if ($damage->photo_path)
                <a href="{{ asset('storage/'.$damage->photo_path) }}" target="_blank" class="photo-frame d-block">
                    <img src="{{ asset('storage/'.$damage->photo_path) }}" alt="Foto damage">
                </a>
            @else
                <div class="empty-state py-4"><i class="bi bi-image"></i>Tidak ada foto.</div>
            @endif
        </div>
    </div>
</div>
@endsection

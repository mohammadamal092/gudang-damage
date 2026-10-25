@extends('layouts.app')
@section('title', $damage->exists ? 'Ubah Laporan Damage' : 'Catat Barang Damage')
@section('subtitle', $damage->exists ? $damage->ticket_code : 'Isi detail barang yang rusak')

@section('actions')
    <a href="{{ route('damages.index') }}" class="btn btn-soft"><i class="bi bi-arrow-left me-1"></i><span class="d-none d-sm-inline">Kembali</span></a>
@endsection

@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $damage->exists ? route('damages.update', $damage) : route('damages.store') }}">
    @csrf
    @if ($damage->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card p-4 mb-3">
                <div class="form-section-title">Barang &amp; lokasi</div>
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label" for="item_id">Barang</label>
                        <select id="item_id" name="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                            <option value="">Pilih barang</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" data-stock="{{ $item->stock }}" data-unit="{{ $item->unit }}" data-location="{{ $item->warehouse_location_id }}"
                                    @selected(old('item_id', $damage->item_id) == $item->id)>
                                    {{ $item->sku }} — {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text" id="stockHint">Pilih barang untuk melihat stok tersedia.</div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="warehouse_location_id">Lokasi temuan</label>
                        <select id="warehouse_location_id" name="warehouse_location_id" class="form-select @error('warehouse_location_id') is-invalid @enderror" required>
                            <option value="">Pilih lokasi</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" @selected(old('warehouse_location_id', $damage->warehouse_location_id) == $location->id)>{{ $location->name }}</option>
                            @endforeach
                        </select>
                        @error('warehouse_location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card p-4 mb-3">
                <div class="form-section-title">Detail kerusakan</div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label" for="quantity">Jumlah</label>
                        <input id="quantity" type="number" min="1" name="quantity" value="{{ old('quantity', $damage->quantity ?: 1) }}" class="form-control @error('quantity') is-invalid @enderror" required>
                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="damage_type">Jenis kerusakan</label>
                        <select id="damage_type" name="damage_type" class="form-select" required>
                            @foreach (\App\Models\Damage::TYPES as $key => $label)
                                <option value="{{ $key }}" @selected(old('damage_type', $damage->damage_type) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="reported_at">Tanggal temuan</label>
                        <input id="reported_at" type="date" name="reported_at" value="{{ old('reported_at', optional($damage->reported_at)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="form-control @error('reported_at') is-invalid @enderror" required>
                        @error('reported_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="description">Keterangan</label>
                        <textarea id="description" name="description" class="form-control" rows="3" placeholder="Jelaskan kondisi dan penyebab kerusakan…">{{ old('description', $damage->description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-4 mb-3">
                <div class="form-section-title">Penanganan</div>
                <div class="mb-3">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select" required>
                        @foreach (\App\Models\Damage::STATUSES as $key => $label)
                            <option value="{{ $key }}" @selected(old('status', $damage->status) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="handling_notes">Catatan penanganan</label>
                    <textarea id="handling_notes" name="handling_notes" class="form-control" rows="3">{{ old('handling_notes', $damage->handling_notes) }}</textarea>
                </div>
                <div class="alert alert-secondary small mt-3 mb-0 shadow-none" style="background:#f5f1e8;color:#5c5648">
                    <i class="bi bi-info-circle"></i>
                    <div>Stok berkurang saat status bukan “Diperbaiki”. Jika menjadi “Diperbaiki”, stok dikembalikan.</div>
                </div>
            </div>

            <div class="card p-4 mb-3">
                <div class="form-section-title">Foto (opsional)</div>
                @if ($damage->photo_path)
                    <img src="{{ asset('storage/'.$damage->photo_path) }}" class="img-fluid rounded mb-2" alt="Foto saat ini">
                @endif
                <input type="file" id="photo" name="photo" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
                @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <img id="photoPreview" class="img-fluid rounded mt-2 d-none" alt="Pratinjau foto">
                <div class="form-text">Maks. 2 MB (JPG/PNG).</div>
            </div>

            <div class="d-grid gap-2">
                <button class="btn btn-accent py-2"><i class="bi bi-check2-circle me-1"></i>Simpan laporan</button>
                <a href="{{ route('damages.index') }}" class="btn btn-soft">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const itemSel = document.getElementById('item_id');
    const locSel = document.getElementById('warehouse_location_id');
    const hint = document.getElementById('stockHint');
    const qty = document.getElementById('quantity');
    const isEdit = {{ $damage->exists ? 'true' : 'false' }};

    function onItemChange(autoLocation) {
        const opt = itemSel.options[itemSel.selectedIndex];
        if (!opt || !opt.value) { hint.textContent = 'Pilih barang untuk melihat stok tersedia.'; return; }
        hint.innerHTML = 'Stok tersedia: <strong>' + opt.dataset.stock + ' ' + opt.dataset.unit + '</strong>';
        // Tidak mengunci max pada mode ubah, karena stok sudah termasuk pengurangan laporan ini.
        if (!isEdit) qty.max = opt.dataset.stock;
        if (autoLocation && opt.dataset.location && !locSel.value) locSel.value = opt.dataset.location;
    }
    itemSel.addEventListener('change', () => onItemChange(true));
    onItemChange(false);

    document.getElementById('photo').addEventListener('change', e => {
        const f = e.target.files[0], img = document.getElementById('photoPreview');
        if (!f) { img.classList.add('d-none'); return; }
        img.src = URL.createObjectURL(f); img.classList.remove('d-none');
    });
</script>
@endpush

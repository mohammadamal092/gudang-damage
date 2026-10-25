<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Tiket {{ $damage->ticket_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; color: #111; background: #fff; }
        .sheet { max-width: 760px; margin: 2rem auto; padding: 2rem; border: 1px solid #ddd; border-radius: 12px; }
        .k { font-size: .72rem; text-transform: uppercase; letter-spacing: .08em; color: #666; font-weight: 700; }
        .sign { height: 80px; border-bottom: 1px solid #333; }
        @media print { .sheet { border: 0; margin: 0; padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
<div class="sheet">
    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4">
        <div>
            <div class="fw-bold fs-4">Berita Acara Barang Damage</div>
            <div class="text-muted">{{ config('app.name') }}</div>
        </div>
        <div class="text-end">
            <div class="k">No. Tiket</div>
            <div class="fw-bold fs-5">{{ $damage->ticket_code }}</div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6"><div class="k">Barang</div>{{ $damage->item->name }}</div>
        <div class="col-6"><div class="k">SKU</div>{{ $damage->item->sku }}</div>
        <div class="col-6"><div class="k">Kategori</div>{{ $damage->item->category->name }}</div>
        <div class="col-6"><div class="k">Lokasi temuan</div>{{ $damage->location->name }}</div>
        <div class="col-6"><div class="k">Jenis kerusakan</div>{{ $damage->typeLabel() }}</div>
        <div class="col-6"><div class="k">Jumlah</div>{{ $damage->quantity }} {{ $damage->item->unit }}</div>
        <div class="col-6"><div class="k">Tanggal temuan</div>{{ $damage->reported_at->translatedFormat('d F Y') }}</div>
        <div class="col-6"><div class="k">Status</div>{{ $damage->statusLabel() }}</div>
        <div class="col-12"><div class="k">Keterangan</div>{{ $damage->description ?: '—' }}</div>
        <div class="col-12"><div class="k">Catatan penanganan</div>{{ $damage->handling_notes ?: '—' }}</div>
    </div>

    @if ($damage->photo_path)
        <div class="mb-4">
            <div class="k mb-1">Foto</div>
            <img src="{{ asset('storage/'.$damage->photo_path) }}" style="max-height:260px;border-radius:8px" alt="Foto damage">
        </div>
    @endif

    <div class="row text-center mt-5">
        <div class="col-6"><div class="small">Pelapor</div><div class="sign"></div><div class="small mt-1">{{ $damage->reporter->name }}</div></div>
        <div class="col-6"><div class="small">Mengetahui (Kepala Gudang)</div><div class="sign"></div><div class="small mt-1">( ................................ )</div></div>
    </div>

    <div class="text-center mt-4 no-print">
        <button class="btn btn-dark" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>
</div>
</body>
</html>

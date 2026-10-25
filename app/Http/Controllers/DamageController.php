<?php

namespace App\Http\Controllers;

use App\Models\Damage;
use App\Models\Item;
use App\Models\WarehouseLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DamageController extends Controller
{
    public function index(Request $request): View
    {
        $damages = $this->filteredQuery($request)
            ->with(['item', 'location', 'reporter'])
            ->latest('reported_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('damages.index', [
            'damages' => $damages,
            'statusCounts' => Damage::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }

    /** Ekspor CSV mengikuti filter yang sedang aktif (dibuka di Excel). */
    public function export(Request $request): StreamedResponse
    {
        $query = $this->filteredQuery($request)
            ->with(['item.category', 'location', 'reporter'])
            ->latest('reported_at')
            ->latest('id');

        $filename = 'laporan-damage-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
            fputcsv($out, ['Tiket', 'Tanggal', 'SKU', 'Barang', 'Kategori', 'Lokasi', 'Jenis Kerusakan', 'Jumlah', 'Satuan', 'Status', 'Pelapor', 'Keterangan', 'Catatan Penanganan']);

            $query->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $d) {
                    fputcsv($out, [
                        $d->ticket_code,
                        $d->reported_at->format('Y-m-d'),
                        $d->item->sku,
                        $d->item->name,
                        $d->item->category->name ?? '-',
                        $d->location->name ?? '-',
                        $d->typeLabel(),
                        $d->quantity,
                        $d->item->unit,
                        $d->statusLabel(),
                        $d->reporter->name ?? '-',
                        $d->description,
                        $d->handling_notes,
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function create(): View
    {
        return view('damages.form', $this->formData(new Damage(['status' => 'dilaporkan', 'reported_at' => now()])));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $item = Item::findOrFail($data['item_id']);

        if ($data['status'] !== 'diperbaiki' && $data['quantity'] > $item->stock) {
            return back()->withErrors(['quantity' => 'Jumlah damage melebihi stok tersedia ('.$item->stock.').'])->withInput();
        }

        DB::transaction(function () use ($request, $data, $item) {
            $data['ticket_code'] = $this->nextTicket();
            $data['reported_by'] = $request->user()->id;
            $data['photo_path'] = $this->storePhoto($request);

            Damage::create($data);

            if ($data['status'] !== 'diperbaiki') {
                $item->decrement('stock', $data['quantity']);
            }
        });

        return redirect()->route('damages.index')->with('success', 'Laporan barang damage berhasil dicatat.');
    }

    public function show(Damage $damage): View
    {
        $damage->load(['item.category', 'location', 'reporter']);

        return view('damages.show', compact('damage'));
    }

    /** Halaman tiket siap cetak (Ctrl+P / Simpan sebagai PDF). */
    public function print(Damage $damage): View
    {
        $damage->load(['item.category', 'location', 'reporter']);

        return view('damages.print', compact('damage'));
    }

    public function edit(Damage $damage): View
    {
        return view('damages.form', $this->formData($damage));
    }

    public function update(Request $request, Damage $damage): RedirectResponse
    {
        $data = $this->validated($request, $damage->id);
        $item = Item::findOrFail($data['item_id']);

        try {
            DB::transaction(function () use ($request, $data, $damage, $item) {
                $this->restoreStock($damage);

                $available = $item->fresh()->stock;
                if ($data['status'] !== 'diperbaiki' && $data['quantity'] > $available) {
                    throw new \RuntimeException('Jumlah damage melebihi stok tersedia ('.$available.').');
                }

                if ($request->hasFile('photo')) {
                    if ($damage->photo_path) {
                        Storage::disk('public')->delete($damage->photo_path);
                    }
                    $data['photo_path'] = $this->storePhoto($request);
                }

                unset($data['ticket_code'], $data['reported_by']);
                $damage->update($data);

                if ($data['status'] !== 'diperbaiki') {
                    $item->decrement('stock', $data['quantity']);
                }
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()])->withInput();
        }

        return redirect()->route('damages.index')->with('success', 'Laporan damage berhasil diperbarui.');
    }

    /** Ubah status (dan catatan) langsung dari halaman detail, stok ikut disesuaikan. */
    public function updateStatus(Request $request, Damage $damage): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Damage::STATUSES))],
            'handling_notes' => ['nullable', 'string'],
        ]);

        try {
            DB::transaction(function () use ($damage, $data) {
                $item = $damage->item;
                $wasRestored = $damage->status === 'diperbaiki';
                $willRestore = $data['status'] === 'diperbaiki';

                if (! $wasRestored && $willRestore) {
                    // Barang pulih: kembalikan ke stok.
                    $item->increment('stock', $damage->quantity);
                } elseif ($wasRestored && ! $willRestore) {
                    // Dibuka kembali: kurangi stok lagi.
                    if ($damage->quantity > $item->stock) {
                        throw new \RuntimeException('Stok tidak cukup untuk membuka kembali laporan ini (stok '.$item->stock.').');
                    }
                    $item->decrement('stock', $damage->quantity);
                }

                $damage->update($data);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Status diubah menjadi "'.Damage::STATUSES[$data['status']].'".');
    }

    public function destroy(Damage $damage): RedirectResponse
    {
        DB::transaction(function () use ($damage) {
            $this->restoreStock($damage);
            if ($damage->photo_path) {
                Storage::disk('public')->delete($damage->photo_path);
            }
            $damage->delete();
        });

        return redirect()->route('damages.index')->with('success', 'Laporan damage berhasil dihapus.');
    }

    private function filteredQuery(Request $request): Builder
    {
        return Damage::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q')->toString();
                $query->where(function ($inner) use ($q) {
                    $inner->where('ticket_code', 'like', "%{$q}%")
                        ->orWhereHas('item', function ($itemQuery) use ($q) {
                            $itemQuery->where('name', 'like', "%{$q}%")
                                ->orWhere('sku', 'like', "%{$q}%");
                        });
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('type'), fn ($query) => $query->where('damage_type', $request->string('type')->toString()))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('reported_at', '>=', $request->string('from')->toString()))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('reported_at', '<=', $request->string('to')->toString()));
    }

    private function restoreStock(Damage $damage): void
    {
        if ($damage->status !== 'diperbaiki') {
            $damage->item->increment('stock', $damage->quantity);
        }
    }

    private function formData(Damage $damage): array
    {
        return [
            'damage' => $damage,
            'items' => Item::with('location')->orderBy('name')->get(),
            'locations' => WarehouseLocation::orderBy('name')->get(),
        ];
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'warehouse_location_id' => ['required', 'exists:warehouse_locations,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'damage_type' => ['required', Rule::in(array_keys(Damage::TYPES))],
            'status' => ['required', Rule::in(array_keys(Damage::STATUSES))],
            'reported_at' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'handling_notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    private function storePhoto(Request $request): ?string
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        return $request->file('photo')->store('damages', 'public');
    }

    private function nextTicket(): string
    {
        $prefix = 'DMG-'.now()->format('Ymd');
        $last = Damage::where('ticket_code', 'like', $prefix.'%')->orderByDesc('ticket_code')->value('ticket_code');
        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.'-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}

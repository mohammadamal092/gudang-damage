<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\WarehouseLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $items = Item::query()
            ->with(['category', 'location'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q')->toString();
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('items.index', [
            'items' => $items,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('items.form', $this->formData(new Item));
    }

    public function store(Request $request): RedirectResponse
    {
        Item::create($this->validated($request));

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item): View
    {
        return view('items.form', $this->formData($item));
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $item->update($this->validated($request, $item->id));

        return redirect()->route('items.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        if ($item->damages()->exists()) {
            return back()->with('error', 'Barang tidak dapat dihapus karena sudah memiliki laporan damage.');
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }

    private function formData(Item $item): array
    {
        return [
            'item' => $item,
            'categories' => Category::orderBy('name')->get(),
            'locations' => WarehouseLocation::orderBy('name')->get(),
        ];
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'sku' => ['required', 'string', 'max:40', 'unique:items,sku,'.($id ?? 'NULL')],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'warehouse_location_id' => ['required', 'exists:warehouse_locations,id'],
            'unit' => ['required', 'string', 'max:20'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);
    }
}

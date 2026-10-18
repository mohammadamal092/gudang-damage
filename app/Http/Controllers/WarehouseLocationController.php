<?php

namespace App\Http\Controllers;

use App\Models\WarehouseLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseLocationController extends Controller
{
    public function index(Request $request): View
    {
        $locations = WarehouseLocation::query()
            ->withCount('items')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q')->toString();
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%")
                        ->orWhere('area', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('locations.index', compact('locations'));
    }

    public function create(): View
    {
        return view('locations.form', ['location' => new WarehouseLocation]);
    }

    public function store(Request $request): RedirectResponse
    {
        WarehouseLocation::create($this->validated($request));

        return redirect()->route('locations.index')->with('success', 'Lokasi gudang berhasil ditambahkan.');
    }

    public function edit(WarehouseLocation $location): View
    {
        return view('locations.form', compact('location'));
    }

    public function update(Request $request, WarehouseLocation $location): RedirectResponse
    {
        $location->update($this->validated($request, $location->id));

        return redirect()->route('locations.index')->with('success', 'Lokasi gudang berhasil diperbarui.');
    }

    public function destroy(WarehouseLocation $location): RedirectResponse
    {
        if ($location->items()->exists() || $location->damages()->exists()) {
            return back()->with('error', 'Lokasi tidak dapat dihapus karena masih dipakai data lain.');
        }

        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Lokasi gudang berhasil dihapus.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20', 'unique:warehouse_locations,code,'.($id ?? 'NULL')],
            'area' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);
    }
}

@php
    $openCount = \App\Models\Damage::whereIn('status', ['dilaporkan', 'diproses'])->count();
@endphp
<nav class="nav flex-column gap-1">
    <div class="nav-label">Utama</div>
    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <i class="bi bi-grid-1x2"></i> Dashboard
    </a>
    <a class="nav-link {{ request()->routeIs('damages.*') ? 'active' : '' }}" href="{{ route('damages.index') }}">
        <i class="bi bi-exclamation-octagon"></i> Barang Damage
        @if ($openCount > 0)
            <span class="badge rounded-pill ms-auto" style="background:var(--gd-accent);color:var(--gd-navy)">{{ $openCount }}</span>
        @endif
    </a>

    <div class="nav-label">Data Master</div>
    <a class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}" href="{{ route('items.index') }}">
        <i class="bi bi-box-seam"></i> Master Barang
    </a>
    <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
        <i class="bi bi-tags"></i> Kategori
    </a>
    <a class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}" href="{{ route('locations.index') }}">
        <i class="bi bi-geo-alt"></i> Lokasi Gudang
    </a>
</nav>

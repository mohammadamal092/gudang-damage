<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --gd-sidebar: #13253b;
            --gd-accent: #f4a261;
            --gd-bg: #f3f0ea;
        }
        body { font-family: "DM Sans", sans-serif; background: var(--gd-bg); }
        .sidebar {
            width: 260px; min-height: 100vh; background: var(--gd-sidebar); color: #dbe7f3;
            position: sticky; top: 0;
        }
        .sidebar .brand { color: #fff; letter-spacing: .02em; }
        .sidebar .brand small { color: var(--gd-accent); }
        .sidebar .nav-link { color: #c5d4e3; border-radius: 10px; padding: .7rem .9rem; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { background: rgba(244,162,97,.15); color: #fff; }
        .content-wrap { min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e6dfd4; }
        .card { border: 0; border-radius: 16px; box-shadow: 0 8px 24px rgba(19,37,59,.06); }
        .stat-card { border-left: 4px solid var(--gd-accent); }
        .btn-accent { background: var(--gd-accent); color: #13253b; font-weight: 600; }
        .btn-accent:hover { background: #e08c45; color: #13253b; }
        .table thead th { font-size: .78rem; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; }
    </style>
</head>
<body>
<div class="d-flex">
    <aside class="sidebar p-3 d-none d-lg-flex flex-column">
        <div class="brand mb-4 px-2">
            <div class="fw-bold fs-5">Gudang Damage</div>
            <small>Pengelolaan barang rusak</small>
        </div>
        <nav class="nav flex-column gap-1 flex-grow-1">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            <a class="nav-link {{ request()->routeIs('damages.*') ? 'active' : '' }}" href="{{ route('damages.index') }}"><i class="bi bi-exclamation-octagon me-2"></i>Barang Damage</a>
            <a class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}" href="{{ route('items.index') }}"><i class="bi bi-box-seam me-2"></i>Master Barang</a>
            <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><i class="bi bi-tags me-2"></i>Kategori</a>
            <a class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}" href="{{ route('locations.index') }}"><i class="bi bi-geo-alt me-2"></i>Lokasi Gudang</a>
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button class="btn btn-outline-light w-100" type="submit"><i class="bi bi-box-arrow-left me-1"></i>Keluar</button>
        </form>
    </aside>

    <div class="content-wrap flex-grow-1">
        <header class="topbar px-3 px-lg-4 py-3 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h5 mb-0">@yield('title', 'Dashboard')</h1>
                <div class="text-muted small">Pengembangan Website Pengelolaan Barang Damage pada Gudang</div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                    <div class="small text-muted text-capitalize">{{ auth()->user()->role }}</div>
                </div>
                <div class="d-lg-none">
                    <a href="{{ route('damages.index') }}" class="btn btn-sm btn-accent">Menu</a>
                </div>
            </div>
        </header>

        <main class="p-3 p-lg-4">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

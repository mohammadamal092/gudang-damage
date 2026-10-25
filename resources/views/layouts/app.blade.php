<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/gudang.css') }}" rel="stylesheet">
    @stack('head')
</head>
<body>
@php
    $user = auth()->user();
    $initial = strtoupper(mb_substr($user->name, 0, 1));
@endphp
<div class="d-flex">
    {{-- Sidebar desktop --}}
    <aside class="sidebar p-3 d-none d-lg-flex flex-column">
        <div class="d-flex align-items-center gap-2 px-2 mb-3">
            <div class="brand-logo"><i class="bi bi-boxes"></i></div>
            <div>
                <div class="brand-name">Gudang Damage</div>
                <div class="brand-sub">Pengelolaan barang rusak</div>
            </div>
        </div>
        <div class="flex-grow-1 overflow-auto">
            @include('partials.nav')
        </div>
        <div class="user-chip d-flex align-items-center gap-2 mt-3">
            <div class="avatar">{{ $initial }}</div>
            <div class="flex-grow-1" style="min-width:0">
                <div class="text-white fw-semibold text-truncate" style="font-size:.88rem">{{ $user->name }}</div>
                <div class="text-capitalize" style="font-size:.74rem;color:#8fa6bd">{{ $user->role }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm text-white-50 p-1" type="submit" title="Keluar"><i class="bi bi-box-arrow-right fs-5"></i></button>
            </form>
        </div>
    </aside>

    {{-- Sidebar mobile --}}
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileNav" style="background:var(--gd-navy);color:#dbe7f3;width:280px">
        <div class="offcanvas-header">
            <div class="d-flex align-items-center gap-2">
                <div class="brand-logo"><i class="bi bi-boxes"></i></div>
                <span class="fw-bold text-white">Gudang Damage</span>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body pt-0 mobile-nav">
            @include('partials.nav')
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button class="btn btn-outline-light w-100" type="submit"><i class="bi bi-box-arrow-left me-1"></i>Keluar</button>
            </form>
        </div>
    </div>

    <div class="content-wrap flex-grow-1">
        <header class="topbar px-3 px-lg-4 py-3 d-flex justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-soft btn-icon d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-label="Menu">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <h1 class="h5 page-title mb-0">@yield('title', 'Dashboard')</h1>
                    <div class="text-muted small d-none d-sm-block">@yield('subtitle', now()->translatedFormat('l, d F Y'))</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @yield('actions')
            </div>
        </header>

        <main class="p-3 p-lg-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i><div class="flex-grow-1">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i><div class="flex-grow-1">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.alert-success').forEach(el => setTimeout(() => bootstrap.Alert.getOrCreateInstance(el).close(), 4500));
</script>
@stack('scripts')
</body>
</html>

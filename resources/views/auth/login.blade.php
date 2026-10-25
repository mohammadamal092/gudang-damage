<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/gudang.css') }}" rel="stylesheet">
</head>
<body>
<div class="login-shell">
    <section class="login-hero">
        <div class="d-flex align-items-center gap-2" style="position:relative;z-index:1">
            <div class="avatar" style="border-radius:12px;width:44px;height:44px;font-size:1.3rem"><i class="bi bi-boxes"></i></div>
            <span class="fw-bold fs-5">Gudang Damage</span>
        </div>
        <div style="position:relative;z-index:1">
            <h2 class="fw-bold mb-3" style="font-size:2.2rem;letter-spacing:-.02em;line-height:1.15">Kelola barang rusak<br>lebih rapi &amp; terlacak.</h2>
            <p class="mb-4" style="color:#a9bdd1;max-width:430px">Catat kerusakan, pantau tindak lanjut, dan jaga stok gudang tetap akurat dalam satu tempat.</p>
            <div class="feature"><i class="bi bi-clipboard2-check"></i><div><div class="fw-semibold">Tiket damage otomatis</div><div class="small" style="color:#a9bdd1">Setiap laporan punya kode unik dan riwayat status.</div></div></div>
            <div class="feature"><i class="bi bi-arrow-left-right"></i><div><div class="fw-semibold">Stok tersinkron</div><div class="small" style="color:#a9bdd1">Stok berkurang/kembali otomatis sesuai status.</div></div></div>
            <div class="feature"><i class="bi bi-bar-chart-line"></i><div><div class="fw-semibold">Dashboard &amp; laporan</div><div class="small" style="color:#a9bdd1">Tren, jenis kerusakan, dan ekspor ke Excel.</div></div></div>
        </div>
        <div class="small" style="color:#6f879f;position:relative;z-index:1">&copy; {{ date('Y') }} Gudang Damage</div>
    </section>

    <section class="login-panel">
        <div class="w-100" style="max-width:420px">
            <div class="mb-4">
                <h1 class="h3 fw-bold mb-1">Selamat datang 👋</h1>
                <p class="text-muted mb-0">Masuk untuk mengelola barang damage.</p>
            </div>
            <div class="card p-4">
                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <div class="input-icon">
                            <i class="bi bi-envelope"></i>
                            <input id="email" type="email" name="email" value="{{ old('email', 'admin@gudang.test') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
                        </div>
                        @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Kata sandi</label>
                        <div class="input-icon">
                            <i class="bi bi-lock"></i>
                            <input id="password" type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small" for="remember">Ingat saya</label>
                    </div>
                    <button class="btn btn-navy w-100 py-2 fw-semibold" type="submit">Masuk <i class="bi bi-arrow-right ms-1"></i></button>
                </form>
            </div>
            <p class="small text-muted mt-3 mb-0 text-center">Akun demo: <strong>admin@gudang.test</strong> / <strong>password</strong></p>
        </div>
    </section>
</div>
</body>
</html>

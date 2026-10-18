<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: "DM Sans", sans-serif; min-height: 100vh; background: linear-gradient(135deg, #13253b, #1f3b5a 55%, #f4a261); }
        .login-card { max-width: 440px; border: 0; border-radius: 20px; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3">
    <div class="card login-card shadow-lg p-4 p-md-5 w-100">
        <div class="mb-4">
            <div class="text-uppercase small fw-semibold" style="color:#f4a261">Gudang Damage</div>
            <h1 class="h3 fw-bold mb-1">Masuk ke sistem</h1>
            <p class="text-muted mb-0">Pengelolaan barang rusak pada gudang</p>
        </div>
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', 'admin@gudang.test') }}" class="form-control @error('email') is-invalid @enderror" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Kata sandi</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            <button class="btn w-100 text-white fw-semibold py-2" style="background:#13253b" type="submit">Masuk</button>
        </form>
        <p class="small text-muted mt-4 mb-0">Akun demo: <strong>admin@gudang.test</strong> / <strong>password</strong></p>
    </div>
</body>
</html>

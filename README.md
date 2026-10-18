# Pengembangan Website Pengelolaan Barang Damage pada Gudang

Aplikasi Laravel untuk mencatat dan menindaklanjuti barang rusak di gudang. Dijalankan lewat **Laragon**.

## URL lokal
Setelah Apache Laragon di-start/reload:

- http://gudang-damage.test

## Akun demo
- Email: `admin@gudang.test`
- Password: `password`

## Fitur
- Login petugas/admin
- Dashboard ringkasan damage & stok menipis
- Master kategori, lokasi gudang, dan barang
- CRUD laporan damage (status: dilaporkan, diproses, diperbaiki, diretur, dimusnahkan)
- Stok otomatis berkurang saat damage dicatat, dan kembali jika status menjadi diperbaiki

## Setup ulang
1. Pastikan Apache + MySQL Laragon menyala.
2. Database: `gudang_damage` (user `root`, password kosong).
3. Di folder `C:\laragon\www\gudang-damage`:

```
php artisan migrate --seed
php artisan storage:link
```

Proyek memakai PHP Laragon 8.5 dan MySQL Laragon (port 3306).

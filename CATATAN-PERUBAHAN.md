# Catatan Perubahan — Gudang Damage

Ekstrak zip ini ke folder proyek (timpa file yang sama), lalu jalankan:

    php artisan view:clear
    php artisan route:clear

Tidak ada migrasi baru. Tidak perlu `npm run build` (CSS memakai public/css/gudang.css).

## Tampilan
- public/css/gudang.css (BARU) — gaya bersama: kartu, badge status, tabel, chip filter, empty state.
- Layout: sidebar bergrup + badge jumlah laporan terbuka, menu mobile (offcanvas), avatar user, alert bisa ditutup & hilang otomatis.
- Dashboard: kartu statistik berikon, grafik tren 6 bulan, donat jenis kerusakan, progress status & stok.
- Login: tampilan dua kolom. Halaman Damage/Barang/Kategori/Lokasi: pencarian berikon, tombol ikon, jumlah data, tampilan kosong.

## Fungsi baru
1. Status "Dimusnahkan" (ada di README & badge, tapi sebelumnya belum ada di daftar STATUSES).
2. Filter laporan: jenis kerusakan + rentang tanggal; chip status berisi jumlah.
3. Ekspor CSV (mengikuti filter aktif, terbuka di Excel).
4. Ubah status cepat di halaman detail (stok ikut disesuaikan, ada validasi stok).
5. Cetak Berita Acara per tiket (Simpan sebagai PDF dari browser).
6. Peringatan laporan terbuka > 3 hari (dashboard & daftar).
7. Form damage: stok tersedia tampil otomatis, lokasi terisi otomatis, pratinjau foto.
8. Filter "Stok menipis" di Master Barang.

## Perbaikan kecil
- Validasi stok saat tambah laporan kini tidak memblokir status "Diperbaiki" (konsisten dengan form ubah).
- Bahasa tanggal Indonesia (Carbon locale 'id') dan zona waktu default Asia/Jakarta.

# Sistem Informasi Apotek (CodeIgniter 3)

## Ringkasan Proyek
Aplikasi manajemen apotek berbasis web menggunakan CodeIgniter 3. Fokus pada operasional harian (POS penjualan, stok), SDM (absensi & shift), pelaporan, dan pengaturan dengan kontrol akses berbasis peran (Admin vs Apoteker).

## Teknologi
- PHP 7.4+ (CI3 kompatibel 5.6+, disarankan 7.4)
- CodeIgniter 3 (MVC)
- MySQL/MariaDB
- Bootstrap 5, Bootstrap Icons (CDN)
- DataTables (jsDelivr CDN)
- Chart.js (jsDelivr CDN)

## Fitur Utama (Checklist)
- [x] Dashboard
- [x] Master Data
  - [x] Obat (CRUD)
  - [x] Pengguna (CRUD, admin-only)
- [x] Transaksi
  - [x] POS Penjualan (cart, checkout, invoice)
  - [x] Riwayat Penjualan
- [x] Stok
  - [x] Catat Stok In/Out/Opname
  - [x] Daftar & Pergerakan Stok, Low Stock Alert
- [x] Absensi
  - [x] Checkin/Checkout
  - [x] Laporan Absensi (bulanan, per-user, statistik telat/absen)
- [x] Shift
  - [x] Kalender Jadwal Shift (events)
  - [x] Aturan Penugasan Shift (CRUD, admin-only)
  - [x] Auto-assign attendances saat create/edit shift
  - [ ] Export/Import CSV Aturan (placeholder, akan dilanjutkan)
- [x] Laporan
  - [x] Penjualan (tren, top products, per-kategori, CSV export)
  - [x] Stok (current value, movements, reorder list, CSV export)
  - [x] Absensi (grafik harian, rekap per-user, CSV export)
- [x] Pengaturan
  - [x] Settings (admin-only): ringkasan & API Keys
  - [x] Profile (ubah nama/username)
  - [x] Ubah Password
- [x] Akses & Keamanan
  - [x] Guard controller untuk aksi tulis/admin-only
  - [x] Visibilitas menu berbasis role (sidebar/header)

## Role & Akses
- Admin: Akses penuh termasuk Users, Shift Rules, Settings, semua aksi tulis.
- Apoteker: Akses operasional (POS, stok, absensi, laporan dasar), tanpa akses modul admin-only.
Rincian lengkap ada di `docs/menu-dan-fitur-permission.md`.

## Persyaratan Sistem
- PHP 7.4+ dengan ekstensi: mysqli, mbstring, json, openssl, curl
- MySQL 5.7+ / MariaDB 10.3+
- Web server (Apache/Nginx) atau PHP built-in server
- Composer (opsional) jika ingin menambah dependensi pihak ketiga

## Instalasi
1) Clone repository
```
git clone https://github.com/dawamr/sistem-informasi-apotek.git
cd sistem-informasi-apotek
```

2) Konfigurasi aplikasi
- `application/config/config.php`
  - Set `base_url` sesuai environment Anda
  - Set `$config['encryption_key']` dengan string acak yang kuat
- `application/config/database.php`
  - Isi kredensial database (hostname, username, password, database)
- (Opsional) `application/config/session.php` sesuai kebutuhan sesi Anda

3) Siapkan database
- Buat database kosong (mis. `apotek_db`)
- Import skema/sql awal jika tersedia di dokumentasi internal Anda
  - Jika belum ada, buat tabel sesuai kebutuhan modul (rujuk model-model di `application/models`)

4) Izin folder writable
- Pastikan folder berikut writable oleh web server:
  - `application/cache`
  - `application/logs`

5) Jalankan aplikasi
- Via Apache/Nginx (direkomendasikan) atau
- PHP built-in server (untuk pengembangan):
```
php -S 127.0.0.1:8000 -t .
```
Akses: `http://127.0.0.1:8000/index.php/auth` (atau tanpa `index.php` jika rewrite aktif)

6) Buat akun Admin
- Insert user admin ke tabel `users` secara manual (role = `admin`)
- Simpan password dengan `password_hash()` (PHP):
```
php -r "echo password_hash('your_password', PASSWORD_BCRYPT), PHP_EOL;"
```
Masukkan hasil hash ke kolom password_hash.

## Navigasi Penting
- POS: `/sales/pos`
- Stok: `/stock`
- Absensi: `/attendance`
- Shift: `/shifts` dan aturan `/shifts/rules` (admin-only)
- Laporan: `/reports/sales`, `/reports/stock`, `/reports/attendance`
- Settings (admin-only): `/settings`, API Keys: `/settings/api-keys`
- Profile & Password: `/settings/profile`, `/settings/password`

## API (contoh)
- Health/Test: `GET /api/v1/test`
  - Respon sukses: `{"status":"success","message":"API is working!"}`

## Struktur Direktori (singkat)
- `application/controllers` — Controller web & API
- `application/models` — Akses data & logika bisnis
- `application/views` — Template, halaman, dan partials (header, sidebar, footer)
- `application/config` — Konfigurasi CI3
- `docs` — Dokumentasi proyek

## Troubleshooting
- Flash message logout tidak muncul: pastikan helper `cookie` ter-load dan urutan unset/regenerate/session flash sudah benar (sudah diperbaiki di `Auth::logout`).
- 403/redirect saat akses: periksa role pada session (`role=admin` untuk admin-only), dan guard di controller.
- Base URL / index.php: set `base_url` dan aktifkan rewrite agar URL bersih.
- Kolom database berbeda (mis. `is_active` tidak ada): filter kondisional sudah diterapkan di beberapa query; sesuaikan skema Anda bila perlu.

## Lisensi
- Mengacu pada lisensi CodeIgniter 3 (MIT-style). Konten aplikasi mengikuti lisensi proyek ini.

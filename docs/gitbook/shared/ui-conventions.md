# Pedoman UI

Panduan ini merangkum konvensi UI yang dipakai aplikasi.

## Framework & Aset
- Bootstrap 5 (CDN) & Bootstrap Icons
- DataTables (CDN) untuk tabel interaktif
- Chart.js (CDN) untuk grafik
- Custom CSS: `assets/css/custom.css`

## Layout & Navigasi
- Header navbar gelap dengan dropdown user (Profile, Logout)
- Sidebar tetap (fixed) di kiri, navigasi modul
- Konten utama berada di `.main-content`
- Responsif: pada layar kecil, sidebar menjadi relative penuh lebar

## Komponen Umum
- Buttons: gunakan skema Bootstrap (`btn-primary`, `btn-success`, `btn-danger`, dll.)
- Forms: gunakan `.form-control` dan validasi sisi server (flashdata untuk error/sukses)
- Alerts/Flash Message: `success`, `danger`, `warning`, `info`; diletakkan di atas konten utama
- Tables: gunakan DataTables bila perlu sorting/paging/search; gunakan tabel Bootstrap jika statis
- Icons: Bootstrap Icons (mis. `bi bi-capsule`, `bi bi-gear`)

## Charts
- Chart.js dimuat via footer (template), gunakan canvas dengan id unik per halaman
- Warna chart mengikuti palet Bootstrap bila memungkinkan

## Scripts Per Halaman
- Injeksi melalui `$data['page_scripts']` pada view footer template (jika tersedia)
- Hindari inline script panjang di view utama; tempatkan di berkas JS terpisah bila kompleks

## Akses Berbasis Peran di UI
- Sidebar menyembunyikan menu admin-only bagi Apoteker
- Tombol aksi tulis (create/edit/delete) hanya ditampilkan untuk role yang berwenang
- Header tidak menampilkan Settings; Profile & Logout tersedia untuk semua user login

## Tabel & Pagination
- Gunakan DataTables untuk dataset besar
- Tampilkan kolom penting terlebih dahulu; kolom opsional dapat disembunyikan pada resolusi kecil

## Gambar & Media
- Simpan di `docs/gitbook/images`
- Penamaan: `modul-fitur-deskripsi.png` (contoh: `stock-reorder-list.png`)
- Tambahkan caption pendek dan referensi langkah/komponen

## Aksesibilitas (Singkat)
- Gunakan teks alternatif untuk ikon/gambar penting
- Pastikan kontras memadai dan ukuran font terbaca

## Contoh Penempatan Flash Message (PHP)
```php
<?php if ($this->session->flashdata('success')): ?>
  <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
<?php endif; ?>
<?php if ($this->session->flashdata('danger')): ?>
  <div class="alert alert-danger"><?= $this->session->flashdata('danger') ?></div>
<?php endif; ?>
```

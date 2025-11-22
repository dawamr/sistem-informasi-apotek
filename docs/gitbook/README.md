# GitBook Docs — Sistem Informasi Apotek

Dokumentasi ini menyajikan panduan per fitur secara terstruktur untuk GitBook: fungsi, penggunaan, alur data, skema data, dan referensi kode. Gunakan Template Fitur untuk konsistensi.

## Struktur Direktori (rekomendasi)
- features/
  - medicines/
    - overview.md
    - crud.md
  - users/
    - overview.md
  - sales/
    - pos.md
    - history.md
    - checkout-invoice.md
  - stock/
    - operations.md
    - reports.md
  - attendance/
    - daily.md
    - reports.md
  - shifts/
    - calendar.md
    - rules.md
    - setup.md
    - csv.md
  - reports/
    - sales.md
    - stock.md
    - attendance.md
  - settings/
    - profile.md
    - password.md
    - api-keys.md
  - access/
    - rbac.md
  - api/
    - index.md
- shared/
  - data-model.md (opsional ringkasan skema)
  - ui-conventions.md (opsional)
- TODO.md
- FEATURE_TEMPLATE.md

## Cara Menambahkan Dokumen Fitur
1. Duplikat `FEATURE_TEMPLATE.md` ke lokasi yang relevan di folder `features/`.
2. Isi semua bagian minimal: Overview, Permissions, Usage, Routes, Data Flow, Schema, Code Refs, Testing.
3. Jika fitur kompleks, pecah menjadi beberapa file (mis. `overview.md`, `usage.md`, `advanced.md`).
4. Tambahkan tautan antar-file agar navigasi mudah di GitBook.

## Pedoman Penulisan
- Bahasa: Indonesia (singkat, jelas, konsisten).
- Sertakan potongan kode (controller/model/view) seperlunya, hindari terlalu panjang.
- Gunakan diagram mermaid untuk alur data.
- Tampilkan permission (Admin/Apoteker) di setiap fitur.
- Cantumkan rujukan file & baris jika spesifik.

## Media & Images
- Simpan aset gambar di `docs/gitbook/images`.
- Penamaan file: `modul-fitur-deskripsi.png` (snake/kebab-case, deskriptif), contoh: `sales-pos-overview.png`.
- Sertakan caption singkat di bawah gambar dan titik referensi (langkah ke-berapa, komponen apa).
- Resolusi disarankan 1280px lebar (agar jelas di GitBook) dan kompresi wajar.

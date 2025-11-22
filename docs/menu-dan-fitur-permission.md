# Menu dan Fitur — Permission Matrix (Admin vs Apoteker)

Dokumen ini mendefinisikan akses menu/fitur untuk dua peran utama: Admin dan Apoteker.
Jika tidak disebutkan berbeda, akses default: Baca: Apoteker & Admin, Tulis: Admin.

## Ringkas
- Admin: Full akses (semua menu & aksi CRUD), konfigurasi, manajemen user & API keys.
- Apoteker: Operasional harian (POS/penjualan, stok in/out & opname, absensi, melihat laporan), tanpa hak konfigurasi/administrasi.

---

## Navigasi Utama
- Dashboard
  - Admin: View
  - Apoteker: View

- Medicines (Master Obat)
  - List/Search: Admin, Apoteker
  - Create/Edit/Delete: Admin

- Users (Master Pengguna)
  - List: Admin
  - Create/Edit/Activate/Deactivate/Delete: Admin
  - Apoteker: Tidak ada akses menu Users

- Sales (Penjualan/POS)
  - POS (sales/pos): Admin, Apoteker (input transaksi)
  - Search/Add/Update/Remove item di cart: Admin, Apoteker
  - Checkout/Invoice/Print: Admin, Apoteker
  - History: Admin, Apoteker (view)
  - Edit/Delete transaksi yang sudah tersimpan: Admin

- Stock
  - Stock In/Out: Admin, Apoteker (operasional)
  - Stock Opname: Admin, Apoteker (operasional)
  - Riwayat Mutasi: Admin, Apoteker (view)
  - Koreksi stok manual (di luar opname): Admin

- Shifts
  - Calendar/Listing: Admin, Apoteker (view)
  - Create/Edit/Delete Shift: Admin
  - Assign shift to user: Admin
  - Setup mingguan & Rules: Admin

- Attendance (Absensi)
  - Daily list & Check-in/Check-out: Admin, Apoteker (check-in/out hanya diri sendiri)
  - Edit status absensi (izin/sakit/alpha): Admin
  - Export (jika ada): Admin

- Reports
  - Sales Reports: Admin, Apoteker (view & export CSV)
  - Stock Reports: Admin, Apoteker (view)
  - Attendance Reports: Admin, Apoteker (view)
  - Export PDF/Excel (jika diaktifkan): Admin

- Settings
  - Settings (index): Admin
  - Profile: Admin, Apoteker (hanya akun sendiri)
  - Change Password: Admin, Apoteker (hanya akun sendiri)
  - API Keys: Admin (CRUD)

- API (N8N/Integrasi)
  - Menggunakan X-API-KEY: berdasarkan API key yang aktif, tidak terkait role UI.
  - Pembuatan/penonaktifan API key: Admin.

---

## Detail Aksi per Modul

### Medicines
- View/List/Search: Admin, Apoteker
- Create/Edit/Delete obat: Admin
- Low stock list (view): Admin, Apoteker

### Users
- View/List: Admin
- Create/Edit/Activate/Deactivate/Delete user: Admin

### Sales/POS
- Input transaksi (POS): Admin, Apoteker
- Edit/retur atau void transaksi tersimpan: Admin
- Cetak invoice: Admin, Apoteker

### Stock
- Stock in/out (operasional penerimaan/pengeluaran): Admin, Apoteker
- Stock opname (periodik): Admin, Apoteker
- Koreksi stok manual (ad-hoc): Admin
- Lihat mutasi stok: Admin, Apoteker

### Shifts & Attendance
- Lihat jadwal: Admin, Apoteker
- Setup jadwal mingguan & Rules: Admin
- Assign shift: Admin
- Check-in/Check-out: Admin, Apoteker (hanya akun sendiri)
- Edit status (izin/sakit/alpha) & koreksi waktu: Admin

### Reports
- Sales/Stock/Attendance (view): Admin, Apoteker
- Export CSV (ringkas & dataset): Admin, Apoteker
- Export Excel/PDF (bila diaktifkan): Admin

### Settings
- Settings Index (ringkasan & low stock alerts): Admin
- Profile (ubah nama/username sendiri): Admin, Apoteker (hanya dirinya)
- Change Password (ubah password sendiri): Admin, Apoteker (hanya dirinya)
- API Keys (buat/nonaktif/hapus): Admin

---

## Catatan Implementasi Teknis
- Middleware/guard: pastikan controller aksi tulis (create/update/delete) memeriksa `role === 'admin'`.
- Aksi yang hanya untuk pemilik akun (Profile/Password) harus memeriksa `user_id` dari session.
- Endpoint API menggunakan validasi X-API-KEY; kontrol UI role tidak berlaku untuk API.
- Menu Sidebar: sembunyikan tautan admin-only untuk Apoteker.

---

Diperbarui: <?= date('Y-m-d') ?>

---

## TODO: Implementasi Access Control (Views, Controllers, Routes)

- [ ] Views
  - Sembunyikan menu/tautan khusus Admin pada sidebar dan halaman untuk role Apoteker.
  - Tampilkan tombol aksi (Create/Edit/Delete, Assign, Setup, Export PDF/Excel, API Keys) hanya untuk Admin.
  - Validasi UI: jika user non-admin mengakses halaman admin-only, tampilkan pesan error atau redirect.

- [ ] Controllers
  - Tambahkan guard role di setiap aksi tulis (create/update/delete) dan halaman admin-only:
    - Periksa `session('role') === 'admin'` sebelum proses.
    - Untuk aksi milik user (Profile/Password), validasi `session('user_id')` sesuai target.
  - Standarisasi respon: 403 Forbidden atau redirect ke halaman sebelumnya dengan flash message.

- [ ] Routes
  - Pastikan semua route admin-only diarahkan ke controller yang sudah memiliki guard.
  - (Opsional) Prefix/namespace untuk admin (mis. `admin/*`) agar lebih mudah diproteksi di masa depan.

- [ ] Dokumentasi & Uji
  - Perbarui README/Docs untuk menjelaskan kebijakan akses.
  - Tambahkan test manual (checklist) untuk tiap modul sesuai matriks di atas.

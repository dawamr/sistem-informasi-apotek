# TODO — GitBook Dokumentasi

Centang item saat dokumen selesai. Setiap item minimal memuat: Overview, Permissions, UI/Screens, Routes, Usage, Data Flow, Schema, Code Refs (Controllers/Models/Views/Routes), API (jika ada), CSV (jika ada), Error Handling, Testing.

- [ ] Dasar
  - [x] Konvensi Penulisan & Struktur GitBook (README ini)
  - [x] Ringkasan Skema Data (shared/data-model.md)
  - [x] Pedoman UI (shared/ui-conventions.md)

- [ ] Dashboard
  - [ ] Overview & Navigasi

- [ ] Master Data — Medicines
  - [ ] Overview
  - [ ] CRUD Obat (create/edit/delete) — controller: `Medicines.php`, model: `Medicine_model.php`, view: `views/medicines/*`
  - [ ] Validasi & Edge Cases

- [ ] Master Data — Users (Admin-only)
  - [ ] Overview
  - [ ] CRUD Users — controller: `Users.php`, model: `User_model.php`, view: `views/users/*`
  - [ ] Guard Admin & Error Handling

- [ ] Sales
  - [ ] POS — controller: `Sales.php` (cart: search/add/update/remove), view: `views/sales/pos.php`
  - [ ] Checkout & Invoice — controller: `Sales.php` (checkout, invoice), view: `views/sales/*`
  - [ ] History — controller: `Sales.php` (history)
  - [ ] Data Flow & Schema: `sales`, `sale_items`, relasi ke `medicines`

- [ ] Stock
  - [ ] Operasional (In/Out/Opname) — controller: `Stock.php`, model: `Stock_model.php`, view: `views/stock/*`
  - [ ] Logs & Reorder List
  - [ ] Laporan Stok — controller: `Reports.php` (stock), view: `views/reports/stock.php`

- [ ] Attendance
  - [ ] Daily (checkin/checkout, `_can_modify`) — controller: `Attendance.php`, model: `Attendance_model.php`
  - [ ] Laporan Absensi Bulanan — controller: `Reports.php` (attendance), view: `views/reports/attendance.php`

- [ ] Shifts
  - [ ] Calendar Events — controller: `Shifts.php` (events, listing)
  - [ ] Aturan Penugasan (CRUD, admin-only) — controller: `Shifts.php` (rules_*), table: `shift_rules`
  - [ ] Assign & Auto-Assign Attendances — controller: `Shifts.php` (assign, create/edit)
  - [ ] Weekly Setup — controller: `Shifts.php` (setup)
  - [ ] CSV Export/Import (placeholder) — controller: `Shifts.php` (rules_export/rules_import)

- [ ] Reports
  - [ ] Sales — controller: `Reports.php` (sales), model: `Sale_model.php`, `Sale_item_model.php`, view: `views/reports/sales.php`
  - [ ] Stock — controller: `Reports.php` (stock), model: `Stock_model.php`, view: `views/reports/stock.php`
  - [ ] Attendance — controller: `Reports.php` (attendance), model: `Attendance_model.php`, view: `views/reports/attendance.php`

- [ ] Settings
  - [ ] Settings (admin-only) — controller: `Settings.php` (index, api_keys), view: `views/settings/*`
  - [ ] Profile — controller: `Settings.php` (profile), view: `views/settings/profile.php`
  - [ ] Password — controller: `Settings.php` (password), view: `views/settings/password.php`

- [ ] Access Control & Permissions
  - [ ] Matriks Akses — `docs/menu-dan-fitur-permission.md`
  - [ ] Implementasi Guard — controllers (Users, Medicines, Shifts, Settings, dst.)
  - [ ] Visibilitas Menu — `views/templates/sidebar.php`, `views/templates/header.php`

- [ ] API
  - [ ] Daftar Endpoint & Contoh
    - [ ] `/api/v1/sales/*` — summary_daily, items_by_day, top_products
    - [ ] `/api/v1/attendance/*` — shift_today, summary
    - [ ] `/api/v1/visits/summary`
    - [ ] `/api/v1/stock/check`
    - [ ] `/api/v1/health`
    - [ ] `/api/v1/test` (ping)

- [ ] CSV & Ekspor
  - [ ] Format CSV Laporan
  - [ ] Export/Import Shift Rules (saat implementasi)

- [ ] Troubleshooting & Testing
  - [ ] Error umum (DB kolom berbeda, flash message, session/cookie)
  - [ ] Checklist uji per modul

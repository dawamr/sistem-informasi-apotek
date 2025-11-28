# Sistem Informasi Apotek CI3 - API Documentation

Dokumentasi API endpoints untuk Sistem Informasi Apotek berbasis CodeIgniter 3.

**Base URL:** `https://apotek.codeai.my.id`  
**API Key:** `sk_n8n_apotek_2025_test_key_12345`

---

## Authentication

Semua endpoint memerlukan header:
```
X-API-KEY: sk_n8n_apotek_2025_test_key_12345
```

---

## Sales Endpoints

### 1. Daily Sales Summary

**Endpoint:** `GET /api/v1/sales/summary/daily`

**Deskripsi:** Mendapatkan ringkasan penjualan harian untuk tanggal tertentu.

**Query Parameters:**
- `date` (string, optional): Tanggal dalam format YYYY-MM-DD (contoh: 2025-02-21). Jika tidak diisi, menggunakan tanggal hari ini.

**Headers:**
```
X-API-KEY: {{api_key}}
```

**Contoh Request:**
```
GET {{base_url}}/api/v1/sales/summary/daily?date=2025-02-21
```

**Use Case:**
- Melihat total penjualan harian
- Monitoring performa penjualan per hari
- Laporan keuangan harian

---

### 2. Items Sold Per Day

**Endpoint:** `GET /api/v1/sales/items-by-day`

**Deskripsi:** Mendapatkan daftar item/obat yang terjual pada hari tertentu beserta kuantitasnya.

**Query Parameters:**
- `date` (string, optional): Tanggal dalam format YYYY-MM-DD (contoh: 2025-02-21). Jika tidak diisi, menggunakan tanggal hari ini.

**Headers:**
```
X-API-KEY: {{api_key}}
```

**Contoh Request:**
```
GET {{base_url}}/api/v1/sales/items-by-day?date=2025-02-21
```

**Use Case:**
- Melihat detail barang yang terjual per hari
- Analisis produk yang laku terjual
- Restock planning berdasarkan penjualan harian

---

### 3. Top Products

**Endpoint:** `GET /api/v1/sales/top-products`

**Deskripsi:** Mendapatkan daftar produk dengan penjualan tertinggi berdasarkan periode tertentu.

**Query Parameters:**
- `period` (string, required): Periode analisis (daily, weekly, monthly)
- `date` (string, optional): Tanggal referensi dalam format YYYY-MM-DD (contoh: 2025-02-21)
- `limit` (integer, optional): Jumlah maksimal produk yang ditampilkan (default: 10)

**Headers:**
```
X-API-KEY: {{api_key}}
```

**Contoh Request:**
```
GET {{base_url}}/api/v1/sales/top-products?period=daily&date=2025-02-21&limit=10
```

**Use Case:**
- Identifikasi produk best seller
- Strategi inventory management
- Promosi produk populer
- Analisis trend penjualan

---

## Attendance Endpoints

### 1. Shift Today

**Endpoint:** `GET /api/v1/attendance/shift-today`

**Deskripsi:** Mendapatkan informasi staff yang bertugas pada hari tertentu beserta shift-nya.

**Query Parameters:**
- `date` (string, optional): Tanggal dalam format YYYY-MM-DD (contoh: 2025-02-21). Jika tidak diisi, menggunakan tanggal hari ini.

**Headers:**
```
X-API-KEY: {{api_key}}
```

**Contoh Request:**
```
GET {{base_url}}/api/v1/attendance/shift-today?date=2025-02-21
```

**Use Case:**
- Mengetahui siapa saja yang bertugas hari ini
- Manajemen shift karyawan
- Monitoring kehadiran real-time

---

### 2. Attendance Summary

**Endpoint:** `GET /api/v1/attendance/summary`

**Deskripsi:** Mendapatkan ringkasan kehadiran karyawan untuk tanggal tertentu, termasuk jumlah hadir, izin, sakit, dan tidak hadir.

**Query Parameters:**
- `date` (string, optional): Tanggal dalam format YYYY-MM-DD (contoh: 2025-02-21). Jika tidak diisi, menggunakan tanggal hari ini.

**Headers:**
```
X-API-KEY: {{api_key}}
```

**Contoh Request:**
```
GET {{base_url}}/api/v1/attendance/summary?date=2025-02-21
```

**Use Case:**
- Rekap kehadiran harian
- Laporan absensi untuk HR
- Monitoring disiplin karyawan
- Perhitungan payroll

---

## Visits Endpoints

### 1. Visits Summary

**Endpoint:** `GET /api/v1/visits/summary`

**Deskripsi:** Mendapatkan ringkasan total kunjungan pasien/pelanggan ke apotek pada tanggal tertentu.

**Query Parameters:**
- `date` (string, optional): Tanggal dalam format YYYY-MM-DD (contoh: 2025-02-21). Jika tidak diisi, menggunakan tanggal hari ini.

**Headers:**
```
X-API-KEY: {{api_key}}
```

**Contoh Request:**
```
GET {{base_url}}/api/v1/visits/summary?date=2025-02-21
```

**Use Case:**
- Monitoring trafik pelanggan harian
- Analisis jam ramai apotek
- Perencanaan staffing berdasarkan volume kunjungan
- Evaluasi efektivitas promosi

---

## Stock Endpoints

### 1. Check Stock by Code

**Endpoint:** `GET /api/v1/stock/check`

**Deskripsi:** Memeriksa ketersediaan stok obat berdasarkan kode produk.

**Query Parameters:**
- `code` (string, required): Kode unik produk obat (contoh: OBT001)

**Headers:**
```
X-API-KEY: {{api_key}}
```

**Contoh Request:**
```
GET {{base_url}}/api/v1/stock/check?code=OBT001
```

**Use Case:**
- Cek stok spesifik obat dengan kode produk
- Verifikasi ketersediaan sebelum penjualan
- Inventory tracking per kode produk

---

### 2. Search Stock by Name

**Endpoint:** `GET /api/v1/stock/check`

**Deskripsi:** Mencari dan memeriksa ketersediaan stok obat berdasarkan nama produk (pencarian menggunakan keyword).

**Query Parameters:**
- `q` (string, required): Keyword pencarian nama obat (contoh: paracetamol)

**Headers:**
```
X-API-KEY: {{api_key}}
```

**Contoh Request:**
```
GET {{base_url}}/api/v1/stock/check?q=paracetamol
```

**Use Case:**
- Pencarian obat berdasarkan nama
- Cek ketersediaan tanpa mengetahui kode produk
- Customer service untuk informasi stok
- Pencarian fuzzy untuk nama obat yang mirip

---

## Common Response Format

Semua endpoint mengikuti format response standar:

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    // data response sesuai endpoint
  },
  "message": "Request berhasil"
}
```

**Error Response (4xx/5xx):**
```json
{
  "status": "error",
  "message": "Deskripsi error",
  "code": "ERROR_CODE"
}
```

---

## Error Codes

- `401 Unauthorized`: API key tidak valid atau tidak diberikan
- `404 Not Found`: Endpoint atau resource tidak ditemukan
- `400 Bad Request`: Parameter request tidak valid
- `500 Internal Server Error`: Kesalahan server internal

---

## Variables

Gunakan variables berikut untuk testing:

- **base_url**: `http://localhost:8081`
- **api_key**: `sk_n8n_apotek_2025_test_key_12345`
- **date**: `2025-02-21` (format: YYYY-MM-DD)

---

## Notes

1. Semua endpoint menggunakan method `GET`
2. Parameter `date` bersifat optional, jika tidak diisi akan menggunakan tanggal hari ini
3. Response format dalam JSON
4. Timezone mengikuti server (default: UTC+7 / WIB)
5. Rate limiting: Tidak ada batasan untuk development, production akan dibatasi sesuai plan

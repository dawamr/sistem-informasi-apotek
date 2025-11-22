## 1. Project Overview – Aplikasi Apoteker Sederhana

### 1.1. Tujuan Aplikasi

Aplikasi ini adalah **sistem informasi apotek sederhana** berbasis **CodeIgniter 3 (CI3)** yang fokus ke:

* Pencatatan **obat & stok**
* Pencatatan **penjualan harian**
* Pencatatan **kunjungan (berdasarkan transaksi)**
* Pencatatan **absensi & shift petugas**
* Penyediaan **API ringan** untuk diakses oleh:

  * N8N
  * Chatbot AI (Gemini) via N8N
  * WA Gateway (secara tidak langsung via N8N)

Target utamanya:

> “Bikin data apotek rapi dan gampang di-query via API, supaya chatbot bisa jawab pertanyaan petugas cukup lewat WhatsApp.”

---

### 1.2. Tech Stack Singkat

* **Backend**: PHP 7.x / 8.x dengan **CodeIgniter 3**
* **Database**: MySQL / MariaDB
* **Auth API**: API key sederhana (header `X-API-KEY`) / token
* **Output API**: JSON
* **Integrasi eksternal**:

  * N8N → call API CI3
  * CI3 → query database

---

### 1.3. Modul Utama Aplikasi

1. **Master Data**

   * Data obat
   * Kategori obat (optional)
   * Data pelanggan (optional, bisa auto-create per transaksi)

2. **Penjualan**

   * Input transaksi penjualan
   * Detail item obat per transaksi

3. **Stok**

   * Stok awal
   * Mutasi stok (barang masuk/keluar)
   * Perhitungan stok akhir

4. **Absensi & Shift Petugas**

   * Petugas apotek (user)
   * Jadwal shift
   * Absensi (hadir/tidak)

5. **Laporan / Summary**

   * Total penjualan harian
   * List obat terjual per hari
   * Produk terlaris (daily/weekly/monthly)
   * Total kunjungan (berdasarkan transaksi)

6. **API untuk Chatbot / N8N**

   * Endpoint ringkas untuk summary dan pengecekan:

     * Total penjualan per hari
     * Daftar obat terjual per hari
     * Top selling product
     * Absensi & petugas yang jaga
     * Total kunjungan
     * Cek stok obat

---

## 2. Schema Model Data

Ini versi sederhana tapi cukup buat kebutuhan chatbot/operasional.

> Notasi tipe data: anggap MySQL. Silakan di-adjust nanti kalau perlu.

### 2.1. Tabel `users` (Petugas / Admin)

| Kolom         | Tipe                     | Keterangan              |
| ------------- | ------------------------ | ----------------------- |
| id            | INT PK AI                | ID user                 |
| name          | VARCHAR(100)             | Nama lengkap            |
| username      | VARCHAR(50)              | Username login          |
| password_hash | VARCHAR(255)             | Password (hash)         |
| role          | ENUM('admin','apoteker') | Role user               |
| active        | TINYINT(1)               | 1 = aktif, 0 = nonaktif |
| created_at    | DATETIME                 | Waktu dibuat            |
| updated_at    | DATETIME                 | Waktu update            |

---

### 2.2. Tabel `medicine_categories` (Optional)

| Kolom      | Tipe         | Keterangan    |
| ---------- | ------------ | ------------- |
| id         | INT PK AI    | ID kategori   |
| name       | VARCHAR(100) | Nama kategori |
| created_at | DATETIME     | Waktu dibuat  |
| updated_at | DATETIME     | Waktu update  |

---

### 2.3. Tabel `medicines` (Obat)

| Kolom       | Tipe          | Keterangan                                    |
| ----------- | ------------- | --------------------------------------------- |
| id          | INT PK AI     | ID obat                                       |
| code        | VARCHAR(50)   | Kode unik obat                                |
| name        | VARCHAR(150)  | Nama obat                                     |
| category_id | INT FK        | Relasi ke `medicine_categories.id` (nullable) |
| unit        | VARCHAR(50)   | Satuan (tablet, strip, botol, dsb)            |
| price       | DECIMAL(15,2) | Harga jual                                    |
| is_active   | TINYINT(1)    | 1 = aktif, 0 = tidak                          |
| created_at  | DATETIME      | Waktu dibuat                                  |
| updated_at  | DATETIME      | Waktu update                                  |

Index penting:

* `UNIQUE(code)`
* Index `category_id`

---

### 2.4. Tabel `customers` (Optional tapi berguna buat kunjungan)

| Kolom      | Tipe         | Keterangan                  |
| ---------- | ------------ | --------------------------- |
| id         | INT PK AI    | ID customer                 |
| name       | VARCHAR(150) | Nama (boleh kosong/anonim)  |
| phone      | VARCHAR(50)  | No HP (opsional)            |
| created_at | DATETIME     | Waktu pertama kali tercatat |

> Kalau simple banget, bisa aja `customers` di-skip, lalu “kunjungan” dihitung dari jumlah transaksi.

---

### 2.5. Tabel `sales` (Transaksi Penjualan – Header)

| Kolom          | Tipe          | Keterangan                           |
| -------------- | ------------- | ------------------------------------ |
| id             | INT PK AI     | ID transaksi                         |
| invoice_number | VARCHAR(50)   | Nomor nota/invoice                   |
| sale_date      | DATE          | Tanggal transaksi                    |
| sale_time      | TIME          | Jam transaksi (optional)             |
| customer_id    | INT FK        | Relasi ke `customers.id` (nullable)  |
| total_amount   | DECIMAL(15,2) | Total nilai transaksi                |
| total_items    | INT           | Total item (qty)                     |
| created_by     | INT FK        | Relasi ke `users.id` (petugas input) |
| created_at     | DATETIME      | Waktu dibuat                         |

Index penting:

* Index `sale_date`
* Index `customer_id`
* Index `created_by`

---

### 2.6. Tabel `sale_items` (Detail Penjualan)

| Kolom       | Tipe          | Keterangan               |
| ----------- | ------------- | ------------------------ |
| id          | INT PK AI     | ID detail                |
| sale_id     | INT FK        | Relasi ke `sales.id`     |
| medicine_id | INT FK        | Relasi ke `medicines.id` |
| qty         | INT           | Jumlah                   |
| price       | DECIMAL(15,2) | Harga satuan             |
| subtotal    | DECIMAL(15,2) | qty × price              |

Index:

* Index `sale_id`
* Index `medicine_id`

---

### 2.7. Tabel `stock_logs` (Mutasi Stok)

| Kolom       | Tipe                          | Keterangan                                  |
| ----------- | ----------------------------- | ------------------------------------------- |
| id          | INT PK AI                     | ID log                                      |
| medicine_id | INT FK                        | Relasi ke `medicines.id`                    |
| log_date    | DATETIME                      | Waktu mutasi                                |
| type        | ENUM('in','out','adjustment') | Jenis mutasi                                |
| ref_type    | VARCHAR(50)                   | Referensi (e.g. 'sale','purchase','manual') |
| ref_id      | INT                           | ID referensi (misalnya `sales.id`)          |
| qty         | INT                           | Jumlah (+/-)                                |
| notes       | VARCHAR(255)                  | Catatan                                     |

> Stok akhir bisa dihitung: stok_awal + SUM(qty) per obat.
> Atau nanti tambahkan kolom `current_stock` di `medicines` + trigger / update manual.

---

### 2.8. Tabel `shifts` (Jadwal Jaga)

| Kolom      | Tipe        | Keterangan                       |
| ---------- | ----------- | -------------------------------- |
| id         | INT PK AI   | ID shift                         |
| date       | DATE        | Tanggal shift                    |
| shift_name | VARCHAR(50) | Contoh: “Pagi”, “Siang”, “Malam” |
| start_time | TIME        | Jam mulai                        |
| end_time   | TIME        | Jam selesai                      |
| created_at | DATETIME    | Waktu dibuat                     |

---

### 2.9. Tabel `attendances` (Absensi Petugas)

| Kolom         | Tipe                                 | Keterangan            |
| ------------- | ------------------------------------ | --------------------- |
| id            | INT PK AI                            | ID absensi            |
| user_id       | INT FK                               | Relasi ke `users.id`  |
| shift_id      | INT FK                               | Relasi ke `shifts.id` |
| date          | DATE                                 | Tanggal absensi       |
| status        | ENUM('hadir','izin','sakit','alpha') | Status kehadiran      |
| checkin_time  | DATETIME NULL                        | Jam masuk (optional)  |
| checkout_time | DATETIME NULL                        | Jam pulang (optional) |
| notes         | VARCHAR(255)                         | Catatan               |

---

### 2.10. Tabel `api_keys` (Untuk Integrasi N8N / Gateway)

| Kolom      | Tipe         | Keterangan                 |
| ---------- | ------------ | -------------------------- |
| id         | INT PK AI    | ID api key                 |
| name       | VARCHAR(100) | Nama client (misal: “N8N”) |
| api_key    | VARCHAR(100) | Key rahasia                |
| active     | TINYINT(1)   | 1 = aktif, 0 = nonaktif    |
| created_at | DATETIME     | Waktu dibuat               |

---

## 3. API Spec untuk Chatbot / N8N

> Konvensi:
>
> * Base URL: `/api/v1/...`
> * Auth: header `X-API-KEY: {apikey}`
> * Response: JSON
> * Semua contoh di bawah hanya contoh, nanti bisa di- adjust di implementasi CI3 (controller/method).

---

### 3.1. Summary Penjualan Harian

#### Endpoint

`GET /api/v1/sales/summary/daily`

#### Query Params

* `date` (optional, format: `YYYY-MM-DD`)

  * Default: tanggal hari ini (server time)

#### Request Example

```http
GET /api/v1/sales/summary/daily?date=2025-02-10
X-API-KEY: your-api-key
```

#### Response Example

```json
{
  "date": "2025-02-10",
  "total_transactions": 25,
  "total_items_sold": 130,
  "total_sales_amount": 2750000.00,
  "currency": "IDR"
}
```

---

### 3.2. List Obat Terjual per Hari

#### Endpoint

`GET /api/v1/sales/items-by-day`

#### Query Params

* `date` (required): `YYYY-MM-DD`

#### Response Example

```json
{
  "date": "2025-02-10",
  "items": [
    {
      "medicine_id": 1,
      "code": "OBT001",
      "name": "Paracetamol 500mg",
      "qty_sold": 40,
      "unit": "tablet",
      "total_amount": 400000.00
    },
    {
      "medicine_id": 2,
      "code": "OBT002",
      "name": "Amoxicillin 500mg",
      "qty_sold": 25,
      "unit": "capsule",
      "total_amount": 375000.00
    }
  ]
}
```

---

### 3.3. Produk Terlaris (Top Product)

#### Endpoint

`GET /api/v1/sales/top-products`

#### Query Params

* `period` (optional): `daily` | `weekly` | `monthly` (default: `daily`)
* `date` (optional): referensi tanggal, format `YYYY-MM-DD`
* `limit` (optional): default `10`

#### Response Example

```json
{
  "period": "weekly",
  "start_date": "2025-02-03",
  "end_date": "2025-02-09",
  "top_products": [
    {
      "rank": 1,
      "medicine_id": 1,
      "code": "OBT001",
      "name": "Paracetamol 500mg",
      "qty_sold": 120,
      "total_amount": 1200000.00
    },
    {
      "rank": 2,
      "medicine_id": 5,
      "code": "OBT005",
      "name": "Vitamin C 500mg",
      "qty_sold": 90,
      "total_amount": 675000.00
    }
  ]
}
```

---

### 3.4. Informasi Petugas yang Jaga Hari Ini

#### Endpoint

`GET /api/v1/attendance/shift-today`

#### Query Params

* `date` (optional): `YYYY-MM-DD` – default: hari ini

#### Response Example

```json
{
  "date": "2025-02-10",
  "shifts": [
    {
      "shift_id": 1,
      "shift_name": "Pagi",
      "start_time": "08:00:00",
      "end_time": "16:00:00",
      "guards": [
        {
          "user_id": 3,
          "name": "Budi",
          "status": "hadir"
        },
        {
          "user_id": 4,
          "name": "Ani",
          "status": "izin"
        }
      ]
    },
    {
      "shift_id": 2,
      "shift_name": "Malam",
      "start_time": "16:00:00",
      "end_time": "22:00:00",
      "guards": [
        {
          "user_id": 5,
          "name": "Rina",
          "status": "hadir"
        }
      ]
    }
  ]
}
```

> Ini nanti enak buat chatbot:
> “Siapa yang jaga sore ini?” → N8N panggil endpoint ini, lalu Gemini tinggal format jawaban.

---

### 3.5. Summary Kehadiran Petugas

#### Endpoint

`GET /api/v1/attendance/summary`

#### Query Params

* `date` (required): `YYYY-MM-DD`

#### Response Example

```json
{
  "date": "2025-02-10",
  "summary": {
    "total_scheduled": 5,
    "present": 4,
    "absent": 1,
    "detail": [
      {
        "user_id": 3,
        "name": "Budi",
        "status": "hadir"
      },
      {
        "user_id": 4,
        "name": "Ani",
        "status": "izin"
      }
    ]
  }
}
```

---

### 3.6. Total Kunjungan (Berbasis Transaksi)

#### Endpoint

`GET /api/v1/visits/summary`

#### Query Params

* `date` (required): `YYYY-MM-DD`

#### Logika sederhana:

* Jika tidak pakai tabel `customers`:

  * `total_visits = total_transactions`
* Jika pakai `customers`:

  * Bisa buat `unique_customers` per hari.

#### Response Example (simple)

```json
{
  "date": "2025-02-10",
  "total_transactions": 25,
  "total_visits": 25
}
```

---

### 3.7. Cek Stok Obat

#### Endpoint

`GET /api/v1/stock/check`

#### Query Params (salah satu):

* `code` (optional): kode obat
* `q` (optional): search by name (LIKE)

#### Response Example

```json
{
  "query": "paracetamol",
  "results": [
    {
      "medicine_id": 1,
      "code": "OBT001",
      "name": "Paracetamol 500mg",
      "unit": "tablet",
      "current_stock": 350
    },
    {
      "medicine_id": 7,
      "code": "OBT007",
      "name": "Paracetamol Sirup 250mg",
      "unit": "botol",
      "current_stock": 40
    }
  ]
}
```

---

### 3.8. Format Error Response (Global)

Biar N8N gampang handle error, semua error pakai format konsisten.

```json
{
  "success": false,
  "error": {
    "code": "INVALID_API_KEY",
    "message": "API key tidak valid atau sudah nonaktif."
  }
}
```

Atau:

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Parameter 'date' wajib diisi dengan format YYYY-MM-DD."
  }
}
```

---
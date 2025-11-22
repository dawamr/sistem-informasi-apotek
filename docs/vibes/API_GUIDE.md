# API Guide - Sistem Informasi Apotek

## 🔐 Authentication

Semua API endpoint memerlukan header `X-API-KEY`:

```
X-API-KEY: your-api-key-here
```

## 📡 Base URL

```
http://localhost:8000/api/v1
```

## 📊 Sales Endpoints

### 1. Summary Penjualan Harian

**Endpoint**: `GET /sales/summary/daily`

**Query Parameters**:
- `date` (optional): Format `YYYY-MM-DD`, default: hari ini

**Example Request**:
```bash
curl -X GET "http://localhost:8000/api/v1/sales/summary/daily?date=2025-02-10" \
  -H "X-API-KEY: your-api-key"
```

**Response**:
```json
{
  "success": true,
  "data": {
    "date": "2025-02-10",
    "total_transactions": 25,
    "total_items_sold": 130,
    "total_sales_amount": 2750000.00,
    "currency": "IDR"
  }
}
```

### 2. List Obat Terjual per Hari

**Endpoint**: `GET /sales/items-by-day`

**Query Parameters**:
- `date` (required): Format `YYYY-MM-DD`

**Example Request**:
```bash
curl -X GET "http://localhost:8000/api/v1/sales/items-by-day?date=2025-02-10" \
  -H "X-API-KEY: your-api-key"
```

**Response**:
```json
{
  "success": true,
  "data": {
    "date": "2025-02-10",
    "items": [
      {
        "medicine_id": 1,
        "code": "OBT001",
        "name": "Paracetamol 500mg",
        "qty_sold": 40,
        "unit": "tablet",
        "total_amount": 400000.00
      }
    ]
  }
}
```

### 3. Produk Terlaris

**Endpoint**: `GET /sales/top-products`

**Query Parameters**:
- `period` (optional): `daily`, `weekly`, `monthly` (default: `daily`)
- `date` (optional): Format `YYYY-MM-DD`, default: hari ini
- `limit` (optional): Jumlah produk (default: 10)

**Example Request**:
```bash
curl -X GET "http://localhost:8000/api/v1/sales/top-products?period=weekly&limit=5" \
  -H "X-API-KEY: your-api-key"
```

**Response**:
```json
{
  "success": true,
  "data": {
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
      }
    ]
  }
}
```

## 👥 Attendance Endpoints

### 1. Petugas yang Jaga Hari Ini

**Endpoint**: `GET /attendance/shift-today`

**Query Parameters**:
- `date` (optional): Format `YYYY-MM-DD`, default: hari ini

**Example Request**:
```bash
curl -X GET "http://localhost:8000/api/v1/attendance/shift-today" \
  -H "X-API-KEY: your-api-key"
```

**Response**:
```json
{
  "success": true,
  "data": {
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
          }
        ]
      }
    ]
  }
}
```

### 2. Summary Kehadiran

**Endpoint**: `GET /attendance/summary`

**Query Parameters**:
- `date` (required): Format `YYYY-MM-DD`

**Example Request**:
```bash
curl -X GET "http://localhost:8000/api/v1/attendance/summary?date=2025-02-10" \
  -H "X-API-KEY: your-api-key"
```

**Response**:
```json
{
  "success": true,
  "data": {
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
        }
      ]
    }
  }
}
```

## 🏪 Visits Endpoints

### Total Kunjungan

**Endpoint**: `GET /visits/summary`

**Query Parameters**:
- `date` (required): Format `YYYY-MM-DD`

**Example Request**:
```bash
curl -X GET "http://localhost:8000/api/v1/visits/summary?date=2025-02-10" \
  -H "X-API-KEY: your-api-key"
```

**Response**:
```json
{
  "success": true,
  "data": {
    "date": "2025-02-10",
    "total_transactions": 25,
    "total_visits": 25
  }
}
```

## 💊 Stock Endpoints

### Cek Stok Obat

**Endpoint**: `GET /stock/check`

**Query Parameters**:
- `code` (optional): Kode obat
- `q` (optional): Pencarian berdasarkan nama

**Example Request**:
```bash
curl -X GET "http://localhost:8000/api/v1/stock/check?q=paracetamol" \
  -H "X-API-KEY: your-api-key"
```

**Response**:
```json
{
  "success": true,
  "data": {
    "query": "paracetamol",
    "results": [
      {
        "medicine_id": 1,
        "code": "OBT001",
        "name": "Paracetamol 500mg",
        "unit": "tablet",
        "current_stock": 350
      }
    ]
  }
}
```

## ❌ Error Response

Semua error menggunakan format yang sama:

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Deskripsi error"
  }
}
```

### Error Codes

- `MISSING_API_KEY` - Header X-API-KEY tidak ditemukan
- `INVALID_API_KEY` - API key tidak valid atau nonaktif
- `VALIDATION_ERROR` - Parameter tidak valid
- `NOT_FOUND` - Resource tidak ditemukan
- `SERVER_ERROR` - Error di server

## 🧪 Testing dengan Postman

1. Import collection dari `docs/postman_collection.json`
2. Set variable `api_key` dengan API key Anda
3. Set variable `base_url` dengan URL aplikasi
4. Run requests

## 📝 Rate Limiting

Saat ini tidak ada rate limiting. Implementasi rate limiting dapat ditambahkan di masa depan.

## 🔄 Pagination

Saat ini endpoint tidak mendukung pagination. Untuk data besar, gunakan filter `date` atau `period`.

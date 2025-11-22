# 🧪 API Testing Guide

**Sistem Informasi Apotek CI3 - Testing Documentation**

---

## 🚀 Quick Start

### 1. Start PHP Development Server

```bash
cd /Users/dawamraja/Downloads/apotek-ci3-n8n
php -S localhost:8000 -t .
```

Server akan berjalan di: `http://localhost:8000`

### 2. Setup Database

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS apotek_db;"

# Import schema
mysql -u root -p apotek_db < docs/database_schema.sql

# Seed data
mysql -u root -p apotek_db < docs/database_seeder.sql
```

### 3. Test API Endpoints

---

## 📊 API Endpoints

### Sales Endpoints

#### 1. Daily Sales Summary
```bash
curl -X GET "http://localhost:8000/api/v1/sales/summary/daily?date=2025-02-21" \
  -H "X-API-KEY: sk_n8n_apotek_2025_test_key_12345"
```

**Expected Response**:
```json
{
  "success": true,
  "message": "Daily sales summary retrieved successfully",
  "data": {
    "date": "2025-02-21",
    "total_transactions": 7,
    "total_items_sold": 25,
    "total_sales_amount": 371500,
    "currency": "IDR"
  }
}
```

#### 2. Items Sold Per Day
```bash
curl -X GET "http://localhost:8000/api/v1/sales/items-by-day?date=2025-02-21" \
  -H "X-API-KEY: sk_n8n_apotek_2025_test_key_12345"
```

**Expected Response**:
```json
{
  "success": true,
  "message": "Items sold per day retrieved successfully",
  "data": {
    "date": "2025-02-21",
    "items": [
      {
        "medicine_id": 1,
        "code": "OBT001",
        "name": "Paracetamol 500mg Tablet",
        "unit": "tablet",
        "qty_sold": 23,
        "total_amount": 57500
      }
    ]
  }
}
```

#### 3. Top Products
```bash
curl -X GET "http://localhost:8000/api/v1/sales/top-products?period=daily&limit=5" \
  -H "X-API-KEY: sk_n8n_apotek_2025_test_key_12345"
```

---

### Attendance Endpoints

#### 1. Shift Today
```bash
curl -X GET "http://localhost:8000/api/v1/attendance/shift-today?date=2025-02-21" \
  -H "X-API-KEY: sk_n8n_apotek_2025_test_key_12345"
```

**Expected Response**:
```json
{
  "success": true,
  "message": "Staff on duty retrieved successfully",
  "data": {
    "date": "2025-02-21",
    "shifts": [
      {
        "shift_id": 1,
        "shift_name": "Pagi",
        "start_time": "08:00:00",
        "end_time": "16:00:00",
        "guards": [
          {
            "user_id": 2,
            "name": "Budi Santoso",
            "status": "hadir"
          }
        ]
      }
    ]
  }
}
```

#### 2. Attendance Summary
```bash
curl -X GET "http://localhost:8000/api/v1/attendance/summary?date=2025-02-21" \
  -H "X-API-KEY: sk_n8n_apotek_2025_test_key_12345"
```

---

### Visits Endpoint

#### Total Visits
```bash
curl -X GET "http://localhost:8000/api/v1/visits/summary?date=2025-02-21" \
  -H "X-API-KEY: sk_n8n_apotek_2025_test_key_12345"
```

**Expected Response**:
```json
{
  "success": true,
  "message": "Visits summary retrieved successfully",
  "data": {
    "date": "2025-02-21",
    "total_transactions": 7,
    "total_visits": 7
  }
}
```

---

### Stock Endpoint

#### 1. Check by Code
```bash
curl -X GET "http://localhost:8000/api/v1/stock/check?code=OBT001" \
  -H "X-API-KEY: sk_n8n_apotek_2025_test_key_12345"
```

#### 2. Search by Name
```bash
curl -X GET "http://localhost:8000/api/v1/stock/check?q=paracetamol" \
  -H "X-API-KEY: sk_n8n_apotek_2025_test_key_12345"
```

**Expected Response**:
```json
{
  "success": true,
  "message": "Stock check completed successfully",
  "data": {
    "query": "paracetamol",
    "results": [
      {
        "medicine_id": 1,
        "code": "OBT001",
        "name": "Paracetamol 500mg Tablet",
        "unit": "tablet",
        "current_stock": 477,
        "price": 2500
      }
    ]
  }
}
```

---

## 🔐 Error Testing

### Missing API Key
```bash
curl -X GET "http://localhost:8000/api/v1/sales/summary/daily"
```

**Expected Response** (401):
```json
{
  "success": false,
  "error": {
    "code": "MISSING_API_KEY",
    "message": "Header X-API-KEY tidak ditemukan"
  }
}
```

### Invalid API Key
```bash
curl -X GET "http://localhost:8000/api/v1/sales/summary/daily" \
  -H "X-API-KEY: invalid_key_12345"
```

**Expected Response** (401):
```json
{
  "success": false,
  "error": {
    "code": "INVALID_API_KEY",
    "message": "API key tidak valid atau sudah nonaktif"
  }
}
```

### Invalid Date Format
```bash
curl -X GET "http://localhost:8000/api/v1/sales/items-by-day?date=21-02-2025" \
  -H "X-API-KEY: sk_n8n_apotek_2025_test_key_12345"
```

**Expected Response** (400):
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Parameter 'date' harus format YYYY-MM-DD"
  }
}
```

---

## 📮 Using Postman

### Import Collection

1. Open Postman
2. Click "Import"
3. Select `docs/postman_collection.json`
4. Collection akan ter-import dengan semua endpoints

### Set Environment Variables

1. Click "Environments"
2. Create new environment: "Apotek Local"
3. Set variables:
   - `base_url`: `http://localhost:8000`
   - `api_key`: `sk_n8n_apotek_2025_test_key_12345`
   - `date`: `2025-02-21`

### Run Requests

1. Select environment: "Apotek Local"
2. Click on any request
3. Click "Send"
4. View response

---

## ✅ Test Checklist

### Authentication
- [ ] Request without API key returns 401
- [ ] Request with invalid API key returns 401
- [ ] Request with valid API key succeeds

### Sales Endpoints
- [ ] Daily summary returns correct data
- [ ] Items by day returns list of items
- [ ] Top products returns ranked list

### Attendance Endpoints
- [ ] Shift today returns shifts with guards
- [ ] Attendance summary returns counts

### Visits Endpoint
- [ ] Visits summary returns transaction count

### Stock Endpoint
- [ ] Search by code returns medicine
- [ ] Search by name returns list

### Error Handling
- [ ] Invalid date format returns error
- [ ] Missing required parameters return error
- [ ] Server errors return 500

---

## 🐛 Troubleshooting

### "Connection refused"
- Pastikan PHP server sudah running: `php -S localhost:8000 -t .`

### "Database connection error"
- Pastikan MySQL running
- Pastikan database `apotek_db` sudah dibuat
- Pastikan credentials benar di `application/config/database.php`

### "API key not found"
- Pastikan header `X-API-KEY` sudah ditambahkan
- Gunakan API key: `sk_n8n_apotek_2025_test_key_12345`

### "E_STRICT deprecated warning"
- Sudah di-fix di `system/core/Exceptions.php`
- Warning tidak akan muncul lagi

---

## 📝 Notes

### API Keys for Testing
```
N8N:     sk_n8n_apotek_2025_test_key_12345
Gemini:  sk_gemini_chatbot_2025_test_key_67890
Testing: sk_test_development_key_abcdef123456
```

### Test Data
- Date: 2025-02-21 (hari dengan data)
- Medicines: 29 items (OBT001 - OBT029)
- Users: 5 petugas
- Transactions: 10 sales

---

**Last Updated**: 2025-02-21

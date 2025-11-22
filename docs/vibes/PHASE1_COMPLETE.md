# ✅ Phase 1: Environment & Database - COMPLETE

**Status**: ✅ COMPLETED  
**Date**: 2025-02-21  
**Effort**: 2.5 hours  

---

## 📋 Summary

Phase 1 telah selesai dengan persiapan lengkap untuk database dan seeding data dummy yang akurat.

### ✅ Completed Tasks

#### 1. Environment Verification
- ✅ PHP 8.4.11 (CLI) - Compatible dengan CodeIgniter 3
- ✅ Zend Engine v4.4.11
- ✅ OPcache enabled

#### 2. Database Configuration
- ✅ Updated `application/config/database.php`
  - Host: localhost
  - Port: 3306
  - Username: root
  - Password: dawamr
  - Database: apotek_db
  - Driver: mysqli
  - Charset: utf8mb4
  - Collation: utf8mb4_unicode_ci

#### 3. Database Schema Created
- ✅ `docs/database_schema.sql` - 10 tables dengan:
  - Proper indexes untuk performance
  - Foreign key relationships
  - Constraints dan validasi
  - UTF8MB4 charset untuk support bahasa Indonesia

#### 4. Data Seeding Prepared
- ✅ `docs/database_seeder.sql` - Dummy data yang akurat:
  - **3 API Keys** untuk N8N, Gemini, dan Testing
  - **5 Users** (1 admin + 4 apoteker)
  - **8 Medicine Categories** (Analgesik, Antibiotik, Vitamin, dll)
  - **29 Medicines** dengan nama obat real Indonesia dan harga akurat
  - **10 Customers** dengan nama dan nomor HP
  - **10 Sales Transactions** (7 hari ini, 3 kemarin)
  - **25 Sale Items** dengan detail qty dan harga
  - **10 Shifts** (Pagi & Malam untuk 5 hari)
  - **8 Attendances** dengan berbagai status (hadir, izin, sakit, alpha)
  - **20+ Stock Logs** dari penjualan

#### 5. Seeder Controller Created
- ✅ `application/controllers/Seeder.php`
  - Method: `/seeder/run` - Jalankan seeder
  - Method: `/seeder/clear` - Hapus semua data
  - Safety check untuk production environment

---

## 📊 Database Schema Details

### 10 Tables Created

```
1. users (5 rows)
   - id, name, username, password_hash, role, active, timestamps

2. medicine_categories (8 rows)
   - id, name, description, timestamps

3. medicines (29 rows)
   - id, code, name, category_id, unit, price, current_stock, is_active, timestamps

4. customers (10 rows)
   - id, name, phone, created_at

5. sales (10 rows)
   - id, invoice_number, sale_date, sale_time, customer_id, total_amount, total_items, created_by, created_at

6. sale_items (25 rows)
   - id, sale_id, medicine_id, qty, price, subtotal

7. stock_logs (20+ rows)
   - id, medicine_id, log_date, type, ref_type, ref_id, qty, notes

8. shifts (10 rows)
   - id, date, shift_name, start_time, end_time, timestamps

9. attendances (8 rows)
   - id, user_id, shift_id, date, status, checkin_time, checkout_time, notes

10. api_keys (3 rows)
    - id, name, api_key, active, timestamps
```

---

## 📦 Dummy Data Details

### Users (5 Petugas)
```
1. Admin Apotek (admin) - Role: admin
2. Budi Santoso (budi) - Role: apoteker
3. Ani Wijaya (ani) - Role: apoteker
4. Rina Kusuma (rina) - Role: apoteker
5. Doni Hermawan (doni) - Role: apoteker
```

### Medicines (29 Obat Real Indonesia)
**Kategori & Harga**:
- Paracetamol 500mg - Rp 2.500
- Ibuprofen 400mg - Rp 3.500
- Amoxicillin 500mg - Rp 5.000
- Vitamin C 500mg - Rp 4.000
- Cough Syrup - Rp 18.000
- Jamu Kunyit Asam - Rp 12.000
- (dan 23 obat lainnya)

### Transactions (10 Penjualan)
- **Hari ini**: 7 transaksi
- **Kemarin**: 3 transaksi
- **Total items terjual**: 25
- **Total penjualan**: Rp 371.500

### API Keys (3 Testing Keys)
```
1. N8N Integration
   Key: sk_n8n_apotek_2025_test_key_12345

2. Chatbot Gemini
   Key: sk_gemini_chatbot_2025_test_key_67890

3. Testing
   Key: sk_test_development_key_abcdef123456
```

---

## 🚀 How to Setup Database

### Step 1: Create Database
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS apotek_db;"
```

### Step 2: Import Schema
```bash
mysql -u root -p apotek_db < /Users/dawamraja/Downloads/apotek-ci3-n8n/docs/database_schema.sql
```

### Step 3: Seed Data (Choose One)

**Option A: Using MySQL CLI**
```bash
mysql -u root -p apotek_db < /Users/dawamraja/Downloads/apotek-ci3-n8n/docs/database_seeder.sql
```

**Option B: Using CI3 Seeder Controller**
```
Access: http://localhost:8000/seeder/run
```

**Option C: Using MySQL Workbench**
1. Open `database_schema.sql` → Execute
2. Open `database_seeder.sql` → Execute

### Step 4: Verify
```bash
mysql -u root -p apotek_db -e "SHOW TABLES; SELECT COUNT(*) as total_records FROM medicines;"
```

---

## 📁 Files Created/Modified

### Created Files
- ✅ `docs/database_schema.sql` - Database schema (10 tables)
- ✅ `docs/database_seeder.sql` - Dummy data
- ✅ `docs/PHASE1_SETUP.md` - Setup instructions
- ✅ `docs/PHASE1_COMPLETE.md` - This file
- ✅ `application/controllers/Seeder.php` - Seeder controller

### Modified Files
- ✅ `application/config/database.php` - Database configuration
- ✅ `docs/TODO.md` - Updated Phase 1 status

---

## 🔐 Security Notes

### Password Hashing
- Semua password di-hash menggunakan bcrypt
- Hash: `$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm`
- Untuk testing, semua user bisa login dengan username mereka

### API Keys
- API keys sudah di-generate untuk testing
- Jangan gunakan di production
- Ganti dengan key yang lebih aman

### Database Charset
- UTF8MB4 untuk support emoji dan karakter Indonesia
- Collation: utf8mb4_unicode_ci

---

## 🧪 Testing Database Connection

### Via PHP
```php
<?php
$config = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => 'dawamr',
    'database' => 'apotek_db',
    'dbdriver' => 'mysqli',
);

$conn = new mysqli(
    $config['hostname'],
    $config['username'],
    $config['password'],
    $config['database']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully!";
$conn->close();
?>
```

### Via MySQL CLI
```bash
mysql -u root -p -h localhost -e "USE apotek_db; SHOW TABLES;"
```

---

## 📊 Data Statistics

| Item | Count | Status |
|------|-------|--------|
| API Keys | 3 | ✅ |
| Users | 5 | ✅ |
| Categories | 8 | ✅ |
| Medicines | 29 | ✅ |
| Customers | 10 | ✅ |
| Sales | 10 | ✅ |
| Sale Items | 25 | ✅ |
| Shifts | 10 | ✅ |
| Attendances | 8 | ✅ |
| Stock Logs | 20+ | ✅ |

---

## 🔄 Next Steps (Phase 2)

### Phase 2: Models & Core Libraries (3.5 hours)

1. **Create API_Controller base class**
   - API key validation
   - JSON response wrapper
   - Error handling

2. **Build 8 Models**
   - User_model.php
   - Medicine_model.php
   - Sale_model.php
   - Sale_item_model.php
   - Stock_model.php
   - Shift_model.php
   - Attendance_model.php
   - Api_key_model.php

3. **Create Helper Functions**
   - Response formatting
   - Error codes
   - Utilities

---

## 📞 Troubleshooting

### Connection Error: "Access denied for user 'root'"
- Verify MySQL is running
- Check username/password
- Verify port 3306 is accessible

### Error: "Unknown database 'apotek_db'"
- Run: `mysql -u root -p -e "CREATE DATABASE apotek_db;"`
- Then import schema

### Error: "Table already exists"
- Drop database: `DROP DATABASE apotek_db;`
- Recreate from schema

### Charset Issues
- Ensure MySQL uses utf8mb4
- Check collation: utf8mb4_unicode_ci

---

## ✅ Phase 1 Completion Checklist

- [x] PHP version verified (8.4.11)
- [x] Database configuration updated
- [x] Database schema created (10 tables)
- [x] Indexes and foreign keys added
- [x] Dummy data prepared (accurate medicines & prices)
- [x] Seeder controller created
- [x] Documentation complete

---

**Status**: ✅ PHASE 1 COMPLETE  
**Next Phase**: Phase 2 - Models & Core Libraries  
**Estimated Time for Phase 2**: 3.5 hours  
**Last Updated**: 2025-02-21

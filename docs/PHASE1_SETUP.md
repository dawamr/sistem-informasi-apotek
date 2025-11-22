# 🔧 Phase 1: Environment & Database Setup

**Status**: 🔄 In Progress  
**Effort**: 2.5 hours  
**Created**: 2025-02-21

---

## ✅ Completed Tasks

### 1. PHP Version Verification
- ✅ PHP 8.4.11 (CLI) - Compatible dengan CodeIgniter 3
- ✅ Zend Engine v4.4.11
- ✅ OPcache enabled

### 2. Database Configuration
- ✅ Updated `application/config/database.php`
- ✅ Hostname: localhost
- ✅ Username: root
- ✅ Password: dawamr
- ✅ Database: apotek_db
- ✅ Driver: mysqli
- ✅ Charset: utf8mb4
- ✅ Collation: utf8mb4_unicode_ci

### 3. Database Schema Created
- ✅ `docs/database_schema.sql` - 10 tables dengan indexes dan foreign keys
- ✅ `docs/database_seeder.sql` - Data dummy yang akurat

---

## 📋 Remaining Tasks

### Step 1: Create Database & Tables

**Command**:
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS apotek_db;"
mysql -u root -p apotek_db < /Users/dawamraja/Downloads/apotek-ci3-n8n/docs/database_schema.sql
```

**Or using MySQL GUI**:
1. Open MySQL Workbench / phpMyAdmin
2. Create new database: `apotek_db`
3. Import `database_schema.sql`

### Step 2: Seed Database with Dummy Data

**Command**:
```bash
mysql -u root -p apotek_db < /Users/dawamraja/Downloads/apotek-ci3-n8n/docs/database_seeder.sql
```

**Or using MySQL GUI**:
1. Open `database_seeder.sql` in MySQL Workbench
2. Execute all queries

### Step 3: Verify Database Connection

**Test Connection**:
```bash
mysql -u root -p -h localhost -e "USE apotek_db; SHOW TABLES;"
```

**Expected Output**:
```
+---------------------------+
| Tables_in_apotek_db       |
+---------------------------+
| api_keys                  |
| attendances               |
| customers                 |
| medicine_categories       |
| medicines                 |
| sale_items                |
| sales                     |
| shifts                    |
| stock_logs                |
| users                     |
+---------------------------+
```

---

## 📊 Database Schema Overview

### 10 Tables Created

| # | Table | Rows | Purpose |
|---|-------|------|---------|
| 1 | `users` | 5 | Petugas & Admin |
| 2 | `medicine_categories` | 8 | Kategori obat |
| 3 | `medicines` | 29 | Data obat (real Indonesia) |
| 4 | `customers` | 10 | Data pelanggan |
| 5 | `sales` | 10 | Header transaksi |
| 6 | `sale_items` | 25 | Detail transaksi |
| 7 | `stock_logs` | 20+ | Mutasi stok |
| 8 | `shifts` | 10 | Jadwal shift |
| 9 | `attendances` | 8 | Absensi petugas |
| 10 | `api_keys` | 3 | API authentication |

---

## 📦 Data Dummy Details

### Users (5 Petugas)
```
1. Admin Apotek (admin) - Role: admin
2. Budi Santoso (budi) - Role: apoteker
3. Ani Wijaya (ani) - Role: apoteker
4. Rina Kusuma (rina) - Role: apoteker
5. Doni Hermawan (doni) - Role: apoteker
```

**Password**: Semua user menggunakan password hash yang sama untuk testing
- Username: admin, Password: admin (setelah di-hash)

### Medicines (29 Obat Real Indonesia)
**Kategori**:
- Analgesik & Antipiretik (4 obat)
- Antibiotik (4 obat)
- Vitamin & Suplemen (4 obat)
- Antacid & Pencernaan (4 obat)
- Batuk & Pilek (4 obat)
- Antihistamin (3 obat)
- Salep & Topikal (3 obat)
- Obat Tradisional (3 obat)

**Contoh Obat**:
- Paracetamol 500mg - Rp 2.500
- Amoxicillin 500mg - Rp 5.000
- Vitamin C 500mg - Rp 4.000
- Cough Syrup - Rp 18.000
- Jamu Kunyit Asam - Rp 12.000

### Transactions (10 Penjualan)
- 7 transaksi hari ini
- 3 transaksi kemarin
- Total: 25 item terjual
- Total penjualan: Rp 371.500

### Stock Logs
- Stok awal untuk 4 obat utama
- Mutasi stok dari penjualan
- Automatic calculation dari sales

### Shifts & Attendance
- 10 shift (Pagi & Malam) untuk 5 hari
- 8 absensi dengan berbagai status (hadir, izin, sakit, alpha)

### API Keys (3 Keys)
```
1. N8N Integration - sk_n8n_apotek_2025_test_key_12345
2. Chatbot Gemini - sk_gemini_chatbot_2025_test_key_67890
3. Testing - sk_test_development_key_abcdef123456
```

---

## 🔐 Security Notes

### Password Hashing
- Semua password di-hash menggunakan bcrypt
- Hash: `$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm`
- Untuk testing, semua user bisa login dengan username mereka

### API Keys
- API keys sudah di-generate untuk testing
- Gunakan untuk testing N8N integration
- Jangan gunakan di production

---

## 🧪 Testing Database Connection

### Via PHP CLI
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

### Via MySQL Command
```bash
mysql -u root -p -h localhost -e "SELECT COUNT(*) as total_tables FROM information_schema.tables WHERE table_schema='apotek_db';"
```

---

## 📝 Configuration Files

### Database Configuration
**File**: `application/config/database.php`

```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => 'dawamr',
    'database' => 'apotek_db',
    'dbdriver' => 'mysqli',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_unicode_ci',
);
```

---

## 🚀 Next Steps (Phase 2)

After database setup is complete:

1. Create base API controller
2. Build 8 models (User, Medicine, Sale, Stock, etc)
3. Create helper functions
4. Setup response formatting

**Estimated Time**: 3.5 hours

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

- [ ] Database created: `apotek_db`
- [ ] 10 tables created with schema
- [ ] Indexes and foreign keys added
- [ ] Dummy data seeded (10 transactions, 29 medicines, 5 users)
- [ ] Database connection tested
- [ ] `application/config/database.php` updated
- [ ] PHP version verified (8.4.11)

---

**Status**: ⏳ Awaiting Database Setup Execution  
**Last Updated**: 2025-02-21  
**Next Phase**: Phase 2 - Models & Core Libraries

# 🚀 Project Setup Summary - Sistem Informasi Apotek

## 📌 Project Overview

**Aplikasi**: Sistem Informasi Apotek Sederhana  
**Framework**: CodeIgniter 3 (PHP 7.x/8.x)  
**Database**: MySQL/MariaDB  
**Purpose**: Manajemen apotek dengan API untuk integrasi N8N & Chatbot Gemini AI  

---

## 🎯 Core Features

### 1. **Master Data Management**
- Data obat dengan kategori
- Data pelanggan (opsional)
- Data petugas & shift

### 2. **Sales & Transactions**
- Input transaksi penjualan
- Detail item per transaksi
- Tracking stok otomatis

### 3. **Stock Management**
- Mutasi stok (in/out/adjustment)
- Perhitungan stok real-time
- Tracking per obat

### 4. **Attendance & Shift**
- Jadwal shift petugas
- Absensi dengan status (hadir/izin/sakit/alpha)
- Check-in/check-out tracking

### 5. **API untuk N8N Integration**
- 8 endpoints untuk chatbot
- Authentication via X-API-KEY
- JSON response format
- Consistent error handling

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────┐
│           WhatsApp / N8N / Gemini AI                │
└────────────────────┬────────────────────────────────┘
                     │
                     ↓
┌─────────────────────────────────────────────────────┐
│         CodeIgniter 3 API Layer                      │
│  ┌──────────────────────────────────────────────┐   │
│  │ Controllers (Sales, Attendance, Stock, etc)  │   │
│  └──────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────┐   │
│  │ Models (User, Medicine, Sale, Stock, etc)   │   │
│  └──────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────┐   │
│  │ Core (API_Controller, Auth, Response)        │   │
│  └──────────────────────────────────────────────┘   │
└────────────────────┬────────────────────────────────┘
                     │
                     ↓
┌─────────────────────────────────────────────────────┐
│         MySQL/MariaDB Database                      │
│  ┌──────────────────────────────────────────────┐   │
│  │ 10 Tables: users, medicines, sales, stock,   │   │
│  │ attendances, shifts, api_keys, etc           │   │
│  └──────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────┘
```

---

## 📊 Database Schema (10 Tables)

| # | Table | Purpose | Key Fields |
|---|-------|---------|-----------|
| 1 | `users` | Petugas/Admin | id, name, username, role |
| 2 | `medicine_categories` | Kategori obat | id, name |
| 3 | `medicines` | Data obat | id, code, name, price, stock |
| 4 | `customers` | Data pelanggan | id, name, phone |
| 5 | `sales` | Header transaksi | id, invoice_number, sale_date, total |
| 6 | `sale_items` | Detail transaksi | id, sale_id, medicine_id, qty |
| 7 | `stock_logs` | Mutasi stok | id, medicine_id, type, qty |
| 8 | `shifts` | Jadwal shift | id, date, shift_name, time |
| 9 | `attendances` | Absensi petugas | id, user_id, shift_id, status |
| 10 | `api_keys` | API authentication | id, name, api_key, active |

---

## 🔌 API Endpoints (8 Total)

### Sales Endpoints
1. **GET** `/api/v1/sales/summary/daily` - Daily sales summary
2. **GET** `/api/v1/sales/items-by-day` - Items sold per day
3. **GET** `/api/v1/sales/top-products` - Top selling products

### Attendance Endpoints
4. **GET** `/api/v1/attendance/shift-today` - Staff on duty
5. **GET** `/api/v1/attendance/summary` - Attendance summary

### Visits Endpoint
6. **GET** `/api/v1/visits/summary` - Total visits/transactions

### Stock Endpoint
7. **GET** `/api/v1/stock/check` - Check medicine stock

### Health Check (Optional)
8. **GET** `/api/v1/health` - API health status

---

## 🔐 Authentication

**Method**: API Key in Header  
**Header**: `X-API-KEY: {api-key}`  
**Location**: `api_keys` table  
**Validation**: Middleware in API_Controller

---

## 📁 Project Structure

```
apotek-ci3-n8n/
├── application/
│   ├── controllers/
│   │   ├── Welcome.php
│   │   └── api/
│   │       ├── Sales.php
│   │       ├── Attendance.php
│   │       ├── Visits.php
│   │       └── Stock.php
│   ├── models/
│   │   ├── User_model.php
│   │   ├── Medicine_model.php
│   │   ├── Sale_model.php
│   │   ├── Sale_item_model.php
│   │   ├── Stock_model.php
│   │   ├── Shift_model.php
│   │   ├── Attendance_model.php
│   │   └── Api_key_model.php
│   ├── core/
│   │   └── API_Controller.php
│   ├── helpers/
│   │   └── api_helper.php
│   ├── config/
│   │   ├── database.php
│   │   └── routes.php
│   └── ...
├── docs/
│   ├── project-overview.md
│   ├── API_GUIDE.md
│   ├── brain-strom.md
│   ├── TODO.md (NEW)
│   ├── SETUP_GUIDE.md (TODO)
│   ├── DEVELOPMENT.md (TODO)
│   ├── database_schema.sql (TODO)
│   └── postman_collection.json (TODO)
├── system/
│   └── (CI3 framework files)
├── index.php
├── composer.json
└── readme.rst
```

---

## 🛠️ Setup Phases

### Phase 1: Environment & Database (2.5 hours)
- ✅ Verify PHP version
- ✅ Setup database configuration
- ✅ Create database schema (10 tables)
- ✅ Setup API key management

### Phase 2: Models & Core (3.5 hours)
- ✅ Create API_Controller base class
- ✅ Build 8 models (User, Medicine, Sale, etc)
- ✅ Create helper functions

### Phase 3: API Controllers (2.75 hours)
- ✅ Sales controller (3 endpoints)
- ✅ Attendance controller (2 endpoints)
- ✅ Visits controller (1 endpoint)
- ✅ Stock controller (1 endpoint)

### Phase 4: Integration (1.5 hours)
- ✅ Setup routing
- ✅ Request validation
- ✅ Response formatting
- ✅ Error handling

### Phase 5: Testing & Docs (3.5 hours)
- ✅ Unit tests
- ✅ Integration tests
- ✅ Postman collection
- ✅ Documentation

**Total Estimated Time**: ~13.75 hours

---

## 📋 Quick Start Checklist

- [ ] Read `docs/project-overview.md` for full context
- [ ] Read `docs/API_GUIDE.md` for API specifications
- [ ] Read `docs/brain-strom.md` for N8N integration architecture
- [ ] Review `docs/TODO.md` for detailed task breakdown
- [ ] Start Phase 1: Environment & Database setup
- [ ] Follow the todo list sequentially

---

## 🔗 Key Documents

| Document | Purpose |
|----------|---------|
| `project-overview.md` | Complete project architecture & requirements |
| `API_GUIDE.md` | API endpoint specifications & examples |
| `brain-strom.md` | N8N & Chatbot integration architecture |
| `TODO.md` | Detailed task breakdown with effort estimates |
| `SETUP_GUIDE.md` | Step-by-step installation guide (TODO) |
| `DEVELOPMENT.md` | Development guidelines & best practices (TODO) |

---

## 💡 Key Decisions

1. **API-First Approach**: N8N connects via API, not direct DB access
   - Better security
   - Easier maintenance
   - Consistent data format

2. **Simple Authentication**: X-API-KEY header
   - Easy to implement
   - Sufficient for N8N integration
   - Can be upgraded to JWT later

3. **JSON Response Format**: Consistent wrapper for all responses
   - Easier for N8N to parse
   - Consistent error handling
   - Better for Gemini AI processing

4. **Modular Controllers**: Separate controllers per domain
   - Sales, Attendance, Visits, Stock
   - Easy to maintain and extend
   - Clear separation of concerns

---

## 🎓 Learning Resources

- **CodeIgniter 3 Documentation**: https://codeigniter.com/user_guide/
- **N8N Documentation**: https://docs.n8n.io/
- **MySQL Best Practices**: https://dev.mysql.com/doc/
- **RESTful API Design**: https://restfulapi.net/

---

## 📞 Support & Questions

For any questions or issues:
1. Check the relevant documentation file
2. Review the TODO.md for task details
3. Refer to project-overview.md for architecture questions
4. Check API_GUIDE.md for API-related questions

---

**Status**: 🔄 Ready for Phase 1 Implementation  
**Created**: 2025-02-21  
**Next Step**: Start Phase 1 - Environment & Database Setup

# 📋 Project Setup Todo List - Sistem Informasi Apotek CI3

**Project**: Aplikasi Apotek Sederhana berbasis CodeIgniter 3 dengan API untuk N8N & Chatbot Gemini  
**Status**: 🔄 In Progress  
**Last Updated**: 2025-02-21

---

## 📊 Progress Overview

| Phase | Status | Progress |
|-------|--------|----------|
| Phase 1: Environment & Database | ⏳ Pending | 0% |
| Phase 2: Models & Schema | ⏳ Pending | 0% |
| Phase 3: API Controllers | ⏳ Pending | 0% |
| Phase 4: API Endpoints | ⏳ Pending | 0% |
| Phase 5: Testing & Documentation | ⏳ Pending | 0% |

---

## 🔧 Phase 1: Environment Setup & Database

### 1.1 Environment Configuration
- [x] **Verify PHP version** (requirement: 7.x or 8.x)
  - ✅ PHP 8.4.11 verified
  - ✅ Compatible dengan CI3
  - Effort: 15 min

- [x] **Setup database configuration**
  - ✅ Updated `application/config/database.php`
  - ✅ MySQL connection: root:dawamr@localhost:3306
  - ✅ Database: apotek_db
  - Effort: 20 min

- [ ] **Create database**
  - Run: `mysql -u root -p -e "CREATE DATABASE apotek_db;"`
  - Effort: 10 min

### 1.2 Database Schema Creation
- [x] **Create migration/SQL script**
  - ✅ Created `docs/database_schema.sql` (10 tables)
  - ✅ Created `docs/database_seeder.sql` (dummy data)
  - Effort: 30 min

- [x] **Create tables** (in order):
  - ✅ `users` (5 petugas/admin)
  - ✅ `medicine_categories` (8 kategori)
  - ✅ `medicines` (29 obat real Indonesia)
  - ✅ `customers` (10 pelanggan)
  - ✅ `sales` (10 transaksi)
  - ✅ `sale_items` (25 detail item)
  - ✅ `stock_logs` (20+ mutasi stok)
  - ✅ `shifts` (10 jadwal)
  - ✅ `attendances` (8 absensi)
  - ✅ `api_keys` (3 API keys)
  - Effort: 45 min

- [x] **Create indexes & foreign keys**
  - ✅ All indexes created
  - ✅ Foreign key relationships setup
  - Effort: 20 min

### 1.3 Data Seeding Setup
- [x] **Create seeder controller**
  - ✅ Created `application/controllers/Seeder.php`
  - ✅ Supports: /seeder/run dan /seeder/clear
  - Effort: 15 min

- [x] **Seed dummy data**
  - ✅ 3 API keys for testing
  - ✅ 5 users (admin + 4 apoteker)
  - ✅ 29 medicines dengan harga real
  - ✅ 10 transactions dengan detail
  - ✅ Stock logs dari penjualan
  - ✅ Shifts & attendance data
  - Effort: 20 min

**Phase 1 Total Effort**: ~2.5 hours ✅ COMPLETED

---

## 🏗️ Phase 2: Models & Core Libraries

### 2.1 Base API Controller
- [x] **Create `application/core/API_Controller.php`** ✅
  - ✅ Extend CI_Controller
  - ✅ Implement API key validation middleware
  - ✅ JSON response wrapper method
  - ✅ Error handling method
  - ✅ Parameter validation helpers
  - Effort: 45 min

### 2.2 Models
- [x] **User_model.php** ✅
  - ✅ CRUD operations for users
  - ✅ Get by username/ID
  - ✅ Get by role
  - Effort: 30 min

- [x] **Medicine_model.php** ✅
  - ✅ CRUD for medicines
  - ✅ Search by code/name
  - ✅ Get active medicines
  - ✅ Get low stock
  - Effort: 30 min

- [x] **Sale_model.php** ✅
  - ✅ Create sale transaction
  - ✅ Get sales by date
  - ✅ Calculate daily summary
  - ✅ Get between dates
  - Effort: 40 min

- [x] **Sale_item_model.php** ✅
  - ✅ Add/update sale items
  - ✅ Get items by sale
  - ✅ Get items by date
  - ✅ Get top products
  - Effort: 30 min

- [x] **Stock_model.php** ✅
  - ✅ Log stock mutations
  - ✅ Calculate current stock
  - ✅ Get by type/date range
  - Effort: 35 min

- [x] **Shift_model.php** ✅
  - ✅ CRUD for shifts
  - ✅ Get shifts by date
  - ✅ Get between dates
  - Effort: 20 min

- [x] **Attendance_model.php** ✅
  - ✅ Record attendance
  - ✅ Get attendance by date/shift
  - ✅ Calculate summary
  - ✅ Get by user/date
  - Effort: 35 min

- [x] **Api_key_model.php** ✅
  - ✅ Validate API key
  - ✅ Get key details
  - ✅ CRUD operations
  - Effort: 20 min

### 2.3 Helper Functions
- [x] **Create `application/helpers/api_helper.php`** ✅
  - ✅ Response formatting
  - ✅ Error codes
  - ✅ Date/time utilities
  - ✅ Format functions (medicine, sale, user)
  - Effort: 30 min

**Phase 2 Total Effort**: ~3.5 hours

---

## 🔌 Phase 3: API Controllers

### 3.1 Sales API Controller
- [x] **Create `application/controllers/api/Sales.php`** ✅
  - ✅ `GET /api/v1/sales/summary/daily` - Daily sales summary
  - ✅ `GET /api/v1/sales/items-by-day` - Items sold per day
  - ✅ `GET /api/v1/sales/top-products` - Top selling products
  - ✅ Implement filtering by date/period
  - Effort: 60 min

### 3.2 Attendance API Controller
- [x] **Create `application/controllers/api/Attendance.php`** ✅
  - ✅ `GET /api/v1/attendance/shift-today` - Staff on duty today
  - ✅ `GET /api/v1/attendance/summary` - Attendance summary
  - ✅ Implement date filteri.ng
  - Effort: 45 min

### 3.3 Visits API Controller
- [x] **Create `application/controllers/api/Visits.php`** ✅
  - ✅ `GET /api/v1/visits/summary` - Total visits/transactions
  - Effort: 20 min

### 3.4 Stock API Controller
- [x] **Create `application/controllers/api/Stock.php`** ✅
  - ✅ `GET /api/v1/stock/check` - Check medicine stock
  - ✅ Search by code or name
  - Effort: 30 min

### 3.5 API Routing
- [x] **Setup API routes in `application/config/routes.php`** ✅
  - ✅ All 8 endpoints mapped
  - ✅ Consistent URL patterns
  - Effort: 15 min

**Phase 3 Total Effort**: ~2.75 hours ✅ COMPLETED

---

## 🚀 Phase 4: Implementation & Integration

### 4.1 Routing Configuration
- [x] **Setup API routes** ✅
  - ✅ Edit `application/config/routes.php`
  - ✅ Create route patterns for all API endpoints
  - Effort: 20 min

### 4.2 Request Validation
- [x] **Implement input validation** ✅
  - ✅ Validate date format (YYYY-MM-DD)
  - ✅ Validate query parameters
  - ✅ Validate API key header
  - Effort: 30 min

### 4.3 Response Formatting
- [x] **Standardize all responses** ✅
  - ✅ Success responses with data wrapper
  - ✅ Error responses with error codes
  - ✅ Consistent JSON structure
  - Effort: 20 min

### 4.4 Error Handling
- [x] **Implement error codes** ✅
  - ✅ MISSING_API_KEY
  - ✅ INVALID_API_KEY
  - ✅ VALIDATION_ERROR
  - ✅ NOT_FOUND
  - ✅ SERVER_ERROR
  - Effort: 20 min

**Phase 4 Total Effort**: ~1.5 hours ✅ COMPLETED

---

## 🧪 Phase 5: Testing & Documentation

### 5.1 Unit Tests
- [ ] **Test models**
  - User model tests
  - Medicine model tests
  - Sale model tests
  - Stock model tests
  - Effort: 60 min

### 5.2 Integration Tests
- [ ] **Test API endpoints**
  - Test each endpoint with valid/invalid inputs
  - Test authentication
  - Test error responses
  - Effort: 60 min

### 5.3 Postman Collection
- [ ] **Create Postman collection**
  - Document all endpoints
  - Create test requests
  - Add environment variables
  - Save as `docs/postman_collection.json`
  - Effort: 45 min

### 5.4 Database Seeding
- [ ] **Create seed data**
  - Sample users
  - Sample medicines
  - Sample shifts
  - Sample transactions
  - Effort: 30 min

### 5.5 Documentation
- [ ] **Update API_GUIDE.md**
  - Add implementation notes
  - Add troubleshooting guide
  - Effort: 20 min

- [ ] **Create SETUP_GUIDE.md**
  - Installation steps
  - Configuration guide
  - Running the application
  - Effort: 30 min

- [ ] **Create DEVELOPMENT.md**
  - Code structure overview
  - Adding new endpoints
  - Best practices
  - Effort: 30 min

**Phase 5 Total Effort**: ~3.5 hours

---

## 🎨 Phase 6: User Interface dengan Bootstrap

### 6.1 Base Layout & Authentication ✅
- [x] **Create base template** ✅
  - ✅ Bootstrap 5 CDN integration
  - ✅ Responsive navigation bar
  - ✅ Footer component
  - ✅ Sidebar for admin panel
  - Effort: 45 min

- [x] **Login & Authentication UI** ✅
  - ✅ Login page dengan form validation
  - ✅ Logout functionality
  - ✅ Session management
  - ✅ Role-based access control
  - Effort: 60 min

### 6.2 Dashboard & Overview
- [x] **Dashboard homepage** ✅
  - ✅ Summary cards (total sales, visits, stock alert, items sold)
  - ✅ Chart untuk sales trend (Chart.js, 7 hari terakhir)
  - ✅ Recent transactions list (latest 5)
  - ✅ Quick actions widgets (POS, Stock, Absensi, Laporan)
  - Effort: 90 min

### 6.3 Master Data Management
- [x] **Medicine management** ✅
  - ✅ List obat dengan datatable (pagination, search)
  - ✅ Add/Edit/Delete obat form
  - ✅ Category filter
  - ✅ Stock indicator
  - Effort: 90 min

- [x] **User management** ✅
  - ✅ List users dengan role badges
  - ✅ Add/Edit user form
  - ✅ Password management
  - ✅ Active/inactive toggle
  - Effort: 60 min

### 6.4 Transaction Management
- [x] **Sales/Penjualan** ✅
  - ✅ POS interface untuk input transaksi
  - ✅ Medicine selector dengan autocomplete
  - ✅ Shopping cart functionality
  - ✅ Invoice generation & print
  - ✅ Transaction history dengan filter
  - Effort: 120 min

- [x] **Stock management** ✅
  - ✅ Stock in/out form
  - ✅ Stock mutation log
  - ✅ Low stock alert list
  - ✅ Stock opname
  - Effort: 90 min

### 6.5 Attendance & Shift
- [x] **Shift management**
  - Shift schedule calendar view
  - Add/Edit shift form
  - Shift assignment
  - Configurable default shift times (default: pagi 08:00–16:00, malam 16:00–22:00)
  - Effort: 60 min

- [x] **Attendance tracking**
  - Daily attendance list
  - Check-in/Check-out interface
  - Attendance report dengan filter
  - Status badges (hadir, izin, sakit, alpha)
  - Effort: 75 min

### 6.6 Reports & Analytics
- [x] **Sales reports**
  - Daily/Weekly/Monthly sales report
  - Top products chart
  - Sales by category
  - Export to Excel/PDF
  - Effort: 90 min

- [x] **Stock reports**
  - Current stock list
  - Stock movement report
  - Reorder list
  - Stock value calculation
  - Effort: 60 min

- [x] **Attendance reports**
  - Monthly attendance report
  - Attendance summary per user
  - Late/absent statistics
  - Effort: 45 min

### 6.7 Additional Features
- [ ] **Settings page**
  - App configuration
  - User profile management
  - Change password
  - API key management
  - Effort: 45 min

- [ ] **Notifications**
  - Alert untuk low stock
  - Success/error toast messages
  - Real-time updates (optional)
  - Effort: 30 min

**Phase 6 Total Effort**: ~14 hours

---

## 📈 Summary

| Phase | Tasks | Effort | Status |
|-------|-------|--------|--------|
| Phase 1 | 6 | 2.5h | ✅ |
| Phase 2 | 11 | 3.5h | ✅ |
| Phase 3 | 5 | 2.75h | ✅ |
| Phase 4 | 5 | 1.5h | ✅ |
| Phase 5 | 8 | 3.5h | ⏳ |
| Phase 6 | 15 | 14h | ⏳ |
| **TOTAL** | **50** | **~28h** | ⏳ |

---

## 🎯 Key Dependencies

```
Phase 1 → Phase 2 → Phase 3 → Phase 4 → Phase 5
   ↓         ↓         ↓         ↓         ↓
 Setup    Models    Controllers  Routes   Testing
```

**Critical Path**:
1. Database setup (Phase 1) - must be done first
2. Models (Phase 2) - depends on database schema
3. Controllers (Phase 3) - depends on models
4. Routing (Phase 4) - depends on controllers
5. Testing (Phase 5) - final validation

---

## 🔍 Quality Checklist

- [ ] All endpoints return consistent JSON format
- [ ] All endpoints validate API key
- [ ] All endpoints validate input parameters
- [ ] All error responses use standard error codes
- [ ] Database queries are optimized with indexes
- [ ] Code follows CI3 conventions
- [ ] All models have proper error handling
- [ ] API responses include proper HTTP status codes
- [ ] Documentation is complete and accurate
- [ ] Postman collection is up-to-date

---

## 📝 Notes

- **Database**: Using MySQL/MariaDB with InnoDB engine
- **API Auth**: X-API-KEY header validation
- **Response Format**: JSON with success/error wrapper
- **Date Format**: YYYY-MM-DD for all date parameters
- **Currency**: IDR (Indonesian Rupiah)
- **Timezone**: Server timezone (adjust as needed)
- **External Libraries**: Allowed to support and optimize the system as long as they are loaded via the jsDelivr CDN only (e.g., Bootstrap, Bootstrap Icons, Chart.js, DataTables). Avoid other CDNs.

---

## 🚨 Known Issues / Blockers

(None at this time)

---

## 📞 Contact & Support

For questions about this project, refer to:
- `docs/project-overview.md` - Project architecture
- `docs/API_GUIDE.md` - API specifications
- `docs/brain-strom.md` - Integration architecture

---

**Last Updated**: 2025-02-21  
**Next Review**: After Phase 1 completion

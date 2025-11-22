# 🎨 Phase 6: User Interface dengan Bootstrap

**Web-based Admin Panel untuk Sistem Informasi Apotek**

---

## 📋 Overview

Phase 6 adalah pengembangan User Interface menggunakan **Bootstrap 5 CDN** untuk membuat tampilan web yang responsive dan modern untuk semua fitur sistem apotek.

### Tech Stack
- **Frontend Framework**: Bootstrap 5 (via CDN)
- **JavaScript**: Vanilla JS + jQuery
- **Charts**: Chart.js
- **DataTables**: DataTables.js
- **Icons**: Bootstrap Icons / Font Awesome
- **Server-side**: PHP CodeIgniter 3

---

## 🎯 Target Features

### 1. Authentication & Security
- Login page dengan form validation
- Session management
- Role-based access control (Admin vs Apoteker)
- Logout functionality

### 2. Dashboard
- Summary cards (Sales, Visits, Stock)
- Sales trend chart
- Recent transactions
- Quick statistics

### 3. Master Data Management
- **Medicine Management**
  - CRUD operations
  - Search & filter
  - Stock indicator
  - Category management

- **User Management**
  - User list with roles
  - Add/Edit users
  - Password management

### 4. Transaction Management
- **Point of Sale (POS)**
  - Medicine selector
  - Shopping cart
  - Invoice generation
  - Print receipt

- **Stock Management**
  - Stock in/out
  - Stock mutation log
  - Low stock alerts

### 5. Attendance & Shift
- Shift schedule management
- Daily attendance tracking
- Check-in/out interface
- Attendance reports

### 6. Reports & Analytics
- Sales reports (daily/weekly/monthly)
- Stock reports
- Attendance reports
- Export to Excel/PDF

### 7. Settings
- App configuration
- User profile
- API key management

---

## 📊 Phase 6 Breakdown

| Section | Tasks | Effort | Priority |
|---------|-------|--------|----------|
| Base Layout & Auth | 2 | 1.75h | High |
| Dashboard | 1 | 1.5h | High |
| Master Data | 2 | 2.5h | High |
| Transactions | 2 | 3.5h | High |
| Attendance & Shift | 2 | 2.25h | Medium |
| Reports | 3 | 3.25h | Medium |
| Additional Features | 2 | 1.25h | Low |
| **TOTAL** | **15** | **~14h** | - |

---

## 🎨 Design Guidelines

### Layout Structure

```
┌─────────────────────────────────────────┐
│           Navigation Bar                │
├─────────┬───────────────────────────────┤
│         │                               │
│ Sidebar │      Main Content Area        │
│         │                               │
│  Menu   │      (Views/Pages)            │
│         │                               │
├─────────┴───────────────────────────────┤
│              Footer                     │
└─────────────────────────────────────────┘
```

### Color Scheme
- **Primary**: Bootstrap Blue (#0d6efd)
- **Success**: Green (#198754) - untuk status aktif, stok aman
- **Warning**: Yellow (#ffc107) - untuk stok rendah
- **Danger**: Red (#dc3545) - untuk alert, stok habis
- **Info**: Cyan (#0dcaf0) - untuk informasi

### Components
- **Cards**: Untuk summary stats dan content containers
- **Tables**: DataTables untuk list data
- **Forms**: Bootstrap form controls dengan validation
- **Modals**: Untuk add/edit operations
- **Alerts**: Toast notifications untuk feedback
- **Badges**: Status indicators

---

## 📁 File Structure

```
application/
├── controllers/
│   ├── Dashboard.php           # Dashboard controller
│   ├── Auth.php                # Login/logout
│   ├── Medicines.php           # Medicine CRUD
│   ├── Users.php               # User management
│   ├── Sales.php               # POS & transactions
│   ├── Stock.php               # Stock management
│   ├── Attendance.php          # Attendance tracking
│   ├── Shifts.php              # Shift management
│   └── Reports.php             # Reports & analytics
│
├── views/
│   ├── templates/
│   │   ├── header.php          # Head + navbar
│   │   ├── sidebar.php         # Sidebar menu
│   │   └── footer.php          # Footer
│   │
│   ├── auth/
│   │   └── login.php           # Login page
│   │
│   ├── dashboard/
│   │   └── index.php           # Dashboard view
│   │
│   ├── medicines/
│   │   ├── index.php           # List medicines
│   │   └── form.php            # Add/edit form
│   │
│   ├── sales/
│   │   ├── pos.php             # Point of sale
│   │   └── history.php         # Transaction history
│   │
│   ├── stock/
│   │   ├── index.php           # Stock list
│   │   └── mutation.php        # Stock in/out
│   │
│   ├── attendance/
│   │   ├── index.php           # Daily attendance
│   │   └── report.php          # Attendance report
│   │
│   └── reports/
│       ├── sales.php           # Sales report
│       ├── stock.php           # Stock report
│       └── attendance.php      # Attendance report
│
└── assets/
    ├── css/
    │   └── custom.css          # Custom styles
    ├── js/
    │   ├── app.js              # Main JS
    │   └── modules/            # Feature-specific JS
    └── images/
        └── logo.png            # App logo
```

---

## 🔧 Bootstrap CDN Integration

### Basic Template

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Apotek</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.0/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.0/dist/chart.umd.min.js"></script>
    
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/custom.css') ?>" rel="stylesheet">
</head>
<body>
    <!-- Content here -->
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.0/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.0/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
```

---

## 🚀 Development Workflow

### Step-by-Step

1. **Phase 6.1**: Base Layout & Authentication (1.75h)
   - Create template files
   - Implement login system
   - Setup session management

2. **Phase 6.2**: Dashboard (1.5h)
   - Build dashboard with stats
   - Add charts
   - Recent transactions

3. **Phase 6.3**: Master Data (2.5h)
   - Medicine management
   - User management

4. **Phase 6.4**: Transactions (3.5h)
   - POS interface
   - Stock management

5. **Phase 6.5**: Attendance & Shift (2.25h)
   - Shift schedule
   - Attendance tracking

6. **Phase 6.6**: Reports (3.25h)
   - Sales reports
   - Stock reports
   - Attendance reports

7. **Phase 6.7**: Additional Features (1.25h)
   - Settings
   - Notifications

---

## 📱 Responsive Design

All pages akan responsive dengan breakpoints:
- **Desktop**: > 992px (full sidebar)
- **Tablet**: 768px - 992px (collapsible sidebar)
- **Mobile**: < 768px (hamburger menu)

---

## 🔐 Security Considerations

- Form validation (client & server-side)
- CSRF protection
- XSS prevention
- SQL injection protection (prepared statements)
- Password hashing (bcrypt)
- Session timeout
- Role-based access control

---

## ✅ Quality Standards

- **Code Quality**: Clean, maintainable code
- **Performance**: Fast page load, optimized queries
- **Accessibility**: WCAG 2.1 Level A
- **Browser Support**: Chrome, Firefox, Safari, Edge (latest 2 versions)
- **Mobile-First**: Responsive design
- **User Experience**: Intuitive navigation, clear feedback

---

## 🧪 Testing Checklist

- [ ] All forms validate correctly
- [ ] CRUD operations work
- [ ] Charts display correctly
- [ ] DataTables pagination works
- [ ] Responsive on mobile
- [ ] Print functionality works
- [ ] Export to Excel/PDF works
- [ ] Session management works
- [ ] Role-based access works
- [ ] Error handling displays properly

---

## 📚 Resources

### Bootstrap 5
- Documentation: https://getbootstrap.com/docs/5.3/
- Examples: https://getbootstrap.com/docs/5.3/examples/

### Chart.js
- Documentation: https://www.chartjs.org/docs/latest/

### DataTables
- Documentation: https://datatables.net/manual/

### Bootstrap Icons
- Icons: https://icons.getbootstrap.com/

---

## 🎯 Success Criteria

Phase 6 dianggap selesai jika:
- ✅ Semua 15 tasks completed
- ✅ UI responsive di semua devices
- ✅ Semua fitur functional
- ✅ Code clean dan maintainable
- ✅ Documentation complete
- ✅ Testing passed

---

**Estimated Start**: After Phase 5 completion  
**Estimated Duration**: 14 hours  
**Priority**: High (user-facing features)  
**Status**: ⏳ Pending

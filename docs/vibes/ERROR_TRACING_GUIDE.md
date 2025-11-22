# 🔍 Error Tracing Guide

**Sistem Informasi Apotek - Error Tracing & Debugging**

---

## 📋 Overview

Guide untuk tracing dan debugging errors menggunakan centralized logging system.

---

## 🚀 Quick Start

### 1. Check Auth Logs

```bash
# View today's auth logs
tail -f application/logs/2025-02-21_auth.log

# View last 50 lines
tail -50 application/logs/2025-02-21_auth.log

# Search for errors
grep ERROR application/logs/2025-02-21_auth.log

# Search for failed logins
grep "failed login" application/logs/2025-02-21_auth.log
```

### 2. Check Error Logs

```bash
# View error logs
tail -f application/logs/2025-02-21_error.log

# View exceptions
grep "Exception" application/logs/2025-02-21_error.log
```

### 3. Check All Logs

```bash
# View all logs for today
ls -la application/logs/2025-02-21_*.log

# View combined logs
cat application/logs/2025-02-21_*.log | tail -100
```

---

## 🔐 Authentication Tracing

### Login Flow Logs

Ketika user login, log entries akan terlihat seperti:

```
[2025-02-21 14:30:45] [INFO] [app] Login page accessed
[2025-02-21 14:30:50] [INFO] [auth] Login process started
[2025-02-21 14:30:50] [INFO] [auth] Login attempt for username: admin
[2025-02-21 14:30:50] [DEBUG] [auth] User found in database: admin
[2025-02-21 14:30:50] [DEBUG] [auth] Password verified for user: admin
[2025-02-21 14:30:50] [DEBUG] [auth] Session data set for user: admin
[2025-02-21 14:30:50] [INFO] [auth] Successful login for user 'admin' from IP 127.0.0.1
```

### Failed Login Tracing

Ketika login gagal:

```
[2025-02-21 14:31:00] [INFO] [auth] Login process started
[2025-02-21 14:31:00] [INFO] [auth] Login attempt for username: admin
[2025-02-21 14:31:00] [WARNING] [auth] Failed login attempt for user 'admin' from IP 127.0.0.1
```

### Logout Flow Logs

```
[2025-02-21 14:35:00] [INFO] [auth] Logout process started for user: admin
[2025-02-21 14:35:00] [DEBUG] [auth] Session destroyed for user: admin
[2025-02-21 14:35:00] [INFO] [auth] User admin logged out
```

---

## 🐛 Debugging Steps

### Step 1: Identify the Issue

Check error logs untuk error message:

```bash
grep -i "error\|exception\|warning" application/logs/2025-02-21_*.log
```

### Step 2: Find Related Logs

Cari logs yang berhubungan dengan waktu error:

```bash
# Jika error terjadi pada 14:30:45
grep "14:30" application/logs/2025-02-21_*.log
```

### Step 3: Trace the Flow

Ikuti log entries dari awal sampai error terjadi:

```bash
# Contoh: trace login flow
grep "Login" application/logs/2025-02-21_auth.log
```

### Step 4: Check Context

Lihat context information (user, IP, URI):

```bash
# Cari logs dengan user ID tertentu
grep "user_id.*123" application/logs/2025-02-21_*.log

# Cari logs dari IP tertentu
grep "127.0.0.1" application/logs/2025-02-21_*.log
```

### Step 5: Analyze the Error

Pahami error message dan trace back ke source code.

---

## 🎯 Common Error Scenarios

### Scenario 1: Login Gagal

**Logs**:
```
[14:30:50] [INFO] [auth] Login attempt for username: admin
[14:30:50] [WARNING] [auth] Failed login attempt for user 'admin' from IP 127.0.0.1
```

**Kemungkinan Penyebab**:
- Username tidak ditemukan
- Password salah
- User tidak aktif

**Debugging**:
```bash
# Check database
mysql -u root -p apotek_db
SELECT * FROM users WHERE username = 'admin';

# Verify password hash
php -r "echo password_verify('password', '\$2y\$10\$...');"
```

### Scenario 2: Session Error

**Logs**:
```
[14:35:00] [ERROR] [auth] Exception: Exception | File: Auth.php | Line: 152
```

**Kemungkinan Penyebab**:
- Session library tidak loaded
- Database connection error
- Session data corrupt

**Debugging**:
```bash
# Check session logs
grep "session" application/logs/2025-02-21_*.log

# Check database connection
mysql -u root -p -h host.docker.internal apotek_db -e "SELECT 1;"
```

### Scenario 3: Database Error

**Logs**:
```
[14:30:50] [ERROR] [database] Query failed: SELECT * FROM users WHERE username = ?
```

**Kemungkinan Penyebab**:
- Database connection error
- Table tidak ada
- Query syntax error

**Debugging**:
```bash
# Check database logs
grep "database" application/logs/2025-02-21_*.log

# Verify table exists
mysql -u root -p apotek_db -e "DESCRIBE users;"

# Check query
mysql -u root -p apotek_db -e "SELECT * FROM users LIMIT 1;"
```

---

## 📊 Log Analysis Tools

### Using grep

```bash
# Find all errors
grep ERROR application/logs/2025-02-21_*.log

# Find specific user
grep "username: admin" application/logs/2025-02-21_*.log

# Find time range
grep "14:3[0-5]" application/logs/2025-02-21_*.log

# Count occurrences
grep -c "ERROR" application/logs/2025-02-21_*.log
```

### Using awk

```bash
# Extract specific fields
awk -F'[][]' '{print $2, $4, $6}' application/logs/2025-02-21_app.log

# Count by level
awk -F'[][]' '{print $4}' application/logs/2025-02-21_*.log | sort | uniq -c

# Count by channel
awk -F'[][]' '{print $6}' application/logs/2025-02-21_*.log | sort | uniq -c
```

### Using tail & head

```bash
# Last 100 lines
tail -100 application/logs/2025-02-21_app.log

# First 50 lines
head -50 application/logs/2025-02-21_app.log

# Lines 50-100
sed -n '50,100p' application/logs/2025-02-21_app.log
```

---

## 🔧 Real-Time Monitoring

### Watch Logs Live

```bash
# Watch auth logs
watch -n 1 'tail -20 application/logs/2025-02-21_auth.log'

# Follow logs in real-time
tail -f application/logs/2025-02-21_*.log

# Follow with grep filter
tail -f application/logs/2025-02-21_*.log | grep ERROR
```

### Monitor Multiple Channels

```bash
# Terminal 1: Watch auth logs
tail -f application/logs/2025-02-21_auth.log

# Terminal 2: Watch error logs
tail -f application/logs/2025-02-21_error.log

# Terminal 3: Watch all logs
tail -f application/logs/2025-02-21_*.log
```

---

## 📈 Performance Analysis

### Query Performance

```bash
# Find slow queries
grep "executed in" application/logs/2025-02-21_database.log | grep -E "[0-9]{3,}"

# Average query time
awk -F'ms' '{print $1}' application/logs/2025-02-21_database.log | tail -1
```

### Page Load Time

```bash
# Find page load times
grep "Page load" application/logs/2025-02-21_performance.log

# Find slow pages
grep "Page load" application/logs/2025-02-21_performance.log | awk '{print $NF}' | sort -n | tail -10
```

---

## 🔐 Security Analysis

### Failed Login Attempts

```bash
# Count failed logins
grep "failed login" application/logs/2025-02-21_security.log | wc -l

# Failed logins by user
grep "failed login" application/logs/2025-02-21_security.log | awk '{print $NF}' | sort | uniq -c

# Failed logins by IP
grep "failed login" application/logs/2025-02-21_security.log | grep -oE '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}' | sort | uniq -c
```

### Unauthorized Access

```bash
# Find unauthorized access attempts
grep "Unauthorized" application/logs/2025-02-21_security.log

# Count by resource
grep "Unauthorized" application/logs/2025-02-21_security.log | awk '{print $(NF-2)}' | sort | uniq -c
```

---

## 🛠️ Troubleshooting

### Logs Not Being Created

**Check**:
1. Directory permissions
   ```bash
   ls -la application/logs/
   chmod 755 application/logs/
   ```

2. Logging enabled in config
   ```php
   // application/config/logging.php
   'enabled' => TRUE
   ```

3. Helper loaded
   ```php
   $this->load->helper('logging');
   ```

### Logs Too Large

**Solution**:
```bash
# Cleanup old logs
find application/logs/ -name "*.log" -mtime +30 -delete

# Or manually
rm application/logs/2025-02-01_*.log
```

### Can't Find Specific Error

**Try**:
```bash
# Search all logs
grep -r "error message" application/logs/

# Search with case insensitive
grep -i "error message" application/logs/2025-02-21_*.log

# Search with regex
grep -E "error|exception|warning" application/logs/2025-02-21_*.log
```

---

## 📋 Log Rotation

### Check Log Files

```bash
# List all logs
ls -lh application/logs/

# Sort by size
ls -lhS application/logs/

# Sort by date
ls -lht application/logs/
```

### Manual Rotation

```bash
# Archive old logs
tar -czf application/logs/archive_2025-02-20.tar.gz application/logs/2025-02-20_*.log

# Delete archived logs
rm application/logs/2025-02-20_*.log
```

---

## ✅ Best Practices

1. **Check logs regularly**
   - Monitor error logs daily
   - Review security logs
   - Track performance

2. **Use grep effectively**
   - Filter by level
   - Filter by channel
   - Filter by time

3. **Keep logs organized**
   - Rotate old logs
   - Archive important logs
   - Delete very old logs

4. **Document issues**
   - Note error messages
   - Record timestamps
   - Track patterns

5. **Monitor performance**
   - Track query times
   - Monitor page loads
   - Identify bottlenecks

---

**Last Updated**: 2025-02-21  
**Version**: 1.0

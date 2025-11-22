# 🔧 Troubleshooting Guide

**Sistem Informasi Apotek - Error Fixes & Solutions**

---

## 🚨 Session Errors

### Error: "Session storage invalid"

**Symptoms**:
```
Peringatan mkdir(): Invalid path di Session_files_driver.php baris 137
Path penyimpanan sesi tidak valid atau tidak bisa dibuat
```

**Root Cause**:
- `session.save_path` kosong atau tidak valid
- Folder sesi tidak bisa dibuat
- Permission folder tidak memadai

**Solution**:

1. **Check session configuration**
   ```php
   // application/config/session.php
   $config['sess_driver'] = 'files';
   $config['sess_save_path'] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'apotek_sessions';
   ```

2. **Ensure directory exists**
   ```bash
   mkdir -p /tmp/apotek_sessions
   chmod 755 /tmp/apotek_sessions
   ```

3. **Verify in Docker**
   ```bash
   docker exec apotek-ci3-app mkdir -p /tmp/apotek_sessions
   docker exec apotek-ci3-app chmod 755 /tmp/apotek_sessions
   ```

4. **Check PHP configuration**
   ```bash
   php -i | grep session.save_path
   ```

---

## 🔐 Session Start Errors

### Error: "session_start() gagal inisialisasi"

**Symptoms**:
```
session_start() gagal inisialisasi karena driver "user" dengan path kosong
Konfigurasi session save path tidak terisi
```

**Root Cause**:
- Session driver tidak dikonfigurasi dengan benar
- Session path kosong
- Session library tidak loaded

**Solution**:

1. **Load session library in controller**
   ```php
   public function __construct()
   {
       parent::__construct();
       $this->load->library('session');
   }
   ```

2. **Verify session config**
   ```php
   // application/config/session.php
   $config['sess_driver'] = 'files';
   $config['sess_save_path'] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'apotek_sessions';
   ```

3. **Check error logs**
   ```bash
   tail -f application/logs/php_errors.log
   ```

---

## 📤 Header Errors

### Error: "Cannot modify header information"

**Symptoms**:
```
Cannot modify header information - headers already sent
Output started at [file]:[line]
```

**Root Cause**:
- Output buffering tidak aktif
- Error sebelumnya sudah print output
- Whitespace sebelum `<?php`

**Solution**:

1. **Enable output buffering**
   ```php
   // application/config/error_handler.php
   if (!ob_get_level()) {
       ob_start();
   }
   ```

2. **Check for whitespace**
   - Pastikan tidak ada spasi/newline sebelum `<?php`
   - Pastikan tidak ada spasi/newline setelah `?>`

3. **Use error handler**
   ```php
   require_once APPPATH . 'config/error_handler.php';
   ```

---

## 📝 Logging Configuration Errors

### Error: "Logging misconfig"

**Symptoms**:
```
File application/config/logging.php tidak berisi array konfigurasi yang valid
Library Logger tidak bisa dimuat
```

**Root Cause**:
- `logging.php` return statement salah
- Config array tidak didefinisikan dengan benar
- Syntax error di config file

**Solution**:

1. **Fix logging.php format**
   ```php
   <?php
   defined('BASEPATH') OR exit('No direct script access allowed');

   $config = array(
       // ... config items ...
   );

   return $config;
   ```

2. **Verify syntax**
   ```bash
   php -l application/config/logging.php
   ```

3. **Check in controller**
   ```php
   $this->config->load('logging');
   $logging_config = $this->config->item('logging');
   if (!$logging_config) {
       show_error('Logging configuration not found');
   }
   ```

---

## 🛠️ Complete Error Fix Checklist

### 1. Session Configuration
- [ ] Create `application/config/session.php`
- [ ] Set `sess_driver = 'files'`
- [ ] Set `sess_save_path` to valid directory
- [ ] Ensure directory is writable
- [ ] Load session library in controllers

### 2. Error Handling
- [ ] Create `application/config/error_handler.php`
- [ ] Enable output buffering
- [ ] Set error reporting level
- [ ] Configure error logging
- [ ] Include error handler in index.php

### 3. Logging Configuration
- [ ] Fix `application/config/logging.php` format
- [ ] Use `$config` variable
- [ ] Add `return $config;` at end
- [ ] Verify syntax with `php -l`
- [ ] Load helper in controllers

### 4. Directory Permissions
- [ ] Create `application/logs/` directory
- [ ] Set permissions to 755
- [ ] Create session directory
- [ ] Set session directory permissions

### 5. Testing
- [ ] Test login page loads
- [ ] Test session creation
- [ ] Test error logging
- [ ] Check log files created
- [ ] Verify no header errors

---

## 🔍 Debugging Commands

### Check PHP Configuration

```bash
# Session configuration
php -i | grep session

# Error reporting
php -i | grep error_reporting

# Display errors
php -i | grep display_errors
```

### Check File Permissions

```bash
# Check logs directory
ls -la application/logs/

# Check session directory
ls -la /tmp/apotek_sessions/

# Fix permissions
chmod 755 application/logs/
chmod 755 /tmp/apotek_sessions/
```

### Check Log Files

```bash
# View all logs
ls -la application/logs/

# View error logs
cat application/logs/php_errors.log

# View session logs
cat application/logs/exceptions.log

# View fatal errors
cat application/logs/fatal_errors.log
```

### Docker Commands

```bash
# Check container
docker ps | grep apotek

# Check logs
docker logs apotek-ci3-app

# Execute command in container
docker exec apotek-ci3-app ls -la /tmp/apotek_sessions/

# Create session directory
docker exec apotek-ci3-app mkdir -p /tmp/apotek_sessions

# Set permissions
docker exec apotek-ci3-app chmod 755 /tmp/apotek_sessions
```

---

## 📋 Common Solutions

### Solution 1: Session Path Error

```bash
# Create session directory
mkdir -p /tmp/apotek_sessions
chmod 755 /tmp/apotek_sessions

# Or in Docker
docker exec apotek-ci3-app mkdir -p /tmp/apotek_sessions
docker exec apotek-ci3-app chmod 755 /tmp/apotek_sessions
```

### Solution 2: Logging Configuration Error

```php
// application/config/logging.php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config = array(
    'enabled' => TRUE,
    'log_dir' => APPPATH . 'logs/',
    // ... more config ...
);

return $config;
```

### Solution 3: Output Buffering

```php
// Add to index.php or config/error_handler.php
if (!ob_get_level()) {
    ob_start();
}
```

### Solution 4: Error Logging

```php
// application/config/error_handler.php
set_error_handler('custom_error_handler');
set_exception_handler('custom_exception_handler');
register_shutdown_function('shutdown_handler');
```

---

## 🧪 Testing After Fix

### Test 1: Access Login Page

```bash
curl http://localhost:8081/auth
```

**Expected**: Login page loads without errors

### Test 2: Check Session Directory

```bash
ls -la /tmp/apotek_sessions/
```

**Expected**: Directory exists and is writable

### Test 3: Check Log Files

```bash
ls -la application/logs/
```

**Expected**: Log files created

### Test 4: Test Login

```bash
# Access login page
curl http://localhost:8081/auth

# Submit login form
curl -X POST http://localhost:8081/auth/login \
  -d "username=admin&password=password"
```

**Expected**: No header errors, session created

### Test 5: Check Logs

```bash
tail -f application/logs/2025-02-21_auth.log
```

**Expected**: Login logs recorded

---

## 🚀 Quick Fix Script

```bash
#!/bin/bash

# Create directories
mkdir -p /tmp/apotek_sessions
mkdir -p application/logs

# Set permissions
chmod 755 /tmp/apotek_sessions
chmod 755 application/logs

# Verify
echo "Session directory:"
ls -la /tmp/apotek_sessions/

echo "Logs directory:"
ls -la application/logs/

echo "Fix complete!"
```

---

## 📞 Support

If you still encounter errors:

1. **Check logs**
   ```bash
   tail -f application/logs/*.log
   ```

2. **Check Docker logs**
   ```bash
   docker logs apotek-ci3-app
   ```

3. **Verify configuration**
   ```bash
   php -l application/config/session.php
   php -l application/config/logging.php
   php -l application/config/error_handler.php
   ```

4. **Check permissions**
   ```bash
   ls -la application/logs/
   ls -la /tmp/apotek_sessions/
   ```

---

**Last Updated**: 2025-02-21  
**Version**: 1.0

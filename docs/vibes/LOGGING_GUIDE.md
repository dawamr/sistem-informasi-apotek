# 📝 Centralized Logging Guide

**Sistem Informasi Apotek - Logging System**

---

## 📋 Overview

Centralized logging system untuk Sistem Informasi Apotek dengan support untuk multiple channels, levels, dan contexts.

### Features

- ✅ Multiple log channels (app, auth, database, api, error, performance, security)
- ✅ Log levels (DEBUG, INFO, WARNING, ERROR, CRITICAL)
- ✅ Automatic log rotation
- ✅ Context information (user, IP, URI, method)
- ✅ File-based logging
- ✅ Easy-to-use helper functions
- ✅ Performance tracking
- ✅ Security event logging

---

## 🔧 Configuration

### File: `application/config/logging.php`

```php
// Enable/disable logging
'enabled' => TRUE,

// Log directory
'log_dir' => APPPATH . 'logs/',

// Log levels
'log_levels' => array(
    'DEBUG'     => 0,
    'INFO'      => 1,
    'WARNING'   => 2,
    'ERROR'     => 3,
    'CRITICAL'  => 4
),

// Channels
'channels' => array(
    'app' => array('enabled' => TRUE, 'file' => 'app.log'),
    'auth' => array('enabled' => TRUE, 'file' => 'auth.log'),
    'database' => array('enabled' => TRUE, 'file' => 'database.log'),
    'api' => array('enabled' => TRUE, 'file' => 'api.log'),
    'error' => array('enabled' => TRUE, 'file' => 'error.log'),
    'performance' => array('enabled' => TRUE, 'file' => 'performance.log'),
    'security' => array('enabled' => TRUE, 'file' => 'security.log')
)
```

---

## 📚 Usage

### Load Logger

```php
// In controller
$this->load->library('Logger');
$this->logger->info('Message here');

// Or use helper
$this->load->helper('logging');
log_info('Message here');
```

### Basic Logging

```php
// Using library
$this->logger->debug('Debug message', 'app');
$this->logger->info('Info message', 'app');
$this->logger->warning('Warning message', 'app');
$this->logger->error('Error message', 'app');
$this->logger->critical('Critical message', 'app');

// Using helper functions
log_debug('Debug message');
log_info('Info message');
log_warning('Warning message');
log_error('Error message');
log_critical('Critical message');
```

### Channel-Specific Logging

```php
// Authentication
log_auth('User logged in', 'INFO');
log_failed_login('admin');
log_successful_login('admin');

// Database
log_database('Query executed', 'DEBUG');
log_query_time('SELECT * FROM users', 0.025);

// API
log_api('API request received', 'INFO');

// Security
log_security('Unauthorized access attempt', 'WARNING');
log_unauthorized_access('/admin/users', $user_id);

// Performance
log_performance('Page loaded', 'DEBUG');
log_execution_time('Database query', $start_time, $end_time);
```

### With Context

```php
$context = array(
    'user_id' => 123,
    'username' => 'admin',
    'ip_address' => '192.168.1.1'
);

log_info('User action performed', 'app', $context);
```

### Exception Logging

```php
try {
    // Some code
} catch (Exception $e) {
    log_exception($e);
}
```

---

## 📂 Log Files

Logs are stored in `application/logs/` directory with date prefix:

```
application/logs/
├── 2025-02-21_app.log
├── 2025-02-21_auth.log
├── 2025-02-21_database.log
├── 2025-02-21_api.log
├── 2025-02-21_error.log
├── 2025-02-21_performance.log
└── 2025-02-21_security.log
```

### Log Format

```
[2025-02-21 14:30:45] [INFO] [app] User logged in successfully | Context: {"user_id":"1","username":"admin","ip_address":"127.0.0.1"}
```

---

## 🎯 Common Use Cases

### Authentication Logging

```php
// In Auth controller
public function login()
{
    // Validate credentials
    if ($valid) {
        log_successful_login($username);
        // Login user
    } else {
        log_failed_login($username);
        // Show error
    }
}

public function logout()
{
    log_auth('User logged out');
    // Logout user
}
```

### API Logging

```php
// In API controller
public function get_sales()
{
    log_api('GET /api/v1/sales/summary/daily');
    
    // Validate API key
    if (!$valid_key) {
        log_security('Invalid API key attempt');
        return error_response('Invalid API key');
    }
    
    // Get data
    $data = $this->Sale_model->get_daily_summary();
    
    log_api('Sales data retrieved successfully');
    return success_response($data);
}
```

### Performance Logging

```php
$start = microtime(TRUE);

// Do something
$data = $this->expensive_operation();

$end = microtime(TRUE);
log_execution_time('Expensive operation', $start, $end);
```

### Error Logging

```php
try {
    $result = $this->risky_operation();
} catch (Exception $e) {
    log_exception($e);
    show_error('An error occurred');
}
```

---

## 🔍 Viewing Logs

### From Command Line

```bash
# View today's app logs
tail -f application/logs/2025-02-21_app.log

# View last 50 lines
tail -50 application/logs/2025-02-21_app.log

# Search for errors
grep ERROR application/logs/2025-02-21_app.log

# View all security logs
cat application/logs/2025-02-21_security.log
```

### From Code

```php
// Get last 100 lines of app logs
$logs = get_logs('app', 100);

// Display in view
foreach ($logs as $line) {
    echo $line . '<br>';
}
```

---

## 🔄 Log Rotation

Logs are automatically rotated when they exceed max file size (default: 10MB).

### Configuration

```php
'rotation' => array(
    'enabled' => TRUE,
    'max_files' => 30,      // Keep last 30 days
    'compress' => FALSE     // Compress old logs
)
```

### Manual Cleanup

```php
// In controller or command
cleanup_logs();
```

---

## 📊 Log Levels

| Level | Value | Use Case |
|-------|-------|----------|
| DEBUG | 0 | Detailed debugging information |
| INFO | 1 | General informational messages |
| WARNING | 2 | Warning messages for potential issues |
| ERROR | 3 | Error messages for failures |
| CRITICAL | 4 | Critical errors requiring immediate attention |

---

## 🔐 Security Logging

Important events to log:

```php
// Login attempts
log_failed_login($username);
log_successful_login($username);

// Authorization
log_unauthorized_access($resource, $user_id);

// Data modifications
log_security('User deleted', 'INFO');
log_security('Settings changed', 'INFO');

// Suspicious activity
log_security('Multiple failed login attempts', 'WARNING');
log_security('SQL injection attempt detected', 'CRITICAL');
```

---

## 📈 Performance Logging

Track performance metrics:

```php
// Query performance
$start = microtime(TRUE);
$result = $this->db->query($sql);
log_query_time($sql, microtime(TRUE) - $start);

// Page load time
$start = microtime(TRUE);
// ... page rendering ...
log_execution_time('Page load', $start);

// API response time
$start = microtime(TRUE);
$data = $this->get_data();
log_performance('API response', 'DEBUG');
```

---

## 🚨 Error Handling

### Automatic Error Logging

```php
// In index.php or config
set_error_handler('log_php_error');

// Errors will be automatically logged
```

### Manual Exception Logging

```php
try {
    // Code
} catch (Exception $e) {
    log_exception($e, 'error');
    show_error('An error occurred');
}
```

---

## 📋 Log Channels Reference

### App Channel
- General application events
- Business logic
- Data operations

### Auth Channel
- Login/logout events
- Authentication attempts
- Session management

### Database Channel
- Query execution
- Database errors
- Performance metrics

### API Channel
- API requests
- API responses
- API errors

### Error Channel
- Application errors
- Exceptions
- Fatal errors

### Performance Channel
- Execution times
- Query performance
- Page load times

### Security Channel
- Failed login attempts
- Unauthorized access
- Suspicious activity
- Data modifications

---

## ✅ Best Practices

1. **Log at appropriate levels**
   - Use DEBUG for detailed info
   - Use INFO for important events
   - Use WARNING for potential issues
   - Use ERROR for failures
   - Use CRITICAL for emergencies

2. **Include context**
   - User information
   - IP address
   - Request method/URI
   - Relevant IDs

3. **Don't log sensitive data**
   - Passwords
   - API keys
   - Credit card numbers
   - Personal information

4. **Monitor logs regularly**
   - Check error logs daily
   - Review security logs
   - Monitor performance

5. **Rotate logs**
   - Keep logs manageable
   - Archive old logs
   - Delete very old logs

---

## 🔧 Troubleshooting

### Logs not being created

1. Check `application/logs/` directory exists
2. Verify directory permissions (755)
3. Ensure logging is enabled in config
4. Check channel is enabled

### Logs too large

1. Enable log rotation
2. Reduce log level
3. Disable unnecessary channels
4. Run cleanup manually

### Performance impact

1. Disable DEBUG level in production
2. Disable unnecessary channels
3. Use file-based logging only
4. Monitor log I/O

---

**Last Updated**: 2025-02-21  
**Version**: 1.0

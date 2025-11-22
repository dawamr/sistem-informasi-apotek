<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Logging Helper
 * 
 * Helper functions untuk centralized logging
 */

// ============================================================================
// Global Logger Instance
// ============================================================================

$_logger = NULL;

/**
 * Get logger instance
 */
function get_logger()
{
    global $_logger;
    
    if ($_logger === NULL) {
        $CI =& get_instance();
        $CI->load->library('Logger');
        $_logger = $CI->logger;
    }
    
    return $_logger;
}

// ============================================================================
// Logging Functions
// ============================================================================

/**
 * Log debug message
 */
function log_debug($message, $channel = 'app', $context = array())
{
    return get_logger()->debug($message, $channel, $context);
}

/**
 * Log info message
 */
function log_info($message, $channel = 'app', $context = array())
{
    return get_logger()->info($message, $channel, $context);
}

/**
 * Log warning message
 */
function log_warning($message, $channel = 'app', $context = array())
{
    return get_logger()->warning($message, $channel, $context);
}

/**
 * Log error message
 */
function log_error($message, $channel = 'app', $context = array())
{
    return get_logger()->error($message, $channel, $context);
}

/**
 * Log critical message
 */
function log_critical($message, $channel = 'app', $context = array())
{
    return get_logger()->critical($message, $channel, $context);
}

// ============================================================================
// Channel-Specific Logging
// ============================================================================

/**
 * Log authentication event
 */
function log_auth($message, $level = 'INFO')
{
    return get_logger()->auth($message, $level);
}

/**
 * Log database event
 */
function log_database($message, $level = 'DEBUG')
{
    return get_logger()->database($message, $level);
}

/**
 * Log API event
 */
function log_api($message, $level = 'INFO')
{
    return get_logger()->api($message, $level);
}

/**
 * Log security event
 */
function log_security($message, $level = 'WARNING')
{
    return get_logger()->security($message, $level);
}

/**
 * Log performance event
 */
function log_performance($message, $level = 'DEBUG')
{
    return get_logger()->performance($message, $level);
}

// ============================================================================
// Exception Logging
// ============================================================================

/**
 * Log exception
 */
function log_exception($exception, $channel = 'error')
{
    $message = sprintf(
        "Exception: %s | File: %s | Line: %d | Message: %s",
        get_class($exception),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getMessage()
    );
    
    return get_logger()->error($message, $channel);
}

/**
 * Log PHP error
 */
function log_php_error($errno, $errstr, $errfile, $errline)
{
    $error_types = array(
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE_ERROR',
        E_CORE_WARNING => 'CORE_WARNING',
        E_COMPILE_ERROR => 'COMPILE_ERROR',
        E_COMPILE_WARNING => 'COMPILE_WARNING',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE',
        E_STRICT => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER_DEPRECATED'
    );
    
    $error_type = isset($error_types[$errno]) ? $error_types[$errno] : 'UNKNOWN';
    
    $message = sprintf(
        "[%s] %s in %s on line %d",
        $error_type,
        $errstr,
        $errfile,
        $errline
    );
    
    return get_logger()->error($message, 'error');
}

// ============================================================================
// Performance Logging
// ============================================================================

/**
 * Log execution time
 */
function log_execution_time($label, $start_time, $end_time = NULL)
{
    if ($end_time === NULL) {
        $end_time = microtime(TRUE);
    }
    
    $duration = ($end_time - $start_time) * 1000; // Convert to milliseconds
    
    $message = sprintf(
        "%s executed in %.2f ms",
        $label,
        $duration
    );
    
    return get_logger()->performance($message, 'DEBUG');
}

/**
 * Log query execution time
 */
function log_query_time($query, $duration)
{
    $message = sprintf(
        "Query executed in %.2f ms: %s",
        $duration * 1000,
        substr($query, 0, 100) . (strlen($query) > 100 ? '...' : '')
    );
    
    return get_logger()->database($message, 'DEBUG');
}

// ============================================================================
// Security Logging
// ============================================================================

/**
 * Log failed login attempt
 */
function log_failed_login($username, $ip_address = NULL)
{
    if ($ip_address === NULL) {
        $CI =& get_instance();
        $ip_address = $CI->input->ip_address();
    }
    
    $message = sprintf(
        "Failed login attempt for user '%s' from IP %s",
        $username,
        $ip_address
    );
    
    return get_logger()->security($message, 'WARNING');
}

/**
 * Log successful login
 */
function log_successful_login($username, $ip_address = NULL)
{
    if ($ip_address === NULL) {
        $CI =& get_instance();
        $ip_address = $CI->input->ip_address();
    }
    
    $message = sprintf(
        "Successful login for user '%s' from IP %s",
        $username,
        $ip_address
    );
    
    return get_logger()->auth($message, 'INFO');
}

/**
 * Log unauthorized access attempt
 */
function log_unauthorized_access($resource, $user_id = NULL, $ip_address = NULL)
{
    if ($ip_address === NULL) {
        $CI =& get_instance();
        $ip_address = $CI->input->ip_address();
    }
    
    $message = sprintf(
        "Unauthorized access attempt to '%s' by user %s from IP %s",
        $resource,
        $user_id ?: 'unknown',
        $ip_address
    );
    
    return get_logger()->security($message, 'WARNING');
}

// ============================================================================
// Utility Functions
// ============================================================================

/**
 * Get logs for viewing
 */
function get_logs($channel = 'app', $lines = 100)
{
    return get_logger()->get_logs($channel, $lines);
}

/**
 * Cleanup old logs
 */
function cleanup_logs()
{
    return get_logger()->cleanup_old_logs();
}

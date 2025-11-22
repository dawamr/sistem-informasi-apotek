<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Error Handler Configuration
 * 
 * Setup untuk error handling dan output buffering
 */

// ============================================================================
// Output Buffering
// ============================================================================

// Start output buffering untuk prevent header errors
if (!ob_get_level()) {
    ob_start();
}

// ============================================================================
// Error Reporting
// ============================================================================

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 0);
}

// ============================================================================
// Session Configuration
// ============================================================================

// Set session save path
$session_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'apotek_sessions';

// Ensure directory exists
if (!is_dir($session_path)) {
    @mkdir($session_path, 0755, TRUE);
}

// Set session configuration
if (is_writable($session_path)) {
    ini_set('session.save_path', $session_path);
} else {
    ini_set('session.save_path', sys_get_temp_dir());
}

// ============================================================================
// Logging Configuration
// ============================================================================

// Ensure logs directory exists
$logs_dir = APPPATH . 'logs';
if (!is_dir($logs_dir)) {
    @mkdir($logs_dir, 0755, TRUE);
}

// ============================================================================
// Error Handler Function
// ============================================================================

/**
 * Custom error handler
 */
function custom_error_handler($errno, $errstr, $errfile, $errline)
{
    // Suppress @ operator errors
    if (error_reporting() === 0) {
        return FALSE;
    }

    // Log the error
    $error_message = sprintf(
        "[%s] %s in %s on line %d",
        get_error_type($errno),
        $errstr,
        $errfile,
        $errline
    );

    error_log($error_message, 3, APPPATH . 'logs/php_errors.log');

    // Don't execute PHP internal error handler
    return TRUE;
}

/**
 * Get error type string
 */
function get_error_type($errno)
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

    return isset($error_types[$errno]) ? $error_types[$errno] : 'UNKNOWN';
}

// Set custom error handler
set_error_handler('custom_error_handler');

// ============================================================================
// Exception Handler
// ============================================================================

/**
 * Custom exception handler
 */
function custom_exception_handler($exception)
{
    $error_message = sprintf(
        "Exception: %s | File: %s | Line: %d | Message: %s",
        get_class($exception),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getMessage()
    );

    error_log($error_message, 3, APPPATH . 'logs/exceptions.log');

    // Display error in development
    if (ENVIRONMENT === 'development') {
        echo '<pre>';
        echo $error_message;
        echo '</pre>';
    } else {
        echo 'An error occurred. Please try again later.';
    }
}

// Set custom exception handler
set_exception_handler('custom_exception_handler');

// ============================================================================
// Shutdown Handler
// ============================================================================

/**
 * Handle fatal errors
 */
function shutdown_handler()
{
    $error = error_get_last();
    
    if ($error !== NULL && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
        $error_message = sprintf(
            "Fatal Error: %s in %s on line %d",
            $error['message'],
            $error['file'],
            $error['line']
        );

        error_log($error_message, 3, APPPATH . 'logs/fatal_errors.log');

        // Clear output buffer
        ob_end_clean();

        // Display error
        if (ENVIRONMENT === 'development') {
            echo '<pre>';
            echo $error_message;
            echo '</pre>';
        } else {
            echo 'A fatal error occurred. Please contact administrator.';
        }
    }
}

// Register shutdown handler
register_shutdown_function('shutdown_handler');

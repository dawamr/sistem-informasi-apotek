<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Session Configuration
 * 
 * Session configuration untuk Sistem Informasi Apotek
 */

// ============================================================================
// Session Driver
// ============================================================================

$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'apotek_session';
$config['sess_expiration'] = 7200; // 2 hours
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = FALSE;

// ============================================================================
// Session Storage Path Setup
// ============================================================================

// Try multiple session path options
$session_paths = array(
    APPPATH . 'cache' . DIRECTORY_SEPARATOR . 'sessions',
    sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'apotek_sessions',
    '/tmp/apotek_sessions',
    sys_get_temp_dir()
);

$session_path = '';
foreach ($session_paths as $path) {
    // Try to create directory
    if (!is_dir($path)) {
        @mkdir($path, 0755, TRUE);
    }
    
    // Check if writable
    if (is_dir($path) && is_writable($path)) {
        $session_path = $path;
        break;
    }
}

// If no writable path found, use PHP default
if (empty($session_path)) {
    $session_path = '';
}

$config['sess_save_path'] = $session_path;

// ============================================================================
// Cookie Configuration
// ============================================================================

$config['cookie_prefix'] = 'apotek_';
$config['cookie_domain'] = '';
$config['cookie_path'] = '/';
$config['cookie_secure'] = FALSE;
$config['cookie_httponly'] = TRUE;
$config['cookie_samesite'] = 'Lax';

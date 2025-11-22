<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Logging Configuration
 * 
 * Centralized logging untuk Sistem Informasi Apotek
 */

$config = array(
    // ============================================================================
    // Log Levels
    // ============================================================================
    
    'log_levels' => array(
        'DEBUG'     => 0,
        'INFO'      => 1,
        'WARNING'   => 2,
        'ERROR'     => 3,
        'CRITICAL'  => 4
    ),
    
    // ============================================================================
    // Log Configuration
    // ============================================================================
    
    'default_level' => 'INFO',
    
    // Enable/disable logging
    'enabled' => TRUE,
    
    // Log directory
    'log_dir' => APPPATH . 'logs/',
    
    // Log file format
    'log_file_format' => 'Y-m-d',
    
    // Maximum log file size (in MB)
    'max_file_size' => 10,
    
    // ============================================================================
    // Log Channels
    // ============================================================================
    
    'channels' => array(
        // General application logs
        'app' => array(
            'enabled' => TRUE,
            'file' => 'app.log',
            'level' => 'INFO'
        ),
        
        // Authentication logs
        'auth' => array(
            'enabled' => TRUE,
            'file' => 'auth.log',
            'level' => 'INFO'
        ),
        
        // Database logs
        'database' => array(
            'enabled' => TRUE,
            'file' => 'database.log',
            'level' => 'DEBUG'
        ),
        
        // API logs
        'api' => array(
            'enabled' => TRUE,
            'file' => 'api.log',
            'level' => 'INFO'
        ),
        
        // Error logs
        'error' => array(
            'enabled' => TRUE,
            'file' => 'error.log',
            'level' => 'ERROR'
        ),
        
        // Performance logs
        'performance' => array(
            'enabled' => TRUE,
            'file' => 'performance.log',
            'level' => 'DEBUG'
        ),
        
        // Security logs
        'security' => array(
            'enabled' => TRUE,
            'file' => 'security.log',
            'level' => 'WARNING'
        )
    ),
    
    // ============================================================================
    // Log Format
    // ============================================================================
    
    'format' => '[{timestamp}] [{level}] [{channel}] {message}',
    
    'timestamp_format' => 'Y-m-d H:i:s',
    
    // ============================================================================
    // Context Information
    // ============================================================================
    
    'include_context' => TRUE,
    
    'context_fields' => array(
        'user_id',
        'username',
        'ip_address',
        'user_agent',
        'method',
        'uri'
    ),
    
    // ============================================================================
    // Rotation
    // ============================================================================
    
    'rotation' => array(
        'enabled' => TRUE,
        'max_files' => 30,  // Keep last 30 days of logs
        'compress' => FALSE
    ),
    
    // ============================================================================
    // Handlers
    // ============================================================================
    
    'handlers' => array(
        'file' => TRUE,
        'database' => FALSE,  // Set to TRUE to log to database
        'email' => FALSE,     // Set to TRUE to send critical errors via email
        'slack' => FALSE      // Set to TRUE to send to Slack
    ),
    
    // ============================================================================
    // Email Configuration (if enabled)
    // ============================================================================
    
    'email' => array(
        'enabled' => FALSE,
        'to' => 'admin@apotek.local',
        'subject' => 'Critical Error - Sistem Apotek',
        'min_level' => 'CRITICAL'
    ),
    
    // ============================================================================
    // Slack Configuration (if enabled)
    // ============================================================================
    
    'slack' => array(
        'enabled' => FALSE,
        'webhook_url' => '',
        'channel' => '#errors',
        'username' => 'Apotek Logger',
        'min_level' => 'ERROR'
    )
);

return $config;

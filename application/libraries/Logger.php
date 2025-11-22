<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Logger Library
 * 
 * Centralized logging untuk Sistem Informasi Apotek
 */
class Logger {

    private $CI;
    private $config;
    private $log_levels;
    private $current_level;

    public function __construct()
    {
        $this->CI =& get_instance();
        // Load logging config; with namespace for safety
        $this->CI->config->load('logging', TRUE);

        // Helper to fetch a config key from 'logging' section or global
        $getCfg = function($key, $default = NULL) {
            $val = $this->CI->config->item($key, 'logging');
            if ($val === NULL) {
                $val = $this->CI->config->item($key);
            }
            return ($val === NULL) ? $default : $val;
        };

        // Defaults
        $defaults = array(
            'enabled' => TRUE,
            'log_dir' => APPPATH . 'logs/',
            'log_levels' => array(
                'DEBUG' => 0,
                'INFO' => 1,
                'WARNING' => 2,
                'ERROR' => 3,
                'CRITICAL' => 4
            ),
            'default_level' => 'INFO',
            'channels' => array(
                'app' => array('enabled' => TRUE, 'file' => 'app.log', 'level' => 'INFO'),
                'auth' => array('enabled' => TRUE, 'file' => 'auth.log', 'level' => 'INFO'),
                'database' => array('enabled' => TRUE, 'file' => 'database.log', 'level' => 'DEBUG'),
                'api' => array('enabled' => TRUE, 'file' => 'api.log', 'level' => 'INFO'),
                'error' => array('enabled' => TRUE, 'file' => 'error.log', 'level' => 'ERROR'),
                'performance' => array('enabled' => TRUE, 'file' => 'performance.log', 'level' => 'DEBUG'),
                'security' => array('enabled' => TRUE, 'file' => 'security.log', 'level' => 'WARNING'),
            ),
            'format' => '[{timestamp}] [{level}] [{channel}] {message}',
            'timestamp_format' => 'Y-m-d H:i:s',
            'log_file_format' => 'Y-m-d',
            'max_file_size' => 10,
            'include_context' => TRUE,
            'context_fields' => array('user_id','username','ip_address','user_agent','method','uri'),
            'rotation' => array('enabled' => TRUE, 'max_files' => 30, 'compress' => FALSE),
        );

        // Build effective config
        $this->config = array(
            'enabled' => (bool)$getCfg('enabled', $defaults['enabled']),
            'log_dir' => rtrim($getCfg('log_dir', $defaults['log_dir']), '/').'/',
            'log_levels' => $getCfg('log_levels', $defaults['log_levels']),
            'default_level' => $getCfg('default_level', $defaults['default_level']),
            'channels' => $getCfg('channels', $defaults['channels']),
            'format' => $getCfg('format', $defaults['format']),
            'timestamp_format' => $getCfg('timestamp_format', $defaults['timestamp_format']),
            'log_file_format' => $getCfg('log_file_format', $defaults['log_file_format']),
            'max_file_size' => (int)$getCfg('max_file_size', $defaults['max_file_size']),
            'include_context' => (bool)$getCfg('include_context', $defaults['include_context']),
            'context_fields' => $getCfg('context_fields', $defaults['context_fields']),
            'rotation' => $getCfg('rotation', $defaults['rotation']),
        );

        $this->log_levels = is_array($this->config['log_levels']) ? $this->config['log_levels'] : $defaults['log_levels'];
        $defaultLevel = isset($this->config['default_level']) && isset($this->log_levels[$this->config['default_level']])
            ? $this->config['default_level']
            : $defaults['default_level'];
        $this->current_level = $this->log_levels[$defaultLevel];

        // Ensure log directory exists
        $this->ensure_log_directory();
    }

    /**
     * Ensure log directory exists
     */
    private function ensure_log_directory()
    {
        $log_dir = $this->config['log_dir'];
        if (!is_dir($log_dir)) {
            @mkdir($log_dir, 0755, TRUE);
        }
    }

    /**
     * Log message
     */
    public function log($message, $level = 'INFO', $channel = 'app', $context = array())
    {
        if (!$this->config['enabled']) {
            return FALSE;
        }

        // Check if level is enabled
        if (!isset($this->log_levels[$level])) {
            $level = 'INFO';
        }

        $level_value = $this->log_levels[$level];
        if ($level_value < $this->current_level) {
            return FALSE;
        }

        // Get channel config
        if (!isset($this->config['channels'][$channel])) {
            $channel = 'app';
        }

        $channel_config = $this->config['channels'][$channel];
        if (!$channel_config['enabled']) {
            return FALSE;
        }

        // Format log message
        $log_entry = $this->format_log_entry($message, $level, $channel, $context);

        // Write to file
        $this->write_to_file($channel_config['file'], $log_entry);

        return TRUE;
    }

    /**
     * Format log entry
     */
    private function format_log_entry($message, $level, $channel, $context = array())
    {
        $timestamp = date($this->config['timestamp_format']);
        
        $log_entry = $this->config['format'];
        $log_entry = str_replace('{timestamp}', $timestamp, $log_entry);
        $log_entry = str_replace('{level}', $level, $log_entry);
        $log_entry = str_replace('{channel}', $channel, $log_entry);
        $log_entry = str_replace('{message}', $message, $log_entry);

        // Add context if enabled
        if (!empty($this->config['include_context']) && !empty($context)) {
            $context_str = json_encode($context);
            $log_entry .= ' | Context: ' . $context_str;
        }

        return $log_entry . PHP_EOL;
    }

    /**
     * Write to file
     */
    private function write_to_file($filename, $content)
    {
        $log_dir = $this->config['log_dir'];
        $date_suffix = date($this->config['log_file_format']);
        $filepath = $log_dir . $date_suffix . '_' . $filename;

        // Check file size and rotate if necessary
        if (file_exists($filepath)) {
            $file_size = filesize($filepath) / (1024 * 1024); // Convert to MB
            if ($file_size >= $this->config['max_file_size']) {
                $this->rotate_log_file($filepath);
            }
        }

        // Write to file
        $handle = @fopen($filepath, 'a');
        if ($handle) {
            fwrite($handle, $content);
            fclose($handle);
        }
    }

    /**
     * Rotate log file
     */
    private function rotate_log_file($filepath)
    {
        $timestamp = date('YmdHis');
        $rotated_path = $filepath . '.' . $timestamp;
        
        if (@rename($filepath, $rotated_path)) {
            // Optionally compress
            if ($this->config['rotation']['compress']) {
                // Implement compression if needed
            }
        }
    }

    /**
     * Get context information
     */
    private function get_context()
    {
        $context = array();
        
        if ($this->config['include_context']) {
            $context_fields = $this->config['context_fields'];
            
            foreach ($context_fields as $field) {
                switch ($field) {
                    case 'user_id':
                        $context['user_id'] = $this->CI->session->userdata('user_id') ?: 'guest';
                        break;
                    case 'username':
                        $context['username'] = $this->CI->session->userdata('username') ?: 'guest';
                        break;
                    case 'ip_address':
                        $context['ip_address'] = $this->CI->input->ip_address();
                        break;
                    case 'user_agent':
                        $context['user_agent'] = substr($this->CI->input->user_agent(), 0, 100);
                        break;
                    case 'method':
                        $context['method'] = $this->CI->input->server('REQUEST_METHOD');
                        break;
                    case 'uri':
                        $context['uri'] = $this->CI->uri->uri_string();
                        break;
                }
            }
        }
        
        return $context;
    }

    /**
     * Shortcut methods
     */
    public function debug($message, $channel = 'app', $context = array())
    {
        return $this->log($message, 'DEBUG', $channel, $context ?: $this->get_context());
    }

    public function info($message, $channel = 'app', $context = array())
    {
        return $this->log($message, 'INFO', $channel, $context ?: $this->get_context());
    }

    public function warning($message, $channel = 'app', $context = array())
    {
        return $this->log($message, 'WARNING', $channel, $context ?: $this->get_context());
    }

    public function error($message, $channel = 'app', $context = array())
    {
        return $this->log($message, 'ERROR', $channel, $context ?: $this->get_context());
    }

    public function critical($message, $channel = 'app', $context = array())
    {
        return $this->log($message, 'CRITICAL', $channel, $context ?: $this->get_context());
    }

    /**
     * Channel-specific shortcuts
     */
    public function auth($message, $level = 'INFO')
    {
        return $this->log($message, $level, 'auth', $this->get_context());
    }

    public function database($message, $level = 'DEBUG')
    {
        return $this->log($message, $level, 'database', $this->get_context());
    }

    public function api($message, $level = 'INFO')
    {
        return $this->log($message, $level, 'api', $this->get_context());
    }

    public function security($message, $level = 'WARNING')
    {
        return $this->log($message, $level, 'security', $this->get_context());
    }

    public function performance($message, $level = 'DEBUG')
    {
        return $this->log($message, $level, 'performance', $this->get_context());
    }

    /**
     * Get log file contents
     */
    public function get_logs($channel = 'app', $lines = 100)
    {
        $log_dir = $this->config['log_dir'];
        $date_suffix = date($this->config['log_file_format']);
        $filepath = $log_dir . $date_suffix . '_' . $this->config['channels'][$channel]['file'];

        if (!file_exists($filepath)) {
            return array();
        }

        $file_contents = file($filepath);
        return array_slice($file_contents, -$lines);
    }

    /**
     * Clear old logs
     */
    public function cleanup_old_logs()
    {
        $log_dir = $this->config['log_dir'];
        $max_files = isset($this->config['rotation']['max_files']) ? (int)$this->config['rotation']['max_files'] : 30;
        $files = @scandir($log_dir) ?: array();
        
        if (count($files) > $max_files + 2) { // +2 for . and ..
            $dir = rtrim($log_dir, '/').'/';
            usort($files, function($a, $b) use ($dir) {
                return @filemtime($dir . $a) - @filemtime($dir . $b);
            });
            
            $files_to_delete = array_slice($files, 0, count($files) - $max_files);
            
            foreach ($files_to_delete as $file) {
                if ($file !== '.' && $file !== '..') {
                    @unlink($dir . $file);
                }
            }
        }
    }
}

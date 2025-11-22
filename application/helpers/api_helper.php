<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API Helper
 * 
 * Helper functions untuk API endpoints
 */

/**
 * Format success response
 */
if (!function_exists('api_success')) {
    function api_success($data = NULL, $message = 'Success', $http_code = 200)
    {
        $response = array(
            'success' => TRUE,
            'message' => $message,
            'data' => $data
        );

        http_response_code($http_code);
        return json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

/**
 * Format error response
 */
if (!function_exists('api_error')) {
    function api_error($error_code, $message, $http_code = 400)
    {
        $response = array(
            'success' => FALSE,
            'error' => array(
                'code' => $error_code,
                'message' => $message
            )
        );

        http_response_code($http_code);
        return json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

/**
 * Format currency (IDR)
 */
if (!function_exists('format_currency')) {
    function format_currency($amount)
    {
        return number_format($amount, 2, '.', ',');
    }
}

/**
 * Validate date format (YYYY-MM-DD)
 */
if (!function_exists('validate_date')) {
    function validate_date($date)
    {
        $pattern = '/^\d{4}-\d{2}-\d{2}$/';
        if (!preg_match($pattern, $date)) {
            return FALSE;
        }

        $date_parts = explode('-', $date);
        return checkdate($date_parts[1], $date_parts[2], $date_parts[0]);
    }
}

/**
 * Get date range by period
 */
if (!function_exists('get_date_range')) {
    function get_date_range($period, $reference_date = NULL)
    {
        if (empty($reference_date)) {
            $reference_date = date('Y-m-d');
        }

        $reference_time = strtotime($reference_date);

        switch ($period) {
            case 'daily':
                return array(
                    'start_date' => $reference_date,
                    'end_date' => $reference_date
                );

            case 'weekly':
                $monday = date('Y-m-d', strtotime('monday this week', $reference_time));
                $sunday = date('Y-m-d', strtotime('sunday this week', $reference_time));
                return array(
                    'start_date' => $monday,
                    'end_date' => $sunday
                );

            case 'monthly':
                $first_day = date('Y-m-01', $reference_time);
                $last_day = date('Y-m-t', $reference_time);
                return array(
                    'start_date' => $first_day,
                    'end_date' => $last_day
                );

            default:
                return array(
                    'start_date' => $reference_date,
                    'end_date' => $reference_date
                );
        }
    }
}

/**
 * Error codes
 */
if (!function_exists('get_error_codes')) {
    function get_error_codes()
    {
        return array(
            'MISSING_API_KEY' => 'Header X-API-KEY tidak ditemukan',
            'INVALID_API_KEY' => 'API key tidak valid atau sudah nonaktif',
            'VALIDATION_ERROR' => 'Parameter tidak valid',
            'NOT_FOUND' => 'Resource tidak ditemukan',
            'UNAUTHORIZED' => 'Tidak memiliki akses',
            'SERVER_ERROR' => 'Terjadi kesalahan di server'
        );
    }
}

/**
 * Get error message by code
 */
if (!function_exists('get_error_message')) {
    function get_error_message($error_code)
    {
        $codes = get_error_codes();
        return isset($codes[$error_code]) ? $codes[$error_code] : 'Unknown error';
    }
}

/**
 * Format medicine data
 */
if (!function_exists('format_medicine')) {
    function format_medicine($medicine)
    {
        return array(
            'medicine_id' => (int)$medicine['id'],
            'code' => $medicine['code'],
            'name' => $medicine['name'],
            'category' => isset($medicine['category_name']) ? $medicine['category_name'] : NULL,
            'unit' => $medicine['unit'],
            'price' => (float)$medicine['price'],
            'current_stock' => (int)$medicine['current_stock'],
            'is_active' => (bool)$medicine['is_active']
        );
    }
}

/**
 * Format sale data
 */
if (!function_exists('format_sale')) {
    function format_sale($sale)
    {
        return array(
            'sale_id' => (int)$sale['id'],
            'invoice_number' => $sale['invoice_number'],
            'sale_date' => $sale['sale_date'],
            'sale_time' => $sale['sale_time'],
            'total_amount' => (float)$sale['total_amount'],
            'total_items' => (int)$sale['total_items']
        );
    }
}

/**
 * Format user data
 */
if (!function_exists('format_user')) {
    function format_user($user)
    {
        return array(
            'user_id' => (int)$user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'role' => $user['role'],
            'active' => (bool)$user['active']
        );
    }
}

/**
 * Generate invoice number
 */
if (!function_exists('generate_invoice_number')) {
    function generate_invoice_number()
    {
        $date = date('Ymd');
        $random = strtoupper(substr(md5(time() . rand()), 0, 4));
        return 'INV-' . $date . '-' . $random;
    }
}

/**
 * Log API activity
 */
if (!function_exists('log_api_activity')) {
    function log_api_activity($endpoint, $method, $status, $api_key_id = NULL)
    {
        // TODO: Implement logging if needed
        // log_message('info', "API: $method $endpoint - Status: $status");
    }
}

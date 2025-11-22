<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API_Controller
 * 
 * Base controller untuk semua API endpoints
 * Menyediakan:
 * - API key validation
 * - JSON response formatting
 * - Error handling
 * - Consistent response structure
 */
class API_Controller extends CI_Controller {

    /**
     * @var CI_Input
     */
    public $input;

    /**
     * @var Api_key_model
     */
    public $Api_key_model;

    /**
     * @var int API key ID (setelah validasi)
     */
    protected $api_key_id;

    /**
     * @var string API key name
     */
    protected $api_key_name;

    /**
     * @var bool Status validasi API key
     */
    protected $is_authenticated = FALSE;

    public function __construct()
    {
        parent::__construct();
        
        // Set header JSON
        header('Content-Type: application/json; charset=utf-8');
        
        // Load models
        $this->load->model('Api_key_model');
        
        // Validasi API key
        $this->validate_api_key();
    }

    /**
     * Validasi API key dari header
     */
    protected function validate_api_key()
    {
        // Get API key dari header
        $api_key = $this->input->get_request_header('X-API-KEY');
        
        if (empty($api_key)) {
            $this->error_response('MISSING_API_KEY', 'Header X-API-KEY tidak ditemukan', 401);
        }

        // Validasi API key
        $key_data = $this->Api_key_model->get_by_key($api_key);
        
        if (!$key_data) {
            $this->error_response('INVALID_API_KEY', 'API key tidak valid atau sudah nonaktif', 401);
        }

        if (!$key_data['active']) {
            $this->error_response('INVALID_API_KEY', 'API key sudah dinonaktifkan', 401);
        }

        // Set authenticated
        $this->is_authenticated = TRUE;
        $this->api_key_id = $key_data['id'];
        $this->api_key_name = $key_data['name'];
    }

    /**
     * Success response
     * 
     * @param mixed $data Data yang akan di-return
     * @param string $message Pesan sukses (optional)
     * @param int $http_code HTTP status code (default: 200)
     */
    protected function success_response($data = NULL, $message = 'Success', $http_code = 200)
    {
        $response = array(
            'success' => TRUE,
            'message' => $message,
            'data' => $data
        );

        http_response_code($http_code);
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Error response
     * 
     * @param string $error_code Error code
     * @param string $message Error message
     * @param int $http_code HTTP status code (default: 400)
     */
    protected function error_response($error_code, $message, $http_code = 400)
    {
        $response = array(
            'success' => FALSE,
            'error' => array(
                'code' => $error_code,
                'message' => $message
            )
        );

        http_response_code($http_code);
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Validasi required parameter
     * 
     * @param array $required_params Array nama parameter yang required
     * @param array $params Array parameter yang akan di-check
     * @return bool TRUE jika valid, FALSE jika ada yang missing
     */
    protected function validate_required_params($required_params, $params)
    {
        foreach ($required_params as $param) {
            if (!isset($params[$param]) || empty($params[$param])) {
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * Validasi format date (YYYY-MM-DD)
     * 
     * @param string $date Date string
     * @return bool TRUE jika valid
     */
    protected function validate_date_format($date)
    {
        $pattern = '/^\d{4}-\d{2}-\d{2}$/';
        if (!preg_match($pattern, $date)) {
            return FALSE;
        }

        // Check if valid date
        $date_parts = explode('-', $date);
        return checkdate($date_parts[1], $date_parts[2], $date_parts[0]);
    }

    /**
     * Get date parameter dengan default hari ini
     * 
     * @param string $param_name Nama parameter
     * @return string Date dalam format YYYY-MM-DD
     */
    protected function get_date_param($param_name = 'date')
    {
        $date = $this->input->get($param_name);
        
        if (empty($date)) {
            return date('Y-m-d');
        }

        if (!$this->validate_date_format($date)) {
            $this->error_response('VALIDATION_ERROR', "Parameter '$param_name' harus format YYYY-MM-DD", 400);
        }

        return $date;
    }

    /**
     * Get integer parameter dengan default
     * 
     * @param string $param_name Nama parameter
     * @param int $default Default value
     * @param int $min Minimum value (optional)
     * @param int $max Maximum value (optional)
     * @return int
     */
    protected function get_int_param($param_name, $default = 0, $min = NULL, $max = NULL)
    {
        $value = $this->input->get($param_name);
        
        if (empty($value)) {
            return $default;
        }

        $value = (int) $value;

        if ($min !== NULL && $value < $min) {
            $value = $min;
        }

        if ($max !== NULL && $value > $max) {
            $value = $max;
        }

        return $value;
    }

    /**
     * Get string parameter
     * 
     * @param string $param_name Nama parameter
     * @param string $default Default value
     * @return string
     */
    protected function get_string_param($param_name, $default = '')
    {
        $value = $this->input->get($param_name);
        return !empty($value) ? trim($value) : $default;
    }

    /**
     * Get period parameter (daily, weekly, monthly)
     * 
     * @param string $param_name Nama parameter
     * @param string $default Default value
     * @return string
     */
    protected function get_period_param($param_name = 'period', $default = 'daily')
    {
        $value = $this->get_string_param($param_name, $default);
        
        $valid_periods = array('daily', 'weekly', 'monthly');
        if (!in_array($value, $valid_periods)) {
            return $default;
        }

        return $value;
    }

    /**
     * Get date range berdasarkan period
     * 
     * @param string $period Period (daily, weekly, monthly)
     * @param string $reference_date Reference date (default: today)
     * @return array Array dengan 'start_date' dan 'end_date'
     */
    protected function get_date_range($period, $reference_date = NULL)
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
                // Get Monday of the week
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

    /**
     * Format currency (IDR)
     * 
     * @param float $amount Amount
     * @return string Formatted amount
     */
    protected function format_currency($amount)
    {
        return number_format($amount, 2, '.', ',');
    }

    /**
     * Log API request (optional)
     * 
     * @param string $endpoint Endpoint yang di-akses
     * @param string $method HTTP method
     * @param array $params Parameters
     */
    protected function log_request($endpoint, $method, $params = array())
    {
        // TODO: Implement logging if needed
        // log_message('info', "API Request: $method $endpoint");
    }
}

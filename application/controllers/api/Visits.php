<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'core/API_Controller.php');

/**
 * Visits API Controller
 * 
 * Endpoints:
 * - GET /api/v1/visits/summary - Total visits/transactions
 */
class Visits extends API_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Sale_model');
        $this->load->helper('api');
    }

    /**
     * GET /api/v1/visits/summary
     * 
     * Get total visits (based on transactions)
     * 
     * Query Parameters:
     * - date (required): YYYY-MM-DD format
     */
    public function summary()
    {
        try {
            // Get date parameter
            $date = $this->input->get('date');

            if (empty($date)) {
                $this->error_response('VALIDATION_ERROR', "Parameter 'date' wajib diisi dengan format YYYY-MM-DD", 400);
            }

            if (!$this->validate_date_format($date)) {
                $this->error_response('VALIDATION_ERROR', "Parameter 'date' harus format YYYY-MM-DD", 400);
            }

            // Get total transactions (which equals total visits)
            $total_transactions = $this->Sale_model->count_by_date($date);

            // Format response
            $response = array(
                'date' => $date,
                'total_transactions' => (int)$total_transactions,
                'total_visits' => (int)$total_transactions
            );

            // Return success response
            $this->success_response($response, 'Visits summary retrieved successfully');

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }
}

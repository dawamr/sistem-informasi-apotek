<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'core/API_Controller.php');

/**
 * Stock API Controller
 * 
 * Endpoints:
 * - GET /api/v1/stock/check - Check medicine stock
 */
class Stock extends API_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Medicine_model');
        $this->load->helper('api');
    }

    /**
     * GET /api/v1/stock/check
     * 
     * Check medicine stock by code or search by name
     * 
     * Query Parameters:
     * - code (optional): Medicine code
     * - q (optional): Search by name (LIKE query)
     * 
     * Note: Either 'code' or 'q' must be provided
     */
    public function check()
    {
        try {
            // Get parameters
            $code = $this->get_string_param('code');
            $query = $this->get_string_param('q');

            // Validate that at least one parameter is provided
            if (empty($code) && empty($query)) {
                $this->error_response('VALIDATION_ERROR', "Parameter 'code' atau 'q' harus diisi", 400);
            }

            $results = array();

            // Search by code
            if (!empty($code)) {
                $medicine = $this->Medicine_model->get_by_code($code);
                
                if ($medicine) {
                    $results[] = array(
                        'medicine_id' => (int)$medicine['id'],
                        'code' => $medicine['code'],
                        'name' => $medicine['name'],
                        'unit' => $medicine['unit'],
                        'current_stock' => (int)$medicine['current_stock'],
                        'price' => (float)$medicine['price']
                    );
                }
            }
            // Search by name
            else if (!empty($query)) {
                $medicines = $this->Medicine_model->search($query);

                foreach ($medicines as $medicine) {
                    $results[] = array(
                        'medicine_id' => (int)$medicine['id'],
                        'code' => $medicine['code'],
                        'name' => $medicine['name'],
                        'unit' => $medicine['unit'],
                        'current_stock' => (int)$medicine['current_stock'],
                        'price' => (float)$medicine['price']
                    );
                }
            }

            // Format response
            $response = array(
                'query' => !empty($code) ? $code : $query,
                'results' => $results
            );

            // Return success response
            $this->success_response($response, 'Stock check completed successfully');

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }
}

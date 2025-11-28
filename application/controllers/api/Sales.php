<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'core/API_Controller.php');

/**
 * Sales API Controller
 * 
 * Endpoints:
 * - GET /api/v1/sales/summary/daily - Daily sales summary
 * - GET /api/v1/sales/items-by-day - Items sold per day
 * - GET /api/v1/sales/top-products - Top selling products
 */
class Sales extends API_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Sale_model');
        $this->load->model('Sale_item_model');
        $this->load->helper('api');
    }

    /**
     * GET /api/v1/sales/summary/daily
     * 
     * Get daily sales summary
     * 
     * Query Parameters:
     * - date (optional): YYYY-MM-DD format, default: today
     */
    public function summary_daily()
    {
        try {
            // Get date parameter
            $date = $this->get_date_param('date');

            // Get daily summary
            $summary = $this->Sale_model->get_daily_summary($date);

            // Return success response
            $this->success_response($summary, 'Daily sales summary retrieved successfully');

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/v1/sales/items-by-day
     * 
     * Get items sold per day
     * 
     * Query Parameters:
     * - date (required): YYYY-MM-DD format
     */
    public function items_by_day()
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

            // Get items by date
            $items = $this->Sale_item_model->get_items_by_date($date);

            // Format response
            $response = array(
                'date' => $date,
                'items' => $items
            );

            // Return success response
            $this->success_response($response, 'Items sold per day retrieved successfully');

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/v1/sales/top-products
     * 
     * Get top selling products
     * 
     * Query Parameters:
     * - period (optional): daily|weekly|monthly, default: daily
     * - date (optional): YYYY-MM-DD format, default: today
     * - limit (optional): number of products, default: 10, max: 100
     */
    public function top_products()
    {
        try {
            // Get parameters
            $period = $this->get_period_param('period', 'daily');
            $reference_date = $this->get_date_param('date');
            $limit = $this->get_int_param('limit', 10, 1, 100);

            // Get date range
            $date_range = $this->get_date_range($period, $reference_date);

            // Get top products
            $products = $this->Sale_item_model->get_top_products(
                $date_range['start_date'],
                $date_range['end_date'],
                $limit
            );

            // Add rank to products
            $ranked_products = array();
            foreach ($products as $index => $product) {
                $product['rank'] = $index + 1;
                $ranked_products[] = $product;
            }

            // Format response
            $response = array(
                'period' => $period,
                'start_date' => $date_range['start_date'],
                'end_date' => $date_range['end_date'],
                'top_products' => $ranked_products
            );

            // Return success response
            $this->success_response($response, 'Top products retrieved successfully');

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }
}

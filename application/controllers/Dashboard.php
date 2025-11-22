<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Controller
 * 
 * Main dashboard untuk authenticated users
 */
class Dashboard extends CI_Controller {

    /**
     * @var CI_Session
     */
    public $session;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Sale_model');
        $this->load->model('Medicine_model');
        $this->load->model('Attendance_model');
    }

    /**
     * Check if user is logged in
     */
    private function check_login()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    /**
     * Dashboard index
     */
    public function index()
    {
        $this->check_login();

        $data = array(
            'page_title' => 'Dashboard',
            'current_page' => 'dashboard',
            'current_user' => array(
                'name' => $this->session->userdata('name'),
                'role' => $this->session->userdata('role')
            )
        );

        // Get today's date
        $today = date('Y-m-d');

        // Get sales summary
        $sales_summary = $this->Sale_model->get_daily_summary($today);
        $data['total_sales'] = $sales_summary ? $sales_summary['total_sales_amount'] : 0;
        $data['total_transactions'] = $sales_summary ? $sales_summary['total_transactions'] : 0;
        $data['total_items_sold'] = $sales_summary ? $sales_summary['total_items_sold'] : 0;

        // Get low stock medicines
        $data['low_stock_medicines'] = $this->Medicine_model->get_low_stock();

        // Get today's attendance
        $data['today_attendance'] = $this->Attendance_model->get_by_date($today);

        // Get recent transactions
        $data['recent_sales'] = $this->Sale_model->get_recent(5);

        // Get sales trend (last 7 days)
        $data['trend'] = $this->Sale_model->get_sales_trend(7);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('dashboard/index', $data);
        $this->load->view('templates/footer', $data);
    }
}

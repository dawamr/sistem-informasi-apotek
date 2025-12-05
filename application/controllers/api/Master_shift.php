<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'core/API_Controller.php');

/**
 * Master_shift API Controller
 * 
 * Endpoints untuk mengelola master data jadwal shift:
 * - GET /api/v1/master/shifts - Get all shifts
 * - GET /api/v1/master/shifts/:id - Get shift by ID
 * - POST /api/v1/master/shifts - Create new shift
 * - PUT /api/v1/master/shifts/:id - Update shift
 * - DELETE /api/v1/master/shifts/:id - Delete shift
 */
class Master_shift extends API_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Shift_model');
        $this->load->helper('api');
    }

    /**
     * GET /api/v1/master/shifts
     * 
     * Get all shifts dengan filter
     * 
     * Query Parameters:
     * - date (optional): Filter by specific date (YYYY-MM-DD)
     * - start_date (optional): Filter from date (YYYY-MM-DD)
     * - end_date (optional): Filter to date (YYYY-MM-DD)
     * - shift_name (optional): Filter by shift name (Pagi, Siang, Malam)
     * - page (optional): Page number, default 1
     * - limit (optional): Items per page, default 50
     */
    public function index()
    {
        // Only allow GET requests
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->error_response('METHOD_NOT_ALLOWED', 'Only GET method is allowed', 405);
        }

        try {
            // Get parameters
            $date = $this->get_string_param('date');
            $start_date = $this->get_string_param('start_date');
            $end_date = $this->get_string_param('end_date');
            $shift_name = $this->get_string_param('shift_name');
            $page = $this->get_int_param('page', 1, 1);
            $limit = $this->get_int_param('limit', 50, 1, 100);

            // Build query
            $this->db->from('shifts');

            // Apply filters
            if (!empty($date)) {
                if (!$this->validate_date_format($date)) {
                    $this->error_response('VALIDATION_ERROR', 'Format date harus YYYY-MM-DD', 400);
                }
                $this->db->where('date', $date);
            } else if (!empty($start_date) && !empty($end_date)) {
                if (!$this->validate_date_format($start_date) || !$this->validate_date_format($end_date)) {
                    $this->error_response('VALIDATION_ERROR', 'Format date harus YYYY-MM-DD', 400);
                }
                $this->db->where('date >=', $start_date);
                $this->db->where('date <=', $end_date);
            }

            if (!empty($shift_name)) {
                $this->db->where('shift_name', $shift_name);
            }

            // Count total
            $total = $this->db->count_all_results('', FALSE);

            // Apply pagination
            $offset = ($page - 1) * $limit;
            $this->db->limit($limit, $offset);
            $this->db->order_by('date', 'DESC');
            $this->db->order_by('start_time', 'ASC');

            // Get data
            $shifts = $this->db->get()->result_array();

            // Format response
            $formatted_shifts = array();
            foreach ($shifts as $shift) {
                $formatted_shifts[] = array(
                    'id' => (int)$shift['id'],
                    'date' => $shift['date'],
                    'shift_name' => $shift['shift_name'],
                    'start_time' => $shift['start_time'],
                    'end_time' => $shift['end_time'],
                    'created_at' => $shift['created_at'],
                    'updated_at' => $shift['updated_at']
                );
            }

            $response = array(
                'shifts' => $formatted_shifts,
                'pagination' => array(
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                )
            );

            $this->success_response($response, 'Master jadwal shift berhasil diambil');

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/v1/master/shifts/:id
     * 
     * Get shift by ID
     */
    public function detail($id)
    {
        // Only allow GET requests
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->error_response('METHOD_NOT_ALLOWED', 'Only GET method is allowed', 405);
        }

        try {
            if (empty($id) || !is_numeric($id)) {
                $this->error_response('VALIDATION_ERROR', 'ID shift tidak valid', 400);
            }

            $shift = $this->Shift_model->get_by_id($id);

            if (!$shift) {
                $this->error_response('NOT_FOUND', 'Shift tidak ditemukan', 404);
            }

            // Format response
            $response = array(
                'id' => (int)$shift['id'],
                'date' => $shift['date'],
                'shift_name' => $shift['shift_name'],
                'start_time' => $shift['start_time'],
                'end_time' => $shift['end_time'],
                'created_at' => $shift['created_at'],
                'updated_at' => $shift['updated_at']
            );

            $this->success_response($response, 'Detail shift berhasil diambil');

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/v1/master/shifts
     * 
     * Create new shift
     * 
     * Request Body (JSON):
     * {
     *   "date": "2024-01-15",
     *   "shift_name": "Pagi",
     *   "start_time": "08:00:00",
     *   "end_time": "16:00:00"
     * }
     */
    public function create_post()
    {
        try {
            // Get JSON input
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data) {
                $this->error_response('VALIDATION_ERROR', 'Invalid JSON format', 400);
            }

            // Validate required fields
            $required = array('date', 'shift_name', 'start_time', 'end_time');
            if (!$this->validate_required_params($required, $data)) {
                $this->error_response('VALIDATION_ERROR', 'Field date, shift_name, start_time, dan end_time wajib diisi', 400);
            }

            // Validate date format
            if (!$this->validate_date_format($data['date'])) {
                $this->error_response('VALIDATION_ERROR', 'Format date harus YYYY-MM-DD', 400);
            }

            // Validate shift name
            $valid_shifts = array('Pagi', 'Siang', 'Malam');
            if (!in_array($data['shift_name'], $valid_shifts)) {
                $this->error_response('VALIDATION_ERROR', 'Shift name harus salah satu dari: Pagi, Siang, Malam', 400);
            }

            // Create shift
            $shift_id = $this->Shift_model->create($data);

            if ($shift_id) {
                $new_shift = $this->Shift_model->get_by_id($shift_id);
                $this->success_response($new_shift, 'Jadwal shift berhasil ditambahkan', 201);
            } else {
                $this->error_response('SERVER_ERROR', 'Gagal menambahkan jadwal shift', 500);
            }

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * PUT /api/v1/master/shifts/:id
     * 
     * Update shift
     * 
     * Request Body (JSON):
     * {
     *   "shift_name": "Siang",
     *   "start_time": "12:00:00",
     *   "end_time": "20:00:00"
     * }
     */
    public function update_put($id)
    {
        try {
            if (empty($id) || !is_numeric($id)) {
                $this->error_response('VALIDATION_ERROR', 'ID shift tidak valid', 400);
            }

            // Check if shift exists
            $shift = $this->Shift_model->get_by_id($id);
            if (!$shift) {
                $this->error_response('NOT_FOUND', 'Shift tidak ditemukan', 404);
            }

            // Get JSON input
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data) {
                $this->error_response('VALIDATION_ERROR', 'Invalid JSON format', 400);
            }

            // Validate shift name if provided
            if (isset($data['shift_name'])) {
                $valid_shifts = array('Pagi', 'Siang', 'Malam');
                if (!in_array($data['shift_name'], $valid_shifts)) {
                    $this->error_response('VALIDATION_ERROR', 'Shift name harus salah satu dari: Pagi, Siang, Malam', 400);
                }
            }

            // Update shift
            if ($this->Shift_model->update($id, $data)) {
                $updated_shift = $this->Shift_model->get_by_id($id);
                $this->success_response($updated_shift, 'Jadwal shift berhasil diupdate');
            } else {
                $this->error_response('SERVER_ERROR', 'Gagal mengupdate jadwal shift', 500);
            }

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * DELETE /api/v1/master/shifts/:id
     * 
     * Delete shift
     */
    public function delete_delete($id)
    {
        try {
            if (empty($id) || !is_numeric($id)) {
                $this->error_response('VALIDATION_ERROR', 'ID shift tidak valid', 400);
            }

            // Check if shift exists
            $shift = $this->Shift_model->get_by_id($id);
            if (!$shift) {
                $this->error_response('NOT_FOUND', 'Shift tidak ditemukan', 404);
            }

            // Delete shift
            if ($this->Shift_model->delete($id)) {
                $this->success_response(null, 'Jadwal shift berhasil dihapus');
            } else {
                $this->error_response('SERVER_ERROR', 'Gagal menghapus jadwal shift', 500);
            }

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }
}

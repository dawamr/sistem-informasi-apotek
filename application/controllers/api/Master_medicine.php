<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'core/API_Controller.php');

/**
 * Master_medicine API Controller
 * 
 * Endpoints untuk mengelola master data obat:
 * - GET /api/v1/master/medicines - Get all medicines
 * - GET /api/v1/master/medicines/:id - Get medicine by ID
 * - POST /api/v1/master/medicines - Create new medicine
 * - PUT /api/v1/master/medicines/:id - Update medicine
 * - DELETE /api/v1/master/medicines/:id - Delete medicine
 */
class Master_medicine extends API_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Medicine_model');
        $this->load->helper('api');
    }

    /**
     * GET /api/v1/master/medicines
     * 
     * Get all medicines dengan pagination dan filter
     * 
     * Query Parameters:
     * - page (optional): Page number, default 1
     * - limit (optional): Items per page, default 50
     * - category_id (optional): Filter by category ID
     * - active (optional): Filter by active status (1/0)
     * - search (optional): Search by name or code
     */
    public function index()
    {
        // Only allow GET requests
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->error_response('METHOD_NOT_ALLOWED', 'Only GET method is allowed', 405);
        }

        try {
            // Get parameters
            $page = $this->get_int_param('page', 1, 1);
            $limit = $this->get_int_param('limit', 50, 1, 100);
            $category_id = $this->get_int_param('category_id', 0);
            $active = $this->get_string_param('active');
            $search = $this->get_string_param('search');

            // Build query
            $this->db->select('m.*, c.name as category_name')
                     ->from('medicines m')
                     ->join('medicine_categories c', 'c.id = m.category_id', 'left');

            // Apply filters
            if ($category_id > 0) {
                $this->db->where('m.category_id', $category_id);
            }

            if ($active !== '') {
                $this->db->where('m.is_active', $active);
            }

            if (!empty($search)) {
                $this->db->group_start()
                         ->like('m.name', $search)
                         ->or_like('m.code', $search)
                         ->group_end();
            }

            // Count total
            $total = $this->db->count_all_results('', FALSE);

            // Apply pagination
            $offset = ($page - 1) * $limit;
            $this->db->limit($limit, $offset);
            $this->db->order_by('m.name', 'ASC');

            // Get data
            $medicines = $this->db->get()->result_array();

            // Format response
            $formatted_medicines = array();
            foreach ($medicines as $medicine) {
                $formatted_medicines[] = array(
                    'id' => (int)$medicine['id'],
                    'code' => $medicine['code'],
                    'name' => $medicine['name'],
                    'category_id' => $medicine['category_id'] ? (int)$medicine['category_id'] : null,
                    'category_name' => $medicine['category_name'],
                    'unit' => $medicine['unit'],
                    'price' => (float)$medicine['price'],
                    'current_stock' => (int)$medicine['current_stock'],
                    'is_active' => (bool)$medicine['is_active'],
                    'created_at' => $medicine['created_at'],
                    'updated_at' => $medicine['updated_at']
                );
            }

            $response = array(
                'medicines' => $formatted_medicines,
                'pagination' => array(
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                )
            );

            $this->success_response($response, 'Master obat berhasil diambil');

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/v1/master/medicines/:id
     * 
     * Get medicine by ID
     */
    public function detail($id)
    {
        // Only allow GET requests
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->error_response('METHOD_NOT_ALLOWED', 'Only GET method is allowed', 405);
        }

        try {
            if (empty($id) || !is_numeric($id)) {
                $this->error_response('VALIDATION_ERROR', 'ID obat tidak valid', 400);
            }

            $medicine = $this->Medicine_model->get_by_id($id);

            if (!$medicine) {
                $this->error_response('NOT_FOUND', 'Obat tidak ditemukan', 404);
            }

            // Format response
            $response = array(
                'id' => (int)$medicine['id'],
                'code' => $medicine['code'],
                'name' => $medicine['name'],
                'category_id' => $medicine['category_id'] ? (int)$medicine['category_id'] : null,
                'category_name' => $medicine['category_name'],
                'unit' => $medicine['unit'],
                'price' => (float)$medicine['price'],
                'current_stock' => (int)$medicine['current_stock'],
                'is_active' => (bool)$medicine['is_active'],
                'created_at' => $medicine['created_at'],
                'updated_at' => $medicine['updated_at']
            );

            $this->success_response($response, 'Detail obat berhasil diambil');

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/v1/master/medicines
     * 
     * Create new medicine
     * 
     * Request Body (JSON):
     * {
     *   "code": "OBT001",
     *   "name": "Paracetamol 500mg",
     *   "category_id": 1,
     *   "unit": "Strip",
     *   "price": 5000,
     *   "current_stock": 100
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
            $required = array('code', 'name', 'unit', 'price');
            if (!$this->validate_required_params($required, $data)) {
                $this->error_response('VALIDATION_ERROR', 'Field code, name, unit, dan price wajib diisi', 400);
            }

            // Check if code already exists
            $existing = $this->Medicine_model->get_by_code($data['code']);
            if ($existing) {
                $this->error_response('DUPLICATE_ERROR', 'Kode obat sudah digunakan', 400);
            }

            // Create medicine
            $create_data = array(
                'code' => $data['code'],
                'name' => $data['name'],
                'category_id' => isset($data['category_id']) ? $data['category_id'] : null,
                'unit' => $data['unit'],
                'price' => $data['price'],
                'current_stock' => isset($data['current_stock']) ? $data['current_stock'] : 0,
                'is_active' => isset($data['is_active']) ? $data['is_active'] : 1
            );

            if ($this->Medicine_model->create($create_data)) {
                $new_medicine = $this->Medicine_model->get_by_code($data['code']);
                $this->success_response($new_medicine, 'Obat berhasil ditambahkan', 201);
            } else {
                $this->error_response('SERVER_ERROR', 'Gagal menambahkan obat', 500);
            }

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * PUT /api/v1/master/medicines/:id
     * 
     * Update medicine
     * 
     * Request Body (JSON):
     * {
     *   "name": "Paracetamol 500mg Updated",
     *   "price": 5500,
     *   "is_active": 1
     * }
     */
    public function update_put($id)
    {
        try {
            if (empty($id) || !is_numeric($id)) {
                $this->error_response('VALIDATION_ERROR', 'ID obat tidak valid', 400);
            }

            // Check if medicine exists
            $medicine = $this->Medicine_model->get_by_id($id);
            if (!$medicine) {
                $this->error_response('NOT_FOUND', 'Obat tidak ditemukan', 404);
            }

            // Get JSON input
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data) {
                $this->error_response('VALIDATION_ERROR', 'Invalid JSON format', 400);
            }

            // Update medicine
            if ($this->Medicine_model->update($id, $data)) {
                $updated_medicine = $this->Medicine_model->get_by_id($id);
                $this->success_response($updated_medicine, 'Obat berhasil diupdate');
            } else {
                $this->error_response('SERVER_ERROR', 'Gagal mengupdate obat', 500);
            }

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * DELETE /api/v1/master/medicines/:id
     * 
     * Soft delete medicine (set is_active to 0)
     */
    public function delete_delete($id)
    {
        try {
            if (empty($id) || !is_numeric($id)) {
                $this->error_response('VALIDATION_ERROR', 'ID obat tidak valid', 400);
            }

            // Check if medicine exists
            $medicine = $this->Medicine_model->get_by_id($id);
            if (!$medicine) {
                $this->error_response('NOT_FOUND', 'Obat tidak ditemukan', 404);
            }

            // Soft delete
            if ($this->Medicine_model->update($id, array('is_active' => 0))) {
                $this->success_response(null, 'Obat berhasil dihapus');
            } else {
                $this->error_response('SERVER_ERROR', 'Gagal menghapus obat', 500);
            }

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }
}

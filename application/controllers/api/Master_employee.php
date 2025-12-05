<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'core/API_Controller.php');

/**
 * Master_employee API Controller
 * 
 * Endpoints untuk mengelola master data karyawan/user:
 * - GET /api/v1/master/employees - Get all employees
 * - GET /api/v1/master/employees/:id - Get employee by ID
 * - POST /api/v1/master/employees - Create new employee
 * - PUT /api/v1/master/employees/:id - Update employee
 * - DELETE /api/v1/master/employees/:id - Delete employee
 */
class Master_employee extends API_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->helper('api');
    }

    /**
     * GET /api/v1/master/employees
     * 
     * Get all employees dengan pagination dan filter
     * 
     * Query Parameters:
     * - page (optional): Page number, default 1
     * - limit (optional): Items per page, default 50
     * - role (optional): Filter by role (admin, apoteker, kasir)
     * - active (optional): Filter by active status (1/0)
     * - search (optional): Search by name or username
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
            $role = $this->get_string_param('role');
            $active = $this->get_string_param('active');
            $search = $this->get_string_param('search');

            // Build query
            $this->db->select('id, name, username, role, active, created_at, updated_at')
                     ->from('users');

            // Apply filters
            if (!empty($role)) {
                $this->db->where('role', $role);
            }

            if ($active !== '') {
                $this->db->where('active', $active);
            }

            if (!empty($search)) {
                $this->db->group_start()
                         ->like('name', $search)
                         ->or_like('username', $search)
                         ->group_end();
            }

            // Count total
            $total = $this->db->count_all_results('', FALSE);

            // Apply pagination
            $offset = ($page - 1) * $limit;
            $this->db->limit($limit, $offset);
            $this->db->order_by('name', 'ASC');

            // Get data
            $employees = $this->db->get()->result_array();

            // Format response
            $formatted_employees = array();
            foreach ($employees as $employee) {
                $formatted_employees[] = array(
                    'id' => (int)$employee['id'],
                    'name' => $employee['name'],
                    'username' => $employee['username'],
                    'role' => $employee['role'],
                    'active' => (bool)$employee['active'],
                    'created_at' => $employee['created_at'],
                    'updated_at' => $employee['updated_at']
                );
            }

            $response = array(
                'employees' => $formatted_employees,
                'pagination' => array(
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                )
            );

            $this->success_response($response, 'Master karyawan berhasil diambil');

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/v1/master/employees/:id
     * 
     * Get employee by ID
     */
    public function detail($id)
    {
        // Only allow GET requests
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->error_response('METHOD_NOT_ALLOWED', 'Only GET method is allowed', 405);
        }

        try {
            if (empty($id) || !is_numeric($id)) {
                $this->error_response('VALIDATION_ERROR', 'ID karyawan tidak valid', 400);
            }

            $employee = $this->User_model->get_by_id($id);

            if (!$employee) {
                $this->error_response('NOT_FOUND', 'Karyawan tidak ditemukan', 404);
            }

            // Format response (exclude password)
            $response = array(
                'id' => (int)$employee['id'],
                'name' => $employee['name'],
                'username' => $employee['username'],
                'role' => $employee['role'],
                'active' => (bool)$employee['active'],
                'created_at' => $employee['created_at'],
                'updated_at' => $employee['updated_at']
            );

            $this->success_response($response, 'Detail karyawan berhasil diambil');

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/v1/master/employees
     * 
     * Create new employee
     * 
     * Request Body (JSON):
     * {
     *   "name": "John Doe",
     *   "username": "johndoe",
     *   "password": "password123",
     *   "role": "apoteker"
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
            $required = array('name', 'username', 'password', 'role');
            if (!$this->validate_required_params($required, $data)) {
                $this->error_response('VALIDATION_ERROR', 'Field name, username, password, dan role wajib diisi', 400);
            }

            // Validate role
            $valid_roles = array('admin', 'apoteker', 'kasir');
            if (!in_array($data['role'], $valid_roles)) {
                $this->error_response('VALIDATION_ERROR', 'Role harus salah satu dari: admin, apoteker, kasir', 400);
            }

            // Check if username already exists
            if ($this->User_model->username_exists($data['username'])) {
                $this->error_response('DUPLICATE_ERROR', 'Username sudah digunakan', 400);
            }

            // Create employee
            $create_data = array(
                'name' => $data['name'],
                'username' => $data['username'],
                'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
                'role' => $data['role'],
                'active' => isset($data['active']) ? $data['active'] : 1
            );

            if ($this->User_model->create($create_data)) {
                $new_employee = $this->User_model->get_by_username($data['username']);
                
                // Format response (exclude password)
                $response = array(
                    'id' => (int)$new_employee['id'],
                    'name' => $new_employee['name'],
                    'username' => $new_employee['username'],
                    'role' => $new_employee['role'],
                    'active' => (bool)$new_employee['active']
                );

                $this->success_response($response, 'Karyawan berhasil ditambahkan', 201);
            } else {
                $this->error_response('SERVER_ERROR', 'Gagal menambahkan karyawan', 500);
            }

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * PUT /api/v1/master/employees/:id
     * 
     * Update employee
     * 
     * Request Body (JSON):
     * {
     *   "name": "John Doe Updated",
     *   "password": "newpassword123",
     *   "role": "admin",
     *   "active": 1
     * }
     */
    public function update_put($id)
    {
        try {
            if (empty($id) || !is_numeric($id)) {
                $this->error_response('VALIDATION_ERROR', 'ID karyawan tidak valid', 400);
            }

            // Check if employee exists
            $employee = $this->User_model->get_by_id($id);
            if (!$employee) {
                $this->error_response('NOT_FOUND', 'Karyawan tidak ditemukan', 404);
            }

            // Get JSON input
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data) {
                $this->error_response('VALIDATION_ERROR', 'Invalid JSON format', 400);
            }

            // Prepare update data
            $update_data = array();

            if (isset($data['name'])) {
                $update_data['name'] = $data['name'];
            }

            if (isset($data['password'])) {
                $update_data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
            }

            if (isset($data['role'])) {
                $valid_roles = array('admin', 'apoteker', 'kasir');
                if (!in_array($data['role'], $valid_roles)) {
                    $this->error_response('VALIDATION_ERROR', 'Role harus salah satu dari: admin, apoteker, kasir', 400);
                }
                $update_data['role'] = $data['role'];
            }

            if (isset($data['active'])) {
                $update_data['active'] = $data['active'];
            }

            // Update employee
            if ($this->User_model->update($id, $update_data)) {
                $updated_employee = $this->User_model->get_by_id($id);
                
                // Format response (exclude password)
                $response = array(
                    'id' => (int)$updated_employee['id'],
                    'name' => $updated_employee['name'],
                    'username' => $updated_employee['username'],
                    'role' => $updated_employee['role'],
                    'active' => (bool)$updated_employee['active']
                );

                $this->success_response($response, 'Karyawan berhasil diupdate');
            } else {
                $this->error_response('SERVER_ERROR', 'Gagal mengupdate karyawan', 500);
            }

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * DELETE /api/v1/master/employees/:id
     * 
     * Soft delete employee (set active to 0)
     */
    public function delete_delete($id)
    {
        try {
            if (empty($id) || !is_numeric($id)) {
                $this->error_response('VALIDATION_ERROR', 'ID karyawan tidak valid', 400);
            }

            // Check if employee exists
            $employee = $this->User_model->get_by_id($id);
            if (!$employee) {
                $this->error_response('NOT_FOUND', 'Karyawan tidak ditemukan', 404);
            }

            // Soft delete
            if ($this->User_model->deactivate($id)) {
                $this->success_response(null, 'Karyawan berhasil dihapus');
            } else {
                $this->error_response('SERVER_ERROR', 'Gagal menghapus karyawan', 500);
            }

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }
}

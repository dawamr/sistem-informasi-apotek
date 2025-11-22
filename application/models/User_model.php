<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User_model
 * 
 * Model untuk mengelola data pengguna (petugas/admin)
 */
class User_model extends CI_Model {

    private $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get user by ID
     */
    public function get_by_id($id)
    {
        return $this->db->where('id', $id)
                        ->get($this->table)
                        ->row_array();
    }

    /**
     * Get user by username
     */
    public function get_by_username($username)
    {
        return $this->db->where('username', $username)
                        ->get($this->table)
                        ->row_array();
    }

    /**
     * Get all active users
     */
    public function get_all_active()
    {
        return $this->db->where('active', 1)
                        ->order_by('name', 'ASC')
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Get all users
     */
    public function get_all()
    {
        return $this->db->order_by('name', 'ASC')
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Get users by role
     */
    public function get_by_role($role)
    {
        return $this->db->where('role', $role)
                        ->where('active', 1)
                        ->order_by('name', 'ASC')
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Create new user
     */
    public function create($data)
    {
        $insert_data = array(
            'name' => $data['name'],
            'username' => $data['username'],
            'password_hash' => $data['password_hash'],
            'role' => isset($data['role']) ? $data['role'] : 'apoteker',
            'active' => isset($data['active']) ? $data['active'] : 1,
            'created_at' => date('Y-m-d H:i:s')
        );

        return $this->db->insert($this->table, $insert_data);
    }

    /**
     * Update user
     */
    public function update($id, $data)
    {
        $update_data = array();

        if (isset($data['name'])) {
            $update_data['name'] = $data['name'];
        }

        if (isset($data['password_hash'])) {
            $update_data['password_hash'] = $data['password_hash'];
        }

        if (isset($data['role'])) {
            $update_data['role'] = $data['role'];
        }

        if (isset($data['active'])) {
            $update_data['active'] = $data['active'];
        }

        $update_data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->where('id', $id)
                        ->update($this->table, $update_data);
    }

    /**
     * Deactivate user
     */
    public function deactivate($id)
    {
        return $this->update($id, array('active' => 0));
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)
                        ->delete($this->table);
    }

    /**
     * Check if username exists
     */
    public function username_exists($username, $exclude_id = NULL)
    {
        $this->db->where('username', $username);
        
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        $result = $this->db->get($this->table)->row_array();
        return !empty($result);
    }

    /**
     * Count total users
     */
    public function count_all()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * Count active users
     */
    public function count_active()
    {
        return $this->db->where('active', 1)
                        ->count_all_results($this->table);
    }
}

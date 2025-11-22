<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api_key_model
 * 
 * Model untuk mengelola API keys
 */
class Api_key_model extends CI_Model {

    private $table = 'api_keys';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get API key by key string
     * 
     * @param string $api_key API key string
     * @return array|FALSE
     */
    public function get_by_key($api_key)
    {
        return $this->db->where('api_key', $api_key)
                        ->where('active', 1)
                        ->get($this->table)
                        ->row_array();
    }

    /**
     * Get API key by ID
     * 
     * @param int $id API key ID
     * @return array|FALSE
     */
    public function get_by_id($id)
    {
        return $this->db->where('id', $id)
                        ->get($this->table)
                        ->row_array();
    }

    /**
     * Get all active API keys
     * 
     * @return array
     */
    public function get_all_active()
    {
        return $this->db->where('active', 1)
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Create new API key
     * 
     * @param array $data Data API key
     * @return int|FALSE Insert ID atau FALSE jika gagal
     */
    public function create($data)
    {
        $insert_data = array(
            'name' => $data['name'],
            'api_key' => $data['api_key'],
            'active' => isset($data['active']) ? $data['active'] : 1,
            'created_at' => date('Y-m-d H:i:s')
        );

        if ($this->db->insert($this->table, $insert_data)) {
            return $this->db->insert_id();
        }

        return FALSE;
    }

    /**
     * Update API key
     * 
     * @param int $id API key ID
     * @param array $data Data yang akan di-update
     * @return bool
     */
    public function update($id, $data)
    {
        $update_data = array();

        if (isset($data['name'])) {
            $update_data['name'] = $data['name'];
        }

        if (isset($data['active'])) {
            $update_data['active'] = $data['active'];
        }

        $update_data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->where('id', $id)
                        ->update($this->table, $update_data);
    }

    /**
     * Deactivate API key
     * 
     * @param int $id API key ID
     * @return bool
     */
    public function deactivate($id)
    {
        return $this->update($id, array('active' => 0));
    }

    /**
     * Delete API key
     * 
     * @param int $id API key ID
     * @return bool
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)
                        ->delete($this->table);
    }

    /**
     * Generate random API key
     * 
     * @return string
     */
    public static function generate_key()
    {
        $prefix = 'sk_';
        $random = bin2hex(random_bytes(32));
        return $prefix . $random;
    }

    /**
     * Check if API key exists
     * 
     * @param string $api_key API key string
     * @return bool
     */
    public function key_exists($api_key)
    {
        $result = $this->db->where('api_key', $api_key)
                           ->get($this->table)
                           ->row_array();
        return !empty($result);
    }
}

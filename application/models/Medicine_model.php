<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Medicine_model
 * 
 * Model untuk mengelola data obat
 */
class Medicine_model extends CI_Model {

    private $table = 'medicines';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get medicine by ID
     */
    public function get_by_id($id)
    {
        return $this->db->select('m.*, c.name as category_name')
                        ->from($this->table . ' m')
                        ->join('medicine_categories c', 'c.id = m.category_id', 'left')
                        ->where('m.id', $id)
                        ->get()
                        ->row_array();
    }

    /**
     * Get medicine by code
     */
    public function get_by_code($code)
    {
        return $this->db->select('m.*, c.name as category_name')
                        ->from($this->table . ' m')
                        ->join('medicine_categories c', 'c.id = m.category_id', 'left')
                        ->where('m.code', $code)
                        ->get()
                        ->row_array();
    }

    /**
     * Get all active medicines
     */
    public function get_all_active()
    {
        return $this->db->select('m.*, c.name as category_name')
                        ->from($this->table . ' m')
                        ->join('medicine_categories c', 'c.id = m.category_id', 'left')
                        ->where('m.is_active', 1)
                        ->order_by('m.name', 'ASC')
                        ->get()
                        ->result_array();
    }

    /**
     * Get medicines by category
     */
    public function get_by_category($category_id)
    {
        return $this->db->select('m.*, c.name as category_name')
                        ->from($this->table . ' m')
                        ->join('medicine_categories c', 'c.id = m.category_id', 'left')
                        ->where('m.category_id', $category_id)
                        ->where('m.is_active', 1)
                        ->order_by('m.name', 'ASC')
                        ->get()
                        ->result_array();
    }

    /**
     * Search medicines by name or code
     */
    public function search($query)
    {
        return $this->db->select('m.*, c.name as category_name')
                        ->from($this->table . ' m')
                        ->join('medicine_categories c', 'c.id = m.category_id', 'left')
                        ->where('m.is_active', 1)
                        ->group_start()
                        ->like('m.name', $query)
                        ->or_like('m.code', $query)
                        ->group_end()
                        ->order_by('m.name', 'ASC')
                        ->get()
                        ->result_array();
    }

    /**
     * Get medicines with low stock
     */
    public function get_low_stock($threshold = 50)
    {
        return $this->db->select('m.*, c.name as category_name')
                        ->from($this->table . ' m')
                        ->join('medicine_categories c', 'c.id = m.category_id', 'left')
                        ->where('m.is_active', 1)
                        ->where('m.current_stock <', $threshold)
                        ->order_by('m.current_stock', 'ASC')
                        ->get()
                        ->result_array();
    }

    /**
     * Create new medicine
     */
    public function create($data)
    {
        $insert_data = array(
            'code' => $data['code'],
            'name' => $data['name'],
            'category_id' => isset($data['category_id']) ? $data['category_id'] : NULL,
            'unit' => $data['unit'],
            'price' => $data['price'],
            'current_stock' => isset($data['current_stock']) ? $data['current_stock'] : 0,
            'is_active' => isset($data['is_active']) ? $data['is_active'] : 1,
            'created_at' => date('Y-m-d H:i:s')
        );

        return $this->db->insert($this->table, $insert_data);
    }

    /**
     * Update medicine
     */
    public function update($id, $data)
    {
        $update_data = array();

        if (isset($data['name'])) {
            $update_data['name'] = $data['name'];
        }

        if (isset($data['category_id'])) {
            $update_data['category_id'] = $data['category_id'];
        }

        if (isset($data['price'])) {
            $update_data['price'] = $data['price'];
        }

        if (isset($data['is_active'])) {
            $update_data['is_active'] = $data['is_active'];
        }

        $update_data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->where('id', $id)
                        ->update($this->table, $update_data);
    }

    /**
     * Update stock
     */
    public function update_stock($id, $qty)
    {
        $this->db->set('current_stock', 'current_stock + ' . (int)$qty, FALSE);
        return $this->db->where('id', $id)
                        ->update($this->table);
    }

    /**
     * Count all medicines
     */
    public function count_all()
    {
        return $this->db->where('is_active', 1)
                        ->count_all_results($this->table);
    }
}

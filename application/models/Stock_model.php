<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Stock_model
 * 
 * Model untuk mengelola mutasi stok obat
 */
class Stock_model extends CI_Model {

    private $table = 'stock_logs';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get stock logs by medicine ID
     */
    public function get_by_medicine_id($medicine_id, $limit = 100)
    {
        return $this->db->where('medicine_id', $medicine_id)
                        ->order_by('log_date', 'DESC')
                        ->limit($limit)
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Get stock logs by date range
     */
    public function get_between_dates($start_date, $end_date)
    {
        return $this->db->where('DATE(log_date) >=', $start_date)
                        ->where('DATE(log_date) <=', $end_date)
                        ->order_by('log_date', 'DESC')
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Get stock logs by type
     */
    public function get_by_type($type, $limit = 100)
    {
        return $this->db->where('type', $type)
                        ->order_by('log_date', 'DESC')
                        ->limit($limit)
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Get current stock for medicine
     */
    public function get_current_stock($medicine_id)
    {
        $this->db->select('SUM(qty) as total_qty');
        $this->db->where('medicine_id', $medicine_id);
        $result = $this->db->get($this->table)->row_array();
        
        return (int)$result['total_qty'];
    }

    /**
     * Create stock log
     */
    public function create($data)
    {
        $insert_data = array(
            'medicine_id' => $data['medicine_id'],
            'log_date' => isset($data['log_date']) ? $data['log_date'] : date('Y-m-d H:i:s'),
            'type' => $data['type'],
            'ref_type' => isset($data['ref_type']) ? $data['ref_type'] : NULL,
            'ref_id' => isset($data['ref_id']) ? $data['ref_id'] : NULL,
            'qty' => $data['qty'],
            'notes' => isset($data['notes']) ? $data['notes'] : NULL
        );

        return $this->db->insert($this->table, $insert_data);
    }

    /**
     * Create multiple stock logs
     */
    public function create_batch($logs)
    {
        return $this->db->insert_batch($this->table, $logs);
    }

    /**
     * Get stock mutation summary by date
     */
    public function get_summary_by_date($date)
    {
        return $this->db->select('medicine_id, type, SUM(qty) as total_qty')
                        ->where('DATE(log_date)', $date)
                        ->group_by('medicine_id, type')
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Delete stock log
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)
                        ->delete($this->table);
    }

    /**
     * Count stock logs
     */
    public function count_all()
    {
        return $this->db->count_all($this->table);
    }
}

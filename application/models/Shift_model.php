<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Shift_model
 * 
 * Model untuk mengelola jadwal shift
 */
class Shift_model extends CI_Model {

    private $table = 'shifts';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get shift by ID
     */
    public function get_by_id($id)
    {
        return $this->db->where('id', $id)
                        ->get($this->table)
                        ->row_array();
    }

    /**
     * Get shifts by date
     */
    public function get_by_date($date)
    {
        return $this->db->where('date', $date)
                        ->order_by('start_time', 'ASC')
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Get shifts between dates
     */
    public function get_between_dates($start_date, $end_date)
    {
        return $this->db->where('date >=', $start_date)
                        ->where('date <=', $end_date)
                        ->order_by('date', 'ASC')
                        ->order_by('start_time', 'ASC')
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Get all shifts for a specific shift name
     */
    public function get_by_shift_name($shift_name)
    {
        return $this->db->where('shift_name', $shift_name)
                        ->order_by('date', 'DESC')
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Create shift
     */
    public function create($data)
    {
        $insert_data = array(
            'date' => $data['date'],
            'shift_name' => $data['shift_name'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'created_at' => date('Y-m-d H:i:s')
        );

        if ($this->db->insert($this->table, $insert_data)) {
            return $this->db->insert_id();
        }

        return FALSE;
    }

    /**
     * Update shift
     */
    public function update($id, $data)
    {
        $update_data = array();

        if (isset($data['shift_name'])) {
            $update_data['shift_name'] = $data['shift_name'];
        }

        if (isset($data['start_time'])) {
            $update_data['start_time'] = $data['start_time'];
        }

        if (isset($data['end_time'])) {
            $update_data['end_time'] = $data['end_time'];
        }

        $update_data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->where('id', $id)
                        ->update($this->table, $update_data);
    }

    /**
     * Delete shift
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)
                        ->delete($this->table);
    }

    /**
     * Count shifts by date
     */
    public function count_by_date($date)
    {
        return $this->db->where('date', $date)
                        ->count_all_results($this->table);
    }
}

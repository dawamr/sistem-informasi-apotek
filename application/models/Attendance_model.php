<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Attendance_model
 * 
 * Model untuk mengelola absensi petugas
 */
class Attendance_model extends CI_Model {

    private $table = 'attendances';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get attendance by ID
     */
    public function get_by_id($id)
    {
        return $this->db->where('id', $id)
                        ->get($this->table)
                        ->row_array();
    }

    /**
     * Get attendance by user and date
     */
    public function get_by_user_date($user_id, $date)
    {
        return $this->db->select('a.*, u.name as user_name, s.shift_name, s.start_time, s.end_time')
                        ->from($this->table . ' a')
                        ->join('users u', 'u.id = a.user_id')
                        ->join('shifts s', 's.id = a.shift_id')
                        ->where('a.user_id', $user_id)
                        ->where('a.date', $date)
                        ->get()
                        ->result_array();
    }

    /**
     * Get attendance by date
     */
    public function get_by_date($date)
    {
        return $this->db->select('a.*, u.name as user_name, s.shift_name, s.start_time, s.end_time')
                        ->from($this->table . ' a')
                        ->join('users u', 'u.id = a.user_id')
                        ->join('shifts s', 's.id = a.shift_id')
                        ->where('a.date', $date)
                        ->order_by('s.start_time', 'ASC')
                        ->order_by('u.name', 'ASC')
                        ->get()
                        ->result_array();
    }

    /**
     * Get attendance by shift
     */
    public function get_by_shift($shift_id, $date)
    {
        return $this->db->select('a.*, u.name as user_name')
                        ->from($this->table . ' a')
                        ->join('users u', 'u.id = a.user_id')
                        ->where('a.shift_id', $shift_id)
                        ->where('a.date', $date)
                        ->order_by('u.name', 'ASC')
                        ->get()
                        ->result_array();
    }

    /**
     * Get attendance summary by date
     */
    public function get_summary_by_date($date)
    {
        return $this->db->select("
            COUNT(CASE WHEN status = 'hadir' THEN 1 END) as present,
            COUNT(CASE WHEN status = 'izin' THEN 1 END) as permission,
            COUNT(CASE WHEN status = 'sakit' THEN 1 END) as sick,
            COUNT(CASE WHEN status = 'alpha' THEN 1 END) as absent,
            COUNT(*) as total_scheduled
        ")
                        ->where('date', $date)
                        ->get($this->table)
                        ->row_array();
    }

    /**
     * Create attendance
     */
    public function create($data)
    {
        $insert_data = array(
            'user_id' => $data['user_id'],
            'shift_id' => $data['shift_id'],
            'date' => $data['date'],
            'status' => isset($data['status']) ? $data['status'] : 'alpha',
            'checkin_time' => isset($data['checkin_time']) ? $data['checkin_time'] : NULL,
            'checkout_time' => isset($data['checkout_time']) ? $data['checkout_time'] : NULL,
            'notes' => isset($data['notes']) ? $data['notes'] : NULL
        );

        return $this->db->insert($this->table, $insert_data);
    }

    /**
     * Update attendance
     */
    public function update($id, $data)
    {
        $update_data = array();

        if (isset($data['status'])) {
            $update_data['status'] = $data['status'];
        }

        if (isset($data['checkin_time'])) {
            $update_data['checkin_time'] = $data['checkin_time'];
        }

        if (isset($data['checkout_time'])) {
            $update_data['checkout_time'] = $data['checkout_time'];
        }

        if (isset($data['notes'])) {
            $update_data['notes'] = $data['notes'];
        }

        return $this->db->where('id', $id)
                        ->update($this->table, $update_data);
    }

    /**
     * Delete attendance
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)
                        ->delete($this->table);
    }

    /**
     * Check if attendance exists
     */
    public function exists($user_id, $shift_id, $date)
    {
        $result = $this->db->where('user_id', $user_id)
                           ->where('shift_id', $shift_id)
                           ->where('date', $date)
                           ->get($this->table)
                           ->row_array();
        return !empty($result);
    }

    /**
     * Count attendance by status
     */
    public function count_by_status($date, $status)
    {
        return $this->db->where('date', $date)
                        ->where('status', $status)
                        ->count_all_results($this->table);
    }
}

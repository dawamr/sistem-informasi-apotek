<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) { redirect('auth'); }
        $this->load->model('Attendance_model');
        $this->load->database();
    }

    // Daily attendance list
    public function index()
    {
        $date = $this->input->get('date') ?: date('Y-m-d');
        $rows = $this->Attendance_model->get_by_date($date);
        $data = array(
            'page_title' => 'Absensi Harian',
            'current_page' => 'attendance',
            'date' => $date,
            'rows' => $rows,
            'page_scripts' => array('pages/attendance-index.js'),
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('attendance/index', $data);
        $this->load->view('templates/footer', $data);
    }

    // POST: check-in by attendance id
    public function checkin()
    {
        if ($this->input->method() !== 'post') { show_404(); }
        $id = (int)$this->input->post('id');
        $row = $this->Attendance_model->get_by_id($id);
        if (!$row) { $this->_flash('danger','Data absensi tidak ditemukan'); redirect('attendance'); return; }
        if (!$this->_can_modify($row)) { $this->_flash('danger','Tidak diizinkan'); redirect('attendance'); return; }
        if (!empty($row['checkin_time'])) { $this->_flash('info','Sudah check-in'); redirect('attendance?date='.$row['date']); return; }
        $ok = $this->Attendance_model->update($id, array('checkin_time' => date('Y-m-d H:i:s'), 'status' => 'hadir'));
        $this->_flash($ok?'success':'danger', $ok?'Berhasil check-in':'Gagal check-in');
        redirect('attendance?date='.$row['date']);
    }

    // POST: check-out by attendance id
    public function checkout()
    {
        if ($this->input->method() !== 'post') { show_404(); }
        $id = (int)$this->input->post('id');
        $row = $this->Attendance_model->get_by_id($id);
        if (!$row) { $this->_flash('danger','Data absensi tidak ditemukan'); redirect('attendance'); return; }
        if (!$this->_can_modify($row)) { $this->_flash('danger','Tidak diizinkan'); redirect('attendance'); return; }
        if (empty($row['checkin_time'])) { $this->_flash('info','Belum check-in'); redirect('attendance?date='.$row['date']); return; }
        if (!empty($row['checkout_time'])) { $this->_flash('info','Sudah check-out'); redirect('attendance?date='.$row['date']); return; }
        $ok = $this->Attendance_model->update($id, array('checkout_time' => date('Y-m-d H:i:s')));
        $this->_flash($ok?'success':'danger', $ok?'Berhasil check-out':'Gagal check-out');
        redirect('attendance?date='.$row['date']);
    }

    // Report between dates
    public function report()
    {
        $start = $this->input->get('start') ?: date('Y-m-01');
        $end = $this->input->get('end') ?: date('Y-m-d');
        $status = $this->input->get('status');
        $this->db->select('a.*, u.name as user_name, s.shift_name, s.start_time, s.end_time');
        $this->db->from('attendances a');
        $this->db->join('users u', 'u.id = a.user_id');
        $this->db->join('shifts s', 's.id = a.shift_id');
        $this->db->where('a.date >=', $start);
        $this->db->where('a.date <=', $end);
        if (!empty($status)) { $this->db->where('a.status', $status); }
        $rows = $this->db->order_by('a.date','DESC')->order_by('u.name','ASC')->get()->result_array();
        $data = array(
            'page_title' => 'Laporan Absensi',
            'current_page' => 'attendance',
            'start' => $start,
            'end' => $end,
            'status' => $status,
            'rows' => $rows,
            'page_scripts' => array('pages/attendance-report.js'),
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('attendance/report', $data);
        $this->load->view('templates/footer', $data);
    }

    private function _can_modify($attendance)
    {
        $uid = (int)$this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        return $role === 'admin' || (int)$attendance['user_id'] === $uid;
    }

    private function _flash($type, $msg)
    {
        $this->session->set_flashdata($type, $msg);
    }
}

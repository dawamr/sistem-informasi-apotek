<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) { redirect('auth'); }
        $this->load->model('Stock_model');
        $this->load->model('Medicine_model');
        $this->load->database();
    }

    public function index()
    {
        $start = $this->input->get('start') ?: date('Y-m-01');
        $end = $this->input->get('end') ?: date('Y-m-d');
        $logs = $this->Stock_model->get_between_dates($start, $end);
        $low_stock = $this->Medicine_model->get_low_stock();

        $data = array(
            'page_title' => 'Manajemen Stok',
            'current_page' => 'stock',
            'start' => $start,
            'end' => $end,
            'logs' => $logs,
            'low_stock' => $low_stock,
            'page_scripts' => array('pages/stock-index.js'),
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('stock/index', $data);
        $this->load->view('templates/footer', $data);
    }

    public function in()
    {
        if ($this->input->method() === 'post') { return $this->save_in(); }
        $medicines = $this->Medicine_model->get_all_active();
        $data = array(
            'page_title' => 'Stok Masuk',
            'current_page' => 'stock',
            'medicines' => $medicines,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('stock/in', $data);
        $this->load->view('templates/footer', $data);
    }

    public function out()
    {
        if ($this->input->method() === 'post') { return $this->save_out(); }
        $medicines = $this->Medicine_model->get_all_active();
        $data = array(
            'page_title' => 'Stok Keluar',
            'current_page' => 'stock',
            'medicines' => $medicines,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('stock/out', $data);
        $this->load->view('templates/footer', $data);
    }

    public function opname()
    {
        if ($this->input->method() === 'post') { return $this->save_opname(); }
        $medicines = $this->Medicine_model->get_all_active();
        $data = array(
            'page_title' => 'Stok Opname',
            'current_page' => 'stock',
            'medicines' => $medicines,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('stock/opname', $data);
        $this->load->view('templates/footer', $data);
    }

    public function save_in()
    {
        if ($this->input->method() !== 'post') { show_404(); }
        $medicine_id = (int)$this->input->post('medicine_id');
        $qty = (int)$this->input->post('qty');
        $notes = $this->input->post('notes', TRUE);
        if ($qty < 1) { $this->_flash('danger', 'Qty tidak valid'); redirect('stock/in'); return; }
        $this->Stock_model->create(array(
            'medicine_id' => $medicine_id,
            'type' => 'in',
            'ref_type' => 'manual',
            'ref_id' => NULL,
            'qty' => $qty,
            'notes' => $notes,
        ));
        // update current_stock
        $this->db->set('current_stock', 'current_stock + '.(int)$qty, FALSE)
                 ->where('id', $medicine_id)->update('medicines');
        $this->_flash('success', 'Stok masuk tersimpan');
        redirect('stock');
    }

    public function save_out()
    {
        if ($this->input->method() !== 'post') { show_404(); }
        $medicine_id = (int)$this->input->post('medicine_id');
        $qty = (int)$this->input->post('qty');
        $notes = $this->input->post('notes', TRUE);
        if ($qty < 1) { $this->_flash('danger', 'Qty tidak valid'); redirect('stock/out'); return; }

        $row = $this->Medicine_model->get_by_id($medicine_id);
        if (!$row) { $this->_flash('danger', 'Obat tidak ditemukan'); redirect('stock/out'); return; }

        $current = (int)$row['current_stock'];
        if ($qty > $current) {
            $this->_flash('danger', 'Stok tidak mencukupi. Tersedia: '.$current);
            redirect('stock/out');
            return;
        }

        // transactional update
        $this->db->trans_start();
        $this->Stock_model->create(array(
            'medicine_id' => $medicine_id,
            'type' => 'out',
            'ref_type' => 'manual',
            'ref_id' => NULL,
            'qty' => -$qty,
            'notes' => $notes,
        ));
        $this->db->set('current_stock', 'current_stock - '.(int)$qty, FALSE)
                 ->where('id', $medicine_id)->update('medicines');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->_flash('danger', 'Gagal menyimpan stok keluar');
            redirect('stock/out');
            return;
        }

        $this->_flash('success', 'Stok keluar tersimpan');
        redirect('stock');
    }

    public function save_opname()
    {
        if ($this->input->method() !== 'post') { show_404(); }
        $medicine_id = (int)$this->input->post('medicine_id');
        $real = (int)$this->input->post('real_stock');
        $notes = $this->input->post('notes', TRUE);
        $row = $this->Medicine_model->get_by_id($medicine_id);
        if (!$row) { $this->_flash('danger', 'Obat tidak ditemukan'); redirect('stock/opname'); return; }
        if ($real < 0) { $this->_flash('danger', 'Stok real tidak boleh negatif'); redirect('stock/opname'); return; }
        $diff = $real - (int)$row['current_stock'];
        if ($diff === 0) { $this->_flash('info', 'Tidak ada perubahan stok'); redirect('stock'); return; }

        // transactional update
        $this->db->trans_start();
        $this->Stock_model->create(array(
            'medicine_id' => $medicine_id,
            'type' => 'opname',
            'ref_type' => 'manual',
            'ref_id' => NULL,
            'qty' => $diff,
            'notes' => $notes ?: 'Penyesuaian stok (opname)'
        ));
        $this->db->set('current_stock', (int)$real, FALSE)
                 ->where('id', $medicine_id)->update('medicines');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->_flash('danger', 'Gagal menyimpan opname');
            redirect('stock/opname');
            return;
        }

        $this->_flash('success', 'Opname tersimpan');
        redirect('stock');
    }

    private function _flash($type, $msg)
    {
        $this->session->set_flashdata($type, $msg);
    }
}

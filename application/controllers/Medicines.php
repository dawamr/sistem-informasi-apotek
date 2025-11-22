<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Medicines extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) { redirect('auth'); }
        $this->load->model('Medicine_model');
        $this->load->database();
    }

    public function index()
    {
        $categories = $this->db->order_by('name','ASC')->get('medicine_categories')->result_array();
        $category_id = $this->input->get('category');
        if ($category_id) {
            $medicines = $this->Medicine_model->get_by_category($category_id);
        } else {
            $medicines = $this->Medicine_model->get_all_active();
        }
        $data = array(
            'page_title' => 'Medicines',
            'current_page' => 'medicines',
            'categories' => $categories,
            'selected_category' => $category_id,
            'medicines' => $medicines,
            'page_scripts' => array('pages/medicines-index.js'),
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('medicines/index', $data);
        $this->load->view('templates/footer', $data);
    }

    public function create()
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger', 'Tidak diizinkan'); redirect('medicines'); return; }
        if ($this->input->method() === 'post') {
            $code = $this->input->post('code', TRUE);
            $name = $this->input->post('name', TRUE);
            $category_id = $this->input->post('category_id');
            $unit = $this->input->post('unit', TRUE);
            $price = (int)$this->input->post('price');
            $stock = (int)$this->input->post('current_stock');
            $ok = $this->Medicine_model->create(array(
                'code' => $code,
                'name' => $name,
                'category_id' => $category_id,
                'unit' => $unit,
                'price' => $price,
                'current_stock' => $stock,
                'is_active' => 1
            ));
            if ($ok) {
                $this->session->set_flashdata('success', 'Obat berhasil ditambahkan');
            } else {
                $this->session->set_flashdata('danger', 'Gagal menambahkan obat');
            }
            redirect('medicines');
        }
        $categories = $this->db->order_by('name','ASC')->get('medicine_categories')->result_array();
        $data = array(
            'page_title' => 'Add Medicine',
            'current_page' => 'medicines',
            'categories' => $categories,
            'medicine' => null,
            'page_scripts' => array('pages/medicines-form.js'),
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('medicines/form', $data);
        $this->load->view('templates/footer', $data);
    }

    public function edit($id)
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger', 'Tidak diizinkan'); redirect('medicines'); return; }
        $medicine = $this->Medicine_model->get_by_id($id);
        if (!$medicine) {
            $this->session->set_flashdata('danger', 'Obat tidak ditemukan');
            redirect('medicines');
        }
        if ($this->input->method() === 'post') {
            $update = array(
                'name' => $this->input->post('name', TRUE),
                'category_id' => $this->input->post('category_id'),
                'price' => (int)$this->input->post('price'),
                'is_active' => (int)$this->input->post('is_active')
            );
            $ok = $this->Medicine_model->update($id, $update);
            if ($ok) {
                $this->session->set_flashdata('success', 'Perubahan obat tersimpan');
            } else {
                $this->session->set_flashdata('danger', 'Gagal menyimpan perubahan obat');
            }
            redirect('medicines');
        }
        $categories = $this->db->order_by('name','ASC')->get('medicine_categories')->result_array();
        $data = array(
            'page_title' => 'Edit Medicine',
            'current_page' => 'medicines',
            'categories' => $categories,
            'medicine' => $medicine,
            'page_scripts' => array('pages/medicines-form.js'),
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('medicines/form', $data);
        $this->load->view('templates/footer', $data);
    }

    public function delete($id)
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger', 'Tidak diizinkan'); redirect('medicines'); return; }
        $this->db->where('id', $id)->delete('medicines');
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('success', 'Obat berhasil dihapus');
        } else {
            $this->session->set_flashdata('danger', 'Gagal menghapus obat');
        }
        redirect('medicines');
    }
}

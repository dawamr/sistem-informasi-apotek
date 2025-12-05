<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Chatbot_setting Controller
 * 
 * Mengelola pengaturan akses chatbot per nomor telepon
 */
class Chatbot_setting extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Check login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        
        // Admin only
        if ($this->session->userdata('role') !== 'admin') {
            $this->session->set_flashdata('danger', 'Tidak diizinkan');
            redirect('dashboard');
            return;
        }
        
        $this->load->model('Chatbot_access_model');
    }

    /**
     * List all chatbot access entries
     */
    public function index()
    {
        $entries = $this->Chatbot_access_model->get_all();
        
        // Parse custom_access for display
        foreach ($entries as &$entry) {
            $entry['features'] = $this->Chatbot_access_model->parse_custom_access($entry['custom_access']);
        }
        
        $data = array(
            'page_title' => 'Chatbot Setting',
            'current_page' => 'chatbot_setting',
            'entries' => $entries,
            'available_features' => Chatbot_access_model::$available_features,
            'page_scripts' => array('pages/chatbot-setting.js'),
        );
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('chatbot_setting/index', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Create new chatbot access entry
     */
    public function create()
    {
        if ($this->input->method() === 'post') {
            $phone_number = trim($this->input->post('phone_number', TRUE));
            $access_type = $this->input->post('access_type', TRUE);
            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $is_allowed = (int)$this->input->post('is_allowed');
            $notes = $this->input->post('notes', TRUE);
            
            // Build custom_access JSON
            $features = $this->input->post('features');
            $custom_access = array();
            foreach (Chatbot_access_model::$available_features as $key => $label) {
                $custom_access[$key] = is_array($features) && in_array($key, $features);
            }
            
            // Validation
            if (empty($phone_number)) {
                $this->session->set_flashdata('danger', 'Nomor telepon wajib diisi');
                redirect('chatbot_setting/create');
                return;
            }
            
            if ($this->Chatbot_access_model->phone_exists($phone_number)) {
                $this->session->set_flashdata('danger', 'Nomor telepon sudah terdaftar');
                redirect('chatbot_setting/create');
                return;
            }
            
            $ok = $this->Chatbot_access_model->create(array(
                'phone_number' => $phone_number,
                'access_type' => $access_type,
                'start_date' => $access_type === 'parttime' ? $start_date : null,
                'end_date' => $access_type === 'parttime' ? $end_date : null,
                'is_allowed' => $is_allowed,
                'custom_access' => json_encode($custom_access),
                'notes' => $notes
            ));
            
            if ($ok) {
                $this->session->set_flashdata('success', 'Akses chatbot berhasil ditambahkan');
            } else {
                $this->session->set_flashdata('danger', 'Gagal menambahkan akses chatbot');
            }
            redirect('chatbot_setting');
            return;
        }
        
        $data = array(
            'page_title' => 'Tambah Akses Chatbot',
            'current_page' => 'chatbot_setting',
            'entry' => null,
            'available_features' => Chatbot_access_model::$available_features,
        );
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('chatbot_setting/form', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Edit chatbot access entry
     */
    public function edit($id)
    {
        $entry = $this->Chatbot_access_model->get_by_id($id);
        
        if (!$entry) {
            $this->session->set_flashdata('danger', 'Data tidak ditemukan');
            redirect('chatbot_setting');
            return;
        }
        
        if ($this->input->method() === 'post') {
            $phone_number = trim($this->input->post('phone_number', TRUE));
            $access_type = $this->input->post('access_type', TRUE);
            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $is_allowed = (int)$this->input->post('is_allowed');
            $notes = $this->input->post('notes', TRUE);
            
            // Build custom_access JSON
            $features = $this->input->post('features');
            $custom_access = array();
            foreach (Chatbot_access_model::$available_features as $key => $label) {
                $custom_access[$key] = is_array($features) && in_array($key, $features);
            }
            
            // Validation - phone_number cannot be changed for default (*)
            if ($entry['phone_number'] === '*') {
                $phone_number = '*';
            }
            
            if (empty($phone_number)) {
                $this->session->set_flashdata('danger', 'Nomor telepon wajib diisi');
                redirect('chatbot_setting/edit/' . $id);
                return;
            }
            
            if ($this->Chatbot_access_model->phone_exists($phone_number, $id)) {
                $this->session->set_flashdata('danger', 'Nomor telepon sudah terdaftar');
                redirect('chatbot_setting/edit/' . $id);
                return;
            }
            
            $ok = $this->Chatbot_access_model->update($id, array(
                'phone_number' => $phone_number,
                'access_type' => $access_type,
                'start_date' => $access_type === 'parttime' ? $start_date : null,
                'end_date' => $access_type === 'parttime' ? $end_date : null,
                'is_allowed' => $is_allowed,
                'custom_access' => json_encode($custom_access),
                'notes' => $notes
            ));
            
            if ($ok) {
                $this->session->set_flashdata('success', 'Akses chatbot berhasil diperbarui');
            } else {
                $this->session->set_flashdata('danger', 'Gagal memperbarui akses chatbot');
            }
            redirect('chatbot_setting');
            return;
        }
        
        // Parse custom_access for form
        $entry['features'] = $this->Chatbot_access_model->parse_custom_access($entry['custom_access']);
        
        $data = array(
            'page_title' => 'Edit Akses Chatbot',
            'current_page' => 'chatbot_setting',
            'entry' => $entry,
            'available_features' => Chatbot_access_model::$available_features,
        );
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('chatbot_setting/form', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Toggle allow/disallow status
     */
    public function toggle($id)
    {
        $entry = $this->Chatbot_access_model->get_by_id($id);
        
        if (!$entry) {
            $this->session->set_flashdata('danger', 'Data tidak ditemukan');
            redirect('chatbot_setting');
            return;
        }
        
        $ok = $this->Chatbot_access_model->toggle_allowed($id);
        
        if ($ok) {
            $status = $entry['is_allowed'] ? 'dinonaktifkan' : 'diaktifkan';
            $this->session->set_flashdata('success', 'Akses chatbot berhasil ' . $status);
        } else {
            $this->session->set_flashdata('danger', 'Gagal mengubah status akses');
        }
        redirect('chatbot_setting');
    }

    /**
     * Delete chatbot access entry
     */
    public function delete($id)
    {
        $entry = $this->Chatbot_access_model->get_by_id($id);
        
        if (!$entry) {
            $this->session->set_flashdata('danger', 'Data tidak ditemukan');
            redirect('chatbot_setting');
            return;
        }
        
        if ($entry['phone_number'] === '*') {
            $this->session->set_flashdata('warning', 'Default akses (*) tidak dapat dihapus');
            redirect('chatbot_setting');
            return;
        }
        
        $ok = $this->Chatbot_access_model->delete($id);
        
        if ($ok) {
            $this->session->set_flashdata('success', 'Akses chatbot berhasil dihapus');
        } else {
            $this->session->set_flashdata('danger', 'Gagal menghapus akses chatbot');
        }
        redirect('chatbot_setting');
    }

    /**
     * API endpoint to check access for a phone number
     * Can be called from n8n or external services
     */
    public function check_api()
    {
        // This should be protected with API key in production
        $phone_number = $this->input->get_post('phone_number');
        $feature = $this->input->get_post('feature');
        
        if (empty($phone_number)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Phone number is required'
                )));
            return;
        }
        
        $access = $this->Chatbot_access_model->check_access($phone_number);
        
        $response = array(
            'success' => true,
            'phone_number' => $phone_number,
            'is_allowed' => (bool)$access['is_allowed'],
            'access_type' => $access['access_type'],
            'features' => $this->Chatbot_access_model->parse_custom_access($access['custom_access'])
        );
        
        // If specific feature check requested
        if ($feature) {
            $response['can_access_feature'] = $this->Chatbot_access_model->can_access_feature($phone_number, $feature);
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}

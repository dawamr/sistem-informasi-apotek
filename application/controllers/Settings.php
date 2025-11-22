<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) { redirect('auth'); }
        $this->load->model('User_model');
        $this->load->model('Api_key_model');
        $this->load->model('Medicine_model');
    }

    public function index()
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger','Tidak diizinkan'); redirect('dashboard'); return; }
        $low_threshold = 50;
        $low_list = $this->Medicine_model->get_low_stock($low_threshold);
        $data = array(
            'page_title' => 'Settings',
            'current_page' => 'settings',
            'low_threshold' => $low_threshold,
            'low_stock' => $low_list,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('settings/index', $data);
        $this->load->view('templates/footer', $data);
    }

    public function api_keys()
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger','Tidak diizinkan'); redirect('settings'); return; }
        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'create') {
                $name = trim($this->input->post('name'));
                if ($name === '') {
                    $this->session->set_flashdata('danger', 'Nama API key wajib diisi');
                    redirect('settings/api-keys');
                }
                $key = Api_key_model::generate_key();
                $this->Api_key_model->create(array(
                    'name' => $name,
                    'api_key' => $key,
                    'active' => 1,
                ));
                $this->session->set_flashdata('success', 'API key berhasil dibuat');
                redirect('settings/api-keys');
            } elseif ($action === 'deactivate') {
                $id = (int)$this->input->post('id');
                $this->Api_key_model->deactivate($id);
                $this->session->set_flashdata('info', 'API key dinonaktifkan');
                redirect('settings/api-keys');
            } elseif ($action === 'delete') {
                $id = (int)$this->input->post('id');
                $this->Api_key_model->delete($id);
                $this->session->set_flashdata('info', 'API key dihapus');
                redirect('settings/api-keys');
            }
        }
        $keys = $this->Api_key_model->get_all_active();
        $data = array(
            'page_title' => 'Settings - API Keys',
            'current_page' => 'settings',
            'keys' => $keys,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('settings/api_keys', $data);
        $this->load->view('templates/footer', $data);
    }

    public function profile()
    {
        $user_id = (int)$this->session->userdata('user_id');
        $user = $user_id ? $this->User_model->get_by_id($user_id) : null;
        if ($this->input->method() === 'post') {
            $name = trim($this->input->post('name'));
            $username = trim($this->input->post('username'));
            if ($name === '' || $username === '') {
                $this->session->set_flashdata('danger', 'Nama dan username wajib diisi');
                redirect('settings/profile');
            }
            if ($this->User_model->username_exists($username, $user_id)) {
                $this->session->set_flashdata('danger', 'Username sudah digunakan');
                redirect('settings/profile');
            }
            $ok = $this->User_model->update($user_id, array('name' => $name));
            // Optional: also update username if allowed
            if ($user && $user['username'] !== $username) {
                $this->db->where('id', $user_id)->update('users', array('username' => $username));
            }
            if ($ok) {
                $this->session->set_flashdata('success', 'Profil diperbarui');
            } else {
                $this->session->set_flashdata('danger', 'Gagal memperbarui profil');
            }
            redirect('settings/profile');
        }
        $data = array(
            'page_title' => 'Settings - Profile',
            'current_page' => 'settings',
            'user' => $user,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('settings/profile', $data);
        $this->load->view('templates/footer', $data);
    }

    public function password()
    {
        $user_id = (int)$this->session->userdata('user_id');
        $user = $user_id ? $this->User_model->get_by_id($user_id) : null;
        if ($this->input->method() === 'post') {
            $current = (string)$this->input->post('current_password');
            $new = (string)$this->input->post('new_password');
            $confirm = (string)$this->input->post('confirm_password');
            if ($new === '' || $confirm === '') {
                $this->session->set_flashdata('danger', 'Password baru wajib diisi');
                redirect('settings/password');
            }
            if ($new !== $confirm) {
                $this->session->set_flashdata('danger', 'Konfirmasi password tidak cocok');
                redirect('settings/password');
            }
            $ok = true;
            if ($user && !empty($user['password_hash'])) {
                if (!password_verify($current, $user['password_hash'])) {
                    $this->session->set_flashdata('danger', 'Password saat ini salah');
                    redirect('settings/password');
                }
            }
            if ($ok) {
                $hash = password_hash($new, PASSWORD_BCRYPT);
                $this->User_model->update($user_id, array('password_hash' => $hash));
                $this->session->set_flashdata('success', 'Password berhasil diubah');
            }
            redirect('settings/password');
        }
        $data = array(
            'page_title' => 'Settings - Change Password',
            'current_page' => 'settings',
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('settings/password', $data);
        $this->load->view('templates/footer', $data);
    }
}

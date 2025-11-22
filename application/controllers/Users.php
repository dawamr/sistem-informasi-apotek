<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) { redirect('auth'); }
        $this->load->model('User_model');
        // Admin-only controller
        if ($this->session->userdata('role') !== 'admin') {
            $this->session->set_flashdata('danger', 'Tidak diizinkan');
            redirect('dashboard');
            return;
        }
    }

    public function index()
    {
        $users = $this->User_model->get_all();
        $data = array(
            'page_title' => 'Manajemen Pengguna',
            'current_page' => 'users',
            'users' => $users,
            'page_scripts' => array('pages/users-index.js'),
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('users/index', $data);
        $this->load->view('templates/footer', $data);
    }

    public function create()
    {
        if ($this->input->method() === 'post') {
            $name = $this->input->post('name', TRUE);
            $username = $this->input->post('username', TRUE);
            $role = $this->input->post('role', TRUE);
            $active = (int)$this->input->post('active');
            $password = $this->input->post('password');

            $ok = $this->User_model->create(array(
                'name' => $name,
                'username' => $username,
                'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                'role' => $role,
                'active' => $active
            ));
            if ($ok) {
                $this->session->set_flashdata('success', 'Pengguna berhasil ditambahkan');
            } else {
                $this->session->set_flashdata('danger', 'Gagal menambahkan pengguna');
            }
            redirect('users');
        }
        $data = array(
            'page_title' => 'Tambah Pengguna',
            'current_page' => 'users',
            'user' => null,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('users/form', $data);
        $this->load->view('templates/footer', $data);
    }

    public function edit($id)
    {
        $user = $this->User_model->get_by_id($id);
        if (!$user) {
            $this->session->set_flashdata('danger', 'Pengguna tidak ditemukan');
            redirect('users');
        }

        if ($this->input->method() === 'post') {
            $update = array(
                'name' => $this->input->post('name', TRUE),
                'role' => $this->input->post('role', TRUE),
                'active' => (int)$this->input->post('active')
            );
            $new_password = $this->input->post('password');
            if (!empty($new_password)) {
                $update['password_hash'] = password_hash($new_password, PASSWORD_BCRYPT);
            }
            $ok = $this->User_model->update($id, $update);
            if ($ok) {
                $this->session->set_flashdata('success', 'Perubahan pengguna tersimpan');
            } else {
                $this->session->set_flashdata('danger', 'Gagal menyimpan perubahan pengguna');
            }
            redirect('users');
        }

        $data = array(
            'page_title' => 'Edit Pengguna',
            'current_page' => 'users',
            'user' => $user,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('users/form', $data);
        $this->load->view('templates/footer', $data);
    }

    public function deactivate($id)
    {
        $ok = $this->User_model->update($id, array('active' => 0));
        if ($ok) {
            $this->session->set_flashdata('success', 'Pengguna dinonaktifkan');
        } else {
            $this->session->set_flashdata('danger', 'Gagal menonaktifkan pengguna');
        }
        redirect('users');
    }

    public function activate($id)
    {
        $ok = $this->User_model->update($id, array('active' => 1));
        if ($ok) {
            $this->session->set_flashdata('success', 'Pengguna diaktifkan');
        } else {
            $this->session->set_flashdata('danger', 'Gagal mengaktifkan pengguna');
        }
        redirect('users');
    }
}

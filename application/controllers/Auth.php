<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Controller
 * 
 * Menangani authentication dan session management
 */
class Auth extends CI_Controller {

    /**
     * @var CI_Session
     */
    public $session;

    /**
     * @var CI_Input
     */
    public $input;

    public function __construct()
    {
        parent::__construct();
        // Session, helpers (url, form, logging) sudah di-autoload
        $this->load->model('User_model');
        $this->load->helper('cookie');
    }

    /**
     * Login page
     */
    public function index()
    {
        log_info('Login page accessed');
        
        // Redirect ke dashboard jika sudah login
        if ($this->session->userdata('logged_in')) {
            log_info('User already logged in, redirecting to dashboard');
            redirect('dashboard');
        }

        $this->load->view('auth/login');
    }

    /**
     * Process login
     */
    public function login()
    {
        try {
            log_info('Login process started');
            
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                log_warning('Login accessed with non-POST method');
                redirect('auth');
            }

            // Validation rules
            $this->form_validation->set_rules('username', 'Username', 'required|trim');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if (!$this->form_validation->run()) {
                log_warning('Login form validation failed', 'auth');
                $this->load->view('auth/login');
                return;
            }

            $username = $this->input->post('username', TRUE);
            $password = $this->input->post('password', TRUE);
            $remember = $this->input->post('remember');

            log_info('Login attempt for username: ' . $username, 'auth');

            // Get user from database
            $user = $this->User_model->get_by_username($username);

            if (!$user) {
                log_failed_login($username);
                $this->session->set_flashdata('error', 'Username atau password salah');
                redirect('auth');
                return;
            }

            log_debug('User found in database: ' . $username, 'auth');

            // Verify password against password_hash column
            if (!isset($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
                log_failed_login($username);
                $this->session->set_flashdata('error', 'Username atau password salah');
                redirect('auth');
                return;
            }

            log_debug('Password verified for user: ' . $username, 'auth');

            // Check if user is active
            if ($user['active'] != 1) {
                log_security('Login attempt with inactive account: ' . $username, 'WARNING');
                $this->session->set_flashdata('error', 'Akun Anda telah dinonaktifkan');
                redirect('auth');
                return;
            }

            // Set session data
            $session_data = array(
                'user_id' => $user['id'],
                'username' => $user['username'],
                'name' => $user['name'],
                'role' => $user['role'],
                'logged_in' => TRUE
            );

            $this->session->set_userdata($session_data);

            log_debug('Session data set for user: ' . $username, 'auth');

            // Remember me functionality
            if ($remember) {
                $this->input->set_cookie(array(
                    'name' => 'remember_user',
                    'value' => base64_encode($username),
                    'expire' => 60 * 60 * 24 * 30 // 30 days
                ));
                log_debug('Remember me cookie set for user: ' . $username, 'auth');
            }

            // Log successful login
            log_successful_login($username);

            $this->session->set_flashdata('success', 'Selamat datang, ' . $user['name']);
            redirect('dashboard');
            
        } catch (Exception $e) {
            log_exception($e, 'auth');
            $this->session->set_flashdata('error', 'Terjadi kesalahan. Silakan coba lagi.');
            redirect('auth');
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        try {
            $username = $this->session->userdata('username');
            
            log_info('Logout process started for user: ' . $username, 'auth');
            
            // Delete remember me cookie first
            delete_cookie('remember_user');

            // Unset user session data but keep session for flashdata
            $this->session->unset_userdata(array('user_id','username','name','role','logged_in'));
            // Regenerate session to mitigate fixation and clear residuals
            $this->session->sess_regenerate(TRUE);
            // Set flash AFTER regeneration so it persists
            $this->session->set_flashdata('success', 'Anda telah berhasil logout');

            log_auth('User ' . $username . ' logged out', 'INFO');
            redirect('auth');
            
        } catch (Exception $e) {
            log_exception($e, 'auth');
            redirect('auth');
        }
    }

    /**
     * Check if user is logged in
     */
    public function is_logged_in()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    /**
     * Check user role
     */
    public function check_role($required_role)
    {
        $user_role = $this->session->userdata('role');
        
        if ($user_role !== $required_role) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Chatbot_access_model
 * 
 * Model untuk mengelola pengaturan akses chatbot
 */
class Chatbot_access_model extends CI_Model {

    private $table = 'chatbot_access';

    /**
     * Daftar fitur chatbot yang tersedia
     */
    public static $available_features = array(
        'product_info'  => 'Informasi Produk/Obat',
        'stock_check'   => 'Cek Stok',
        'price_check'   => 'Cek Harga',
        'sales_info'    => 'Info Penjualan',
        'order_create'  => 'Buat Pesanan'
    );

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all chatbot access entries
     */
    public function get_all()
    {
        return $this->db->order_by('phone_number', 'ASC')
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Get allowed entries only
     */
    public function get_all_allowed()
    {
        return $this->db->where('is_allowed', 1)
                        ->order_by('phone_number', 'ASC')
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Get entry by ID
     */
    public function get_by_id($id)
    {
        return $this->db->where('id', $id)
                        ->get($this->table)
                        ->row_array();
    }

    /**
     * Get entry by phone number
     */
    public function get_by_phone($phone_number)
    {
        return $this->db->where('phone_number', $phone_number)
                        ->get($this->table)
                        ->row_array();
    }

    /**
     * Check if phone number can access chatbot
     * Returns access info or default (*) access
     */
    public function check_access($phone_number)
    {
        // First check specific phone number
        $access = $this->get_by_phone($phone_number);
        
        if ($access) {
            // Check if parttime and within date range
            if ($access['access_type'] === 'parttime') {
                $today = date('Y-m-d');
                if ($access['start_date'] && $today < $access['start_date']) {
                    return $this->get_default_access();
                }
                if ($access['end_date'] && $today > $access['end_date']) {
                    return $this->get_default_access();
                }
            }
            return $access;
        }
        
        // Return default access if no specific entry found
        return $this->get_default_access();
    }

    /**
     * Get default access (*)
     */
    public function get_default_access()
    {
        $default = $this->get_by_phone('*');
        if (!$default) {
            // Create default if not exists
            $default_access = json_encode(array(
                'product_info' => true,
                'stock_check' => false,
                'price_check' => false,
                'sales_info' => false,
                'order_create' => false
            ));
            $this->create(array(
                'phone_number' => '*',
                'access_type' => 'lifetime',
                'is_allowed' => 1,
                'custom_access' => $default_access,
                'notes' => 'Default akses untuk semua nomor'
            ));
            return $this->get_by_phone('*');
        }
        return $default;
    }

    /**
     * Create new chatbot access entry
     */
    public function create($data)
    {
        $insert_data = array(
            'phone_number' => $data['phone_number'],
            'access_type' => isset($data['access_type']) ? $data['access_type'] : 'lifetime',
            'start_date' => isset($data['start_date']) && $data['start_date'] ? $data['start_date'] : null,
            'end_date' => isset($data['end_date']) && $data['end_date'] ? $data['end_date'] : null,
            'is_allowed' => isset($data['is_allowed']) ? (int)$data['is_allowed'] : 1,
            'custom_access' => isset($data['custom_access']) ? $data['custom_access'] : null,
            'notes' => isset($data['notes']) ? $data['notes'] : null,
            'created_at' => date('Y-m-d H:i:s')
        );

        return $this->db->insert($this->table, $insert_data);
    }

    /**
     * Update chatbot access entry
     */
    public function update($id, $data)
    {
        $update_data = array();

        if (isset($data['phone_number'])) {
            $update_data['phone_number'] = $data['phone_number'];
        }
        if (isset($data['access_type'])) {
            $update_data['access_type'] = $data['access_type'];
        }
        if (array_key_exists('start_date', $data)) {
            $update_data['start_date'] = $data['start_date'] ?: null;
        }
        if (array_key_exists('end_date', $data)) {
            $update_data['end_date'] = $data['end_date'] ?: null;
        }
        if (isset($data['is_allowed'])) {
            $update_data['is_allowed'] = (int)$data['is_allowed'];
        }
        if (isset($data['custom_access'])) {
            $update_data['custom_access'] = $data['custom_access'];
        }
        if (array_key_exists('notes', $data)) {
            $update_data['notes'] = $data['notes'];
        }

        $update_data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->where('id', $id)
                        ->update($this->table, $update_data);
    }

    /**
     * Toggle is_allowed status
     */
    public function toggle_allowed($id)
    {
        $entry = $this->get_by_id($id);
        if (!$entry) return false;
        
        $new_status = $entry['is_allowed'] ? 0 : 1;
        return $this->update($id, array('is_allowed' => $new_status));
    }

    /**
     * Delete chatbot access entry
     */
    public function delete($id)
    {
        // Prevent deleting default access
        $entry = $this->get_by_id($id);
        if ($entry && $entry['phone_number'] === '*') {
            return false;
        }
        
        return $this->db->where('id', $id)
                        ->delete($this->table);
    }

    /**
     * Check if phone number exists (for validation)
     */
    public function phone_exists($phone_number, $exclude_id = null)
    {
        $this->db->where('phone_number', $phone_number);
        
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        $result = $this->db->get($this->table)->row_array();
        return !empty($result);
    }

    /**
     * Get count of entries
     */
    public function count_all()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * Get count of allowed entries
     */
    public function count_allowed()
    {
        return $this->db->where('is_allowed', 1)
                        ->count_all_results($this->table);
    }

    /**
     * Parse custom_access JSON to array
     */
    public function parse_custom_access($json_string)
    {
        if (empty($json_string)) {
            return array();
        }
        $decoded = json_decode($json_string, true);
        return is_array($decoded) ? $decoded : array();
    }

    /**
     * Check if a specific feature is allowed for a phone number
     */
    public function can_access_feature($phone_number, $feature)
    {
        $access = $this->check_access($phone_number);
        
        if (!$access || !$access['is_allowed']) {
            return false;
        }
        
        $custom_access = $this->parse_custom_access($access['custom_access']);
        
        return isset($custom_access[$feature]) && $custom_access[$feature];
    }
}

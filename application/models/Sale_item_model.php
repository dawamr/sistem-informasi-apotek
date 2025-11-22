<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sale_item_model
 * 
 * Model untuk mengelola detail item penjualan
 */
class Sale_item_model extends CI_Model {

    private $table = 'sale_items';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get sale items by sale ID
     */
    public function get_by_sale_id($sale_id)
    {
        return $this->db->select('si.*, m.code, m.name, m.unit')
                        ->from($this->table . ' si')
                        ->join('medicines m', 'm.id = si.medicine_id')
                        ->where('si.sale_id', $sale_id)
                        ->get()
                        ->result_array();
    }

    /**
     * Get items sold by date
     */
    public function get_items_by_date($date)
    {
        return $this->db->select('si.medicine_id, m.code, m.name, m.unit, SUM(si.qty) as qty_sold, SUM(si.subtotal) as total_amount')
                        ->from($this->table . ' si')
                        ->join('medicines m', 'm.id = si.medicine_id')
                        ->join('sales s', 's.id = si.sale_id')
                        ->where('s.sale_date', $date)
                        ->group_by('si.medicine_id')
                        ->order_by('qty_sold', 'DESC')
                        ->get()
                        ->result_array();
    }

    /**
     * Get top selling products
     */
    public function get_top_products($start_date, $end_date, $limit = 10)
    {
        return $this->db->select('si.medicine_id, m.code, m.name, SUM(si.qty) as qty_sold, SUM(si.subtotal) as total_amount')
                        ->from($this->table . ' si')
                        ->join('medicines m', 'm.id = si.medicine_id')
                        ->join('sales s', 's.id = si.sale_id')
                        ->where('s.sale_date >=', $start_date)
                        ->where('s.sale_date <=', $end_date)
                        ->group_by('si.medicine_id')
                        ->order_by('qty_sold', 'DESC')
                        ->limit($limit)
                        ->get()
                        ->result_array();
    }

    /**
     * Create sale item
     */
    public function create($data)
    {
        $insert_data = array(
            'sale_id' => $data['sale_id'],
            'medicine_id' => $data['medicine_id'],
            'qty' => $data['qty'],
            'price' => $data['price'],
            'subtotal' => $data['qty'] * $data['price']
        );

        return $this->db->insert($this->table, $insert_data);
    }

    /**
     * Create multiple sale items
     */
    public function create_batch($items)
    {
        return $this->db->insert_batch($this->table, $items);
    }

    /**
     * Update sale item
     */
    public function update($id, $data)
    {
        $update_data = array();

        if (isset($data['qty'])) {
            $update_data['qty'] = $data['qty'];
        }

        if (isset($data['price'])) {
            $update_data['price'] = $data['price'];
        }

        if (isset($data['qty']) || isset($data['price'])) {
            $qty = isset($data['qty']) ? $data['qty'] : $this->db->select('qty')->where('id', $id)->get($this->table)->row()->qty;
            $price = isset($data['price']) ? $data['price'] : $this->db->select('price')->where('id', $id)->get($this->table)->row()->price;
            $update_data['subtotal'] = $qty * $price;
        }

        return $this->db->where('id', $id)
                        ->update($this->table, $update_data);
    }

    /**
     * Delete sale item
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)
                        ->delete($this->table);
    }

    /**
     * Delete all items for a sale
     */
    public function delete_by_sale_id($sale_id)
    {
        return $this->db->where('sale_id', $sale_id)
                        ->delete($this->table);
    }

    /**
     * Count items in a sale
     */
    public function count_by_sale_id($sale_id)
    {
        return $this->db->where('sale_id', $sale_id)
                        ->count_all_results($this->table);
    }
}

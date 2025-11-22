<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sale_model
 * 
 * Model untuk mengelola transaksi penjualan (header)
 */
class Sale_model extends CI_Model {

    private $table = 'sales';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get sale by ID
     */
    public function get_by_id($id)
    {
        return $this->db->where('id', $id)
                        ->get($this->table)
                        ->row_array();
    }

    /**
     * Get sale by invoice number
     */
    public function get_by_invoice($invoice_number)
    {
        return $this->db->where('invoice_number', $invoice_number)
                        ->get($this->table)
                        ->row_array();
    }

    /**
     * Get sales by date
     */
    public function get_by_date($date)
    {
        return $this->db->where('sale_date', $date)
                        ->order_by('sale_time', 'ASC')
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Get sales between dates
     */
    public function get_between_dates($start_date, $end_date)
    {
        return $this->db->where('sale_date >=', $start_date)
                        ->where('sale_date <=', $end_date)
                        ->order_by('sale_date', 'DESC')
                        ->order_by('sale_time', 'DESC')
                        ->get($this->table)
                        ->result_array();
    }

    /**
     * Get daily sales summary
     */
    public function get_daily_summary($date)
    {
        $this->db->select('COUNT(id) as total_transactions, SUM(total_amount) as total_sales, SUM(total_items) as total_items_sold');
        $this->db->where('sale_date', $date);
        $result = $this->db->get($this->table)->row_array();
        
        return array(
            'date' => $date,
            'total_transactions' => (int)$result['total_transactions'],
            'total_items_sold' => (int)$result['total_items_sold'],
            'total_sales_amount' => (float)$result['total_sales'],
            'currency' => 'IDR'
        );
    }

    /**
     * Create new sale
     */
    public function create($data)
    {
        $insert_data = array(
            'invoice_number' => $data['invoice_number'],
            'sale_date' => $data['sale_date'],
            'sale_time' => isset($data['sale_time']) ? $data['sale_time'] : date('H:i:s'),
            'customer_id' => isset($data['customer_id']) ? $data['customer_id'] : NULL,
            'total_amount' => $data['total_amount'],
            'total_items' => $data['total_items'],
            'created_by' => $data['created_by'],
            'created_at' => date('Y-m-d H:i:s')
        );

        if ($this->db->insert($this->table, $insert_data)) {
            return $this->db->insert_id();
        }

        return FALSE;
    }

    /**
     * Update sale
     */
    public function update($id, $data)
    {
        $update_data = array();

        if (isset($data['total_amount'])) {
            $update_data['total_amount'] = $data['total_amount'];
        }

        if (isset($data['total_items'])) {
            $update_data['total_items'] = $data['total_items'];
        }

        return $this->db->where('id', $id)
                        ->update($this->table, $update_data);
    }

    /**
     * Delete sale
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)
                        ->delete($this->table);
    }

    /**
     * Count total sales by date
     */
    public function count_by_date($date)
    {
        return $this->db->where('sale_date', $date)
                        ->count_all_results($this->table);
    }

    /**
     * Get total sales amount by date
     */
    public function get_total_amount_by_date($date)
    {
        $this->db->select('SUM(total_amount) as total');
        $this->db->where('sale_date', $date);
        $result = $this->db->get($this->table)->row_array();
        return (float)$result['total'];
    }

    /**
     * Check if invoice exists
     */
    public function invoice_exists($invoice_number)
    {
        $result = $this->db->where('invoice_number', $invoice_number)
                           ->get($this->table)
                           ->row_array();
        return !empty($result);
    }

    /**
     * Get recent sales
     *
     * @param int $limit
     * @return array
     */
    public function get_recent($limit = 5)
    {
        return $this->db->select('id, invoice_number, sale_date, sale_time, total_amount')
                        ->from($this->table)
                        ->order_by('sale_date', 'DESC')
                        ->order_by('sale_time', 'DESC')
                        ->limit((int)$limit)
                        ->get()
                        ->result_array();
    }

    /**
     * Get sales trend for last N days (including today)
     * Returns associative array with labels and values
     *
     * @param int $days
     * @return array{labels: array, values: array}
     */
    public function get_sales_trend($days = 7)
    {
        // Build date list
        $labels = array();
        $map = array();
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} day"));
            $labels[] = $d;
            $map[$d] = 0.0;
        }

        // Query totals grouped by date
        $start = $labels[0];
        $end = end($labels);
        $rows = $this->db->select('sale_date, SUM(total_amount) as total')
                         ->from($this->table)
                         ->where('sale_date >=', $start)
                         ->where('sale_date <=', $end)
                         ->group_by('sale_date')
                         ->order_by('sale_date', 'ASC')
                         ->get()
                         ->result_array();

        foreach ($rows as $r) {
            $d = $r['sale_date'];
            if (isset($map[$d])) {
                $map[$d] = (float)$r['total'];
            }
        }

        // Values aligned with labels
        $values = array();
        foreach ($labels as $d) {
            $values[] = $map[$d];
        }

        return array(
            'labels' => $labels,
            'values' => $values,
        );
    }
}

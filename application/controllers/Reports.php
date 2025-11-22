<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) { redirect('auth'); }
        $this->load->model('Sale_model');
        $this->load->model('Sale_item_model');
        $this->load->model('Stock_model');
        $this->load->model('Attendance_model');
        $this->load->model('Medicine_model');
    }

    public function sales()
    {
        $start = $this->input->get('start') ?: date('Y-m-01');
        $end = $this->input->get('end') ?: date('Y-m-t');
        $period = $this->input->get('period') ?: 'daily';
        if (!in_array($period, array('daily','weekly','monthly'), true)) { $period = 'daily'; }
        $rows = $this->Sale_model->get_between_dates($start, $end);
        $agg = array();
        foreach ($rows as $r) {
            $d = $r['sale_date'];
            $key = $d;
            $label = $d;
            if ($period === 'weekly') {
                $key = date('o-\WW', strtotime($d));
                $label = $key;
            } elseif ($period === 'monthly') {
                $key = date('Y-m', strtotime($d));
                $label = $key;
            }
            if (!isset($agg[$key])) {
                $agg[$key] = array('label' => $label, 'transactions' => 0, 'total_items' => 0, 'total_amount' => 0.0);
            }
            $agg[$key]['transactions'] += 1;
            $agg[$key]['total_items'] += (int)$r['total_items'];
            $agg[$key]['total_amount'] += (float)$r['total_amount'];
        }
        ksort($agg);
        $labels = array();
        $values = array();
        foreach ($agg as $a) {
            $labels[] = $a['label'];
            $values[] = round($a['total_amount'], 2);
        }
        // Top products
        $top_products = $this->Sale_item_model->get_top_products($start, $end, 10);
        $top_labels = array_map(function($r){ return $r['name']; }, $top_products);
        $top_qty = array_map(function($r){ return (int)$r['qty_sold']; }, $top_products);
        $top_amount = array_map(function($r){ return round((float)$r['total_amount'], 2); }, $top_products);
        // Sales by category
        $cat_rows = $this->db->select("COALESCE(c.name, 'Tanpa Kategori') as category_name, SUM(si.qty) as qty_sold, SUM(si.subtotal) as total_amount")
                             ->from('sale_items si')
                             ->join('sales s', 's.id = si.sale_id')
                             ->join('medicines m', 'm.id = si.medicine_id')
                             ->join('medicine_categories c', 'c.id = m.category_id', 'left')
                             ->where('s.sale_date >=', $start)
                             ->where('s.sale_date <=', $end)
                             ->group_by('c.id')
                             ->order_by('total_amount', 'DESC')
                             ->get()->result_array();
        $cat_labels = array_map(function($r){ return $r['category_name']; }, $cat_rows);
        $cat_amounts = array_map(function($r){ return round((float)$r['total_amount'], 2); }, $cat_rows);

        // Optional CSV export
        if ($this->input->get('export') === 'csv') {
            $dataset = $this->input->get('dataset');
            if ($dataset === 'top') {
                $filename = 'sales_top_products_' . $start . '_to_' . $end . '.csv';
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename=' . $filename);
                $out = fopen('php://output', 'w');
                fputcsv($out, array('Kode','Nama Produk','Qty Terjual','Total Pendapatan (IDR)'));
                foreach ($top_products as $r) {
                    fputcsv($out, array($r['code'], $r['name'], (int)$r['qty_sold'], (float)$r['total_amount']));
                }
                fclose($out);
                return;
            } elseif ($dataset === 'category') {
                $filename = 'sales_by_category_' . $start . '_to_' . $end . '.csv';
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename=' . $filename);
                $out = fopen('php://output', 'w');
                fputcsv($out, array('Kategori','Qty Terjual','Total Pendapatan (IDR)'));
                foreach ($cat_rows as $r) {
                    fputcsv($out, array($r['category_name'], (int)$r['qty_sold'], (float)$r['total_amount']));
                }
                fclose($out);
                return;
            } else {
                // Default: period aggregate
                $filename = 'sales_report_' . $period . '_' . $start . '_to_' . $end . '.csv';
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename=' . $filename);
                $out = fopen('php://output', 'w');
                fputcsv($out, array('Periode','Transaksi','Total Item','Total Penjualan (IDR)'));
                foreach ($agg as $a) {
                    fputcsv($out, array($a['label'], $a['transactions'], $a['total_items'], $a['total_amount']));
                }
                fclose($out);
                return;
            }
        }

        $data = array(
            'page_title' => 'Laporan Penjualan',
            'current_page' => 'sales-report',
            'start' => $start,
            'end' => $end,
            'period' => $period,
            'agg' => $agg,
            'chart_labels' => json_encode(array_values($labels)),
            'chart_values' => json_encode(array_values($values)),
            'top_labels' => json_encode(array_values($top_labels)),
            'top_qty' => json_encode(array_values($top_qty)),
            'top_amount' => json_encode(array_values($top_amount)),
            'cat_labels' => json_encode(array_values($cat_labels)),
            'cat_amounts' => json_encode(array_values($cat_amounts)),
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('reports/sales', $data);
        $this->load->view('templates/footer', $data);
    }

    public function stock()
    {
        $start = $this->input->get('start') ?: date('Y-m-d', strtotime('-30 days'));
        $end = $this->input->get('end') ?: date('Y-m-d');
        $threshold = (int)($this->input->get('threshold') ?: 50);

        // Current stock list (join categories)
        $current = $this->db->select("m.id, m.code, m.name, m.unit, m.current_stock, m.price, COALESCE(c.name, 'Tanpa Kategori') as category_name")
                            ->from('medicines m')
                            ->join('medicine_categories c', 'c.id = m.category_id', 'left')
                            ->where('m.is_active', 1)
                            ->order_by('m.name', 'ASC')
                            ->get()->result_array();
        $total_skus = count($current);
        $total_units = 0; $total_value = 0.0;
        foreach ($current as $r) { $total_units += (int)$r['current_stock']; $total_value += ((int)$r['current_stock']) * (float)$r['price']; }

        // Stock movement logs in range
        $movements = $this->db->select('sl.id, sl.log_date, sl.type, sl.ref_type, sl.ref_id, sl.qty, sl.notes, m.code, m.name, m.unit')
                              ->from('stock_logs sl')
                              ->join('medicines m', 'm.id = sl.medicine_id')
                              ->where('DATE(sl.log_date) >=', $start)
                              ->where('DATE(sl.log_date) <=', $end)
                              ->order_by('sl.log_date', 'DESC')
                              ->get()->result_array();

        // Reorder list using low stock helper
        $reorder = $this->Medicine_model->get_low_stock($threshold);

        $data = array(
            'page_title' => 'Laporan Stok',
            'current_page' => 'stock-report',
            'start' => $start,
            'end' => $end,
            'threshold' => $threshold,
            'current' => $current,
            'movements' => $movements,
            'reorder' => $reorder,
            'total_skus' => $total_skus,
            'total_units' => $total_units,
            'total_value' => $total_value,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('reports/stock', $data);
        $this->load->view('templates/footer', $data);
    }

    public function attendance()
    {
        $month = $this->input->get('month') ?: date('Y-m'); // format YYYY-MM
        $user_id = $this->input->get('user_id');
        // Resolve month to start/end
        $start = date('Y-m-01', strtotime($month . '-01'));
        $end = date('Y-m-t', strtotime($start));

        // Users for filter dropdown (some schemas may not have is_active column)
        $usersQ = $this->db->select('id, name')->from('users');
        if ($this->db->field_exists('is_active', 'users')) {
            $usersQ->where('is_active', 1);
        }
        $users = $usersQ->order_by('name', 'ASC')->get()->result_array();

        // Per-user monthly summary
        $this->db->select("a.user_id, u.name as user_name,
                           COUNT(*) as scheduled,
                           SUM(CASE WHEN a.status='hadir' THEN 1 ELSE 0 END) as present,
                           SUM(CASE WHEN a.status='izin' THEN 1 ELSE 0 END) as permission,
                           SUM(CASE WHEN a.status='sakit' THEN 1 ELSE 0 END) as sick,
                           SUM(CASE WHEN a.status='alpha' THEN 1 ELSE 0 END) as absent,
                           SUM(CASE WHEN a.status='hadir' AND a.checkin_time IS NOT NULL AND s.start_time IS NOT NULL AND a.checkin_time > s.start_time THEN 1 ELSE 0 END) as late_count")
                 ->from('attendances a')
                 ->join('users u', 'u.id = a.user_id')
                 ->join('shifts s', 's.id = a.shift_id')
                 ->where('a.date >=', $start)
                 ->where('a.date <=', $end);
        if (!empty($user_id)) { $this->db->where('a.user_id', (int)$user_id); }
        $per_user = $this->db->group_by('a.user_id')
                             ->order_by('u.name', 'ASC')
                             ->get()->result_array();

        // Overall monthly totals by status
        $totals = $this->db->select("COUNT(*) as total,
                                     SUM(CASE WHEN status='hadir' THEN 1 ELSE 0 END) as present,
                                     SUM(CASE WHEN status='izin' THEN 1 ELSE 0 END) as permission,
                                     SUM(CASE WHEN status='sakit' THEN 1 ELSE 0 END) as sick,
                                     SUM(CASE WHEN status='alpha' THEN 1 ELSE 0 END) as absent")
                            ->from('attendances')
                            ->where('date >=', $start)
                            ->where('date <=', $end)
                            ->get()->row_array();

        // Daily totals for chart
        $daily_rows = $this->db->select("date,
                                         SUM(CASE WHEN status='hadir' THEN 1 ELSE 0 END) as present,
                                         SUM(CASE WHEN status='alpha' THEN 1 ELSE 0 END) as absent")
                               ->from('attendances')
                               ->where('date >=', $start)
                               ->where('date <=', $end)
                               ->group_by('date')
                               ->order_by('date', 'ASC')
                               ->get()->result_array();
        // Build complete label range for the month
        $labels = array(); $presentVals = array(); $absentVals = array();
        $map = array(); foreach ($daily_rows as $r) { $map[$r['date']] = $r; }
        $cur = $start;
        while ($cur <= $end) {
            $labels[] = $cur;
            $presentVals[] = isset($map[$cur]) ? (int)$map[$cur]['present'] : 0;
            $absentVals[] = isset($map[$cur]) ? (int)$map[$cur]['absent'] : 0;
            $cur = date('Y-m-d', strtotime($cur . ' +1 day'));
        }

        $data = array(
            'page_title' => 'Laporan Absensi',
            'current_page' => 'attendance-report',
            'month' => $month,
            'start' => $start,
            'end' => $end,
            'user_id' => $user_id,
            'users' => $users,
            'per_user' => $per_user,
            'totals' => $totals,
            'chart_labels' => json_encode($labels),
            'chart_present' => json_encode($presentVals),
            'chart_absent' => json_encode($absentVals),
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('reports/attendance', $data);
        $this->load->view('templates/footer', $data);
    }
}

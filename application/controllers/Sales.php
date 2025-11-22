<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) { redirect('auth'); }
        $this->load->model('Sale_model');
        $this->load->model('Medicine_model');
        $this->load->database();
        $this->load->helper('url');
    }

    // POS UI
    public function pos()
    {
        $cart = $this->session->userdata('cart') ?: array();
        $data = array(
            'page_title' => 'POS Penjualan',
            'current_page' => 'pos',
            'cart' => $cart,
            'page_scripts' => array('pages/sales-pos.js'),
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('sales/pos', $data);
        $this->load->view('templates/footer', $data);
    }

    // Autocomplete search medicines
    public function search()
    {
        $q = trim($this->input->get('q', TRUE));
        $results = array();
        if ($q !== '') {
            $rows = $this->Medicine_model->search($q);
            foreach ($rows as $m) {
                $results[] = array(
                    'id' => (int)$m['id'],
                    'code' => $m['code'],
                    'name' => $m['name'],
                    'price' => (int)$m['price'],
                    'stock' => (int)$m['current_stock'],
                    'unit' => $m['unit'],
                );
            }
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => $results)));
    }

    // Add item to cart
    public function add()
    {
        if ($this->input->method() !== 'post') { show_404(); }
        $id = (int)$this->input->post('medicine_id');
        $qty = (int)$this->input->post('qty');
        $med = $this->Medicine_model->get_by_id($id);
        if (!$med || $qty < 1) { $this->_json_error('Item tidak valid'); return; }
        $cart = $this->session->userdata('cart') ?: array();
        if (!isset($cart[$id])) {
            $cart[$id] = array(
                'id' => $id,
                'code' => $med['code'],
                'name' => $med['name'],
                'price' => (int)$med['price'],
                'qty' => 0,
                'unit' => $med['unit'],
            );
        }
        $cart[$id]['qty'] += $qty;
        $this->session->set_userdata('cart', $cart);
        $this->_json_ok(array('cart' => $cart));
    }

    // Update qty
    public function update()
    {
        if ($this->input->method() !== 'post') { show_404(); }
        $id = (int)$this->input->post('medicine_id');
        $qty = (int)$this->input->post('qty');
        $cart = $this->session->userdata('cart') ?: array();
        if (!isset($cart[$id])) { $this->_json_error('Item tidak ada di keranjang'); return; }
        if ($qty < 1) { unset($cart[$id]); } else { $cart[$id]['qty'] = $qty; }
        $this->session->set_userdata('cart', $cart);
        $this->_json_ok(array('cart' => $cart));
    }

    // Remove item
    public function remove()
    {
        if ($this->input->method() !== 'post') { show_404(); }
        $id = (int)$this->input->post('medicine_id');
        $cart = $this->session->userdata('cart') ?: array();
        if (isset($cart[$id])) { unset($cart[$id]); }
        $this->session->set_userdata('cart', $cart);
        $this->_json_ok(array('cart' => $cart));
    }

    // Checkout -> save to DB
    public function checkout()
    {
        if ($this->input->method() !== 'post') { show_404(); }
        $cart = $this->session->userdata('cart') ?: array();
        if (empty($cart)) { $this->_json_error('Keranjang kosong'); return; }

        // Compute totals
        $total_items = 0; $total_amount = 0;
        foreach ($cart as $it) { $total_items += (int)$it['qty']; $total_amount += ((int)$it['qty'] * (int)$it['price']); }

        // Create sale
        $invoice = $this->_generate_invoice();
        $sale_id = $this->Sale_model->create(array(
            'invoice_number' => $invoice,
            'sale_date' => date('Y-m-d'),
            'sale_time' => date('H:i:s'),
            'customer_id' => NULL,
            'total_amount' => $total_amount,
            'total_items' => $total_items,
            'created_by' => $this->session->userdata('user_id'),
        ));
        if (!$sale_id) { $this->_json_error('Gagal menyimpan transaksi'); return; }

        // Insert items and update stock
        foreach ($cart as $it) {
            $this->db->insert('sale_items', array(
                'sale_id' => $sale_id,
                'medicine_id' => $it['id'],
                'qty' => (int)$it['qty'],
                'price' => (int)$it['price'],
                'subtotal' => (int)$it['qty'] * (int)$it['price'],
            ));
            // reduce stock
            $this->db->set('current_stock', 'current_stock - '.(int)$it['qty'], FALSE)
                     ->where('id', $it['id'])->update('medicines');
        }

        // Clear cart
        $this->session->unset_userdata('cart');
        $this->_json_ok(array('sale_id' => $sale_id, 'invoice' => $invoice, 'redirect' => base_url('sales/invoice/'.$sale_id)));
    }

    public function invoice($id)
    {
        $sale = $this->Sale_model->get_by_id($id);
        if (!$sale) { show_404(); }
        $items = $this->db->select('si.*, m.name, m.unit')
                          ->from('sale_items si')
                          ->join('medicines m','m.id=si.medicine_id','left')
                          ->where('si.sale_id', $id)->get()->result_array();
        $data = array(
            'page_title' => 'Invoice '.$sale['invoice_number'],
            'current_page' => 'sales-history',
            'sale' => $sale,
            'items' => $items,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('sales/invoice', $data);
        $this->load->view('templates/footer', $data);
    }

    public function history()
    {
        $start = $this->input->get('start') ?: date('Y-m-01');
        $end = $this->input->get('end') ?: date('Y-m-d');
        $sales = $this->Sale_model->get_between_dates($start, $end);
        $data = array(
            'page_title' => 'Riwayat Penjualan',
            'current_page' => 'sales-history',
            'sales' => $sales,
            'start' => $start,
            'end' => $end,
            'page_scripts' => array('pages/sales-history.js'),
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('sales/history', $data);
        $this->load->view('templates/footer', $data);
    }

    private function _generate_invoice()
    {
        return 'INV-'.date('Ymd').'-'.strtoupper(substr(md5(uniqid('', true)), 0, 6));
    }

    private function _json_ok($payload){
        $this->output->set_content_type('application/json')->set_output(json_encode(array('ok'=>true)+$payload));
    }
    private function _json_error($msg){
        $this->output->set_content_type('application/json')->set_output(json_encode(array('ok'=>false,'error'=>$msg)));
    }
}

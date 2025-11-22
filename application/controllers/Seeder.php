<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Seeder Controller - Untuk menjalankan seeder
 * Akses: http://localhost:8000/seeder/run
 * CATATAN: Hanya untuk development!
 */
class Seeder extends CI_Controller {

    /**
     * @var CI_DB_query_builder
     */
    public $db;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function run()
    {
        if (ENVIRONMENT === 'production') {
            show_error('Seeder tidak boleh dijalankan di production!', 403);
        }

        // Jalankan semua seeder dari SQL file
        $sql_file = APPPATH . '../docs/database_seeder.sql';
        
        if (!file_exists($sql_file)) {
            show_error('File database_seeder.sql tidak ditemukan!', 500);
        }

        $sql = file_get_contents($sql_file);
        
        // Split queries by semicolon
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        
        try {
            foreach ($queries as $query) {
                if (!empty($query) && strpos($query, '--') !== 0) {
                    $this->db->query($query);
                }
            }

            echo "<h2>✅ Seeding Berhasil!</h2>";
            echo "<p>Semua data dummy telah berhasil di-insert ke database.</p>";
            echo "<hr>";
            echo "<h3>Data Summary:</h3>";
            echo "<ul>";
            echo "<li>✓ API Keys: 3</li>";
            echo "<li>✓ Users: 5</li>";
            echo "<li>✓ Medicine Categories: 8</li>";
            echo "<li>✓ Medicines: 29</li>";
            echo "<li>✓ Customers: 10</li>";
            echo "<li>✓ Shifts: 10</li>";
            echo "<li>✓ Sales: 10</li>";
            echo "<li>✓ Sale Items: 25</li>";
            echo "<li>✓ Stock Logs: 20+</li>";
            echo "<li>✓ Attendances: 8</li>";
            echo "</ul>";
            echo "<p><a href='/'>← Kembali ke Home</a></p>";

        } catch (Exception $e) {
            show_error('Error saat seeding: ' . $e->getMessage(), 500);
        }
    }

    public function clear()
    {
        if (ENVIRONMENT === 'production') {
            show_error('Tidak boleh clear data di production!', 403);
        }

        try {
            $this->db->query('SET FOREIGN_KEY_CHECKS=0');
            $tables = array('stock_logs', 'sale_items', 'sales', 'attendances', 'shifts', 'customers', 'medicines', 'medicine_categories', 'users', 'api_keys');
            
            foreach ($tables as $table) {
                $this->db->query("TRUNCATE TABLE $table");
            }
            
            $this->db->query('SET FOREIGN_KEY_CHECKS=1');

            echo "<h2>✅ Database Cleared!</h2>";
            echo "<p>Semua data telah dihapus.</p>";
            echo "<p><a href='/seeder/run'>Jalankan Seeder Lagi</a></p>";

        } catch (Exception $e) {
            show_error('Error: ' . $e->getMessage(), 500);
        }
    }
}

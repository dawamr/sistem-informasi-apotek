<!-- Sidebar Navigation -->
<aside class="sidebar">
    <?php $CI =& get_instance(); $role_lc = strtolower((string)$CI->session->userdata('role')); ?>
    <ul class="sidebar-nav">
        <li>
            <a href="<?= base_url('dashboard') ?>" class="<?= ($current_page === 'dashboard') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        
        <!-- Master Data Section -->
        <li class="sidebar-section-title">
            <a href="#" class="text-muted small px-3 py-2 d-block">
                <i class="bi bi-folder"></i> Master Data
            </a>
        </li>
        <li>
            <a href="<?= base_url('medicines') ?>" class="<?= ($current_page === 'medicines') ? 'active' : '' ?>">
                <i class="bi bi-capsule"></i> Obat
            </a>
        </li>
        <?php if ($role_lc !== 'apoteker'): ?>
        <li>
            <a href="<?= base_url('users') ?>" class="<?= ($current_page === 'users') ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Pengguna
            </a>
        </li>
        <?php endif; ?>
        
        <!-- Transactions Section -->
        <li class="sidebar-section-title">
            <a href="#" class="text-muted small px-3 py-2 d-block">
                <i class="bi bi-receipt"></i> Transaksi
            </a>
        </li>
        <li>
            <a href="<?= base_url('sales/pos') ?>" class="<?= ($current_page === 'pos') ? 'active' : '' ?>">
                <i class="bi bi-bag-check"></i> Penjualan (POS)
            </a>
        </li>
        <li>
            <a href="<?= base_url('sales/history') ?>" class="<?= ($current_page === 'sales-history') ? 'active' : '' ?>">
                <i class="bi bi-clock-history"></i> Riwayat Penjualan
            </a>
        </li>
        <li>
            <a href="<?= base_url('stock') ?>" class="<?= ($current_page === 'stock') ? 'active' : '' ?>">
                <i class="bi bi-boxes"></i> Stok
            </a>
        </li>
        
        <!-- Attendance Section -->
        <li class="sidebar-section-title">
            <a href="#" class="text-muted small px-3 py-2 d-block">
                <i class="bi bi-calendar-check"></i> Absensi
            </a>
        </li>
        <li>
            <a href="<?= base_url('attendance') ?>" class="<?= ($current_page === 'attendance') ? 'active' : '' ?>">
                <i class="bi bi-check-circle"></i> Absensi Harian
            </a>
        </li>
        <li>
            <a href="<?= base_url('shifts') ?>" class="<?= ($current_page === 'shifts') ? 'active' : '' ?>">
                <i class="bi bi-calendar-event"></i> Jadwal Shift
            </a>
        </li>
        <?php if ($role_lc !== 'apoteker'): ?>
        <li>
            <a href="<?= base_url('shifts/rules') ?>" class="<?= ($current_page === 'shifts') ? 'active' : '' ?>">
                <i class="bi bi-diagram-3"></i> Aturan Penugasan Shift
            </a>
        </li>
        <?php endif; ?>
        
        <!-- Reports Section -->
        <li class="sidebar-section-title">
            <a href="#" class="text-muted small px-3 py-2 d-block">
                <i class="bi bi-graph-up"></i> Laporan
            </a>
        </li>
        <li>
            <a href="<?= base_url('reports/sales') ?>" class="<?= ($current_page === 'sales-report') ? 'active' : '' ?>">
                <i class="bi bi-bar-chart"></i> Laporan Penjualan
            </a>
        </li>
        <li>
            <a href="<?= base_url('reports/stock') ?>" class="<?= ($current_page === 'stock-report') ? 'active' : '' ?>">
                <i class="bi bi-pie-chart"></i> Laporan Stok
            </a>
        </li>
        <li>
            <a href="<?= base_url('reports/attendance') ?>" class="<?= ($current_page === 'attendance-report') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text"></i> Laporan Absensi
            </a>
        </li>
        
        <?php if ($role_lc !== 'apoteker'): ?>
        <!-- Settings Section (Admin Only) -->
        <li class="sidebar-section-title">
            <a href="#" class="text-muted small px-3 py-2 d-block">
                <i class="bi bi-gear"></i> Pengaturan
            </a>
        </li>
        <li>
            <a href="<?= base_url('settings') ?>" class="<?= ($current_page === 'settings') ? 'active' : '' ?>">
                <i class="bi bi-sliders"></i> Konfigurasi
            </a>
        </li>
        <?php endif; ?>
        
    </ul>
</aside>

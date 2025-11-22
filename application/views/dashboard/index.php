<!-- Main Content -->
<div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
        <p>Selamat datang kembali, <?= $current_user['name'] ?></p>
    </div>

    <!-- Summary Cards Row -->
    <div class="row mb-4">
        <!-- Total Sales Card -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label">Total Penjualan Hari Ini</p>
                            <p class="stat-value">Rp <?= number_format($total_sales, 0, ',', '.') ?></p>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-bag-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Transactions Card -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card stat-card success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label">Total Transaksi</p>
                            <p class="stat-value"><?= $total_transactions ?></p>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Sold Card -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card stat-card info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label">Item Terjual</p>
                            <p class="stat-value"><?= isset($total_items_sold) ? (int)$total_items_sold : 0 ?></p>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert Card -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card stat-card warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label">Stok Rendah</p>
                            <p class="stat-value"><?= count($low_stock_medicines) ?></p>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Card -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card stat-card info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label">Petugas Hadir</p>
                            <p class="stat-value"><?= count($today_attendance) ?></p>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Sales Trend Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-info text-dark">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Tren Penjualan (7 Hari)</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesTrendChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Transaksi Terbaru</h5>
                    <a href="<?= base_url('sales/history') ?>" class="btn btn-light btn-sm"><i class="bi bi-list"></i> Lihat Semua</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_sales)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($recent_sales as $sale): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= $sale['invoice_number'] ?></strong>
                                        <div class="small text-muted"><?= date('d/m/Y H:i', strtotime($sale['sale_date'].' '.$sale['sale_time'])) ?></div>
                                    </div>
                                    <span class="badge bg-success">Rp <?= number_format($sale['total_amount'], 0, ',', '.') ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Belum ada transaksi hari ini
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Stok Rendah</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($low_stock_medicines)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($low_stock_medicines as $medicine): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= $medicine['name'] ?></h6>
                                        <small class="text-muted"><?= $medicine['code'] ?></small>
                                    </div>
                                    <span class="badge bg-danger rounded-pill">
                                        <?= $medicine['current_stock'] ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle"></i> Semua stok aman
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="<?= base_url('sales/pos') ?>" class="btn btn-primary w-100">
                                <i class="bi bi-bag-check"></i> Penjualan Baru
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?= base_url('stock') ?>" class="btn btn-info w-100">
                                <i class="bi bi-boxes"></i> Kelola Stok
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?= base_url('attendance') ?>" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Absensi
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?= base_url('reports/sales') ?>" class="btn btn-warning w-100">
                                <i class="bi bi-bar-chart"></i> Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Trend Script -->
<script>
    (function(){
        try {
            var labels = <?= json_encode(isset($trend['labels']) ? array_map('strval', $trend['labels']) : []) ?>;
            var values = <?= json_encode(isset($trend['values']) ? array_map('floatval', $trend['values']) : []) ?>;
            if (labels.length && document.getElementById('salesTrendChart')) {
                var ctx = document.getElementById('salesTrendChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Penjualan (IDR)',
                            data: values,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13,110,253,0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }
        } catch (e) { console.error(e); }
    })();
</script>

<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Laporan Penjualan</h1>
      <p>Ringkasan penjualan per periode dengan grafik.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <form method="get" action="<?= base_url('reports/sales') ?>" class="row g-2 align-items-end">
        <div class="col-auto">
          <label class="form-label mb-0">Mulai</label>
          <input type="date" name="start" value="<?= htmlspecialchars($start) ?>" class="form-control" />
        </div>
        <div class="col-auto">
          <label class="form-label mb-0">Selesai</label>
          <input type="date" name="end" value="<?= htmlspecialchars($end) ?>" class="form-control" />
        </div>
        <div class="col-auto">
          <label class="form-label mb-0">Periode</label>
          <select name="period" class="form-select">
            <option value="daily" <?= $period==='daily'?'selected':'' ?>>Harian</option>
            <option value="weekly" <?= $period==='weekly'?'selected':'' ?>>Mingguan</option>
            <option value="monthly" <?= $period==='monthly'?'selected':'' ?>>Bulanan</option>
          </select>
        </div>
        <div class="col-auto">
          <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
        </div>
      </form>
      <?php 
        $exportUrl = base_url('reports/sales?start=' . urlencode($start) . '&end=' . urlencode($end) . '&period=' . urlencode($period) . '&export=csv');
        $exportTopUrl = base_url('reports/sales?start=' . urlencode($start) . '&end=' . urlencode($end) . '&period=' . urlencode($period) . '&export=csv&dataset=top');
        $exportCatUrl = base_url('reports/sales?start=' . urlencode($start) . '&end=' . urlencode($end) . '&period=' . urlencode($period) . '&export=csv&dataset=category');
      ?>
      <a href="<?= $exportUrl ?>" class="btn btn-outline-success"><i class="bi bi-download"></i> Export CSV (Ringkas)</a>
      <a href="<?= $exportTopUrl ?>" class="btn btn-outline-success"><i class="bi bi-download"></i> Export CSV (Top Produk)</a>
      <a href="<?= $exportCatUrl ?>" class="btn btn-outline-success"><i class="bi bi-download"></i> Export CSV (Kategori)</a>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <canvas id="salesChart" height="100"></canvas>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header">
          <strong>Top Produk Terjual</strong>
        </div>
        <div class="card-body">
          <canvas id="topProductsChart" height="220"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header">
          <strong>Penjualan per Kategori</strong>
        </div>
        <div class="card-body">
          <canvas id="categoryChart" height="220"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Periode</th>
              <th>Transaksi</th>
              <th>Total Item</th>
              <th>Total Penjualan (IDR)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($agg)): ?>
              <?php foreach ($agg as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['label']) ?></td>
                  <td><?= (int)$row['transactions'] ?></td>
                  <td><?= (int)$row['total_items'] ?></td>
                  <td><?= number_format($row['total_amount'], 0, ',', '.') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-center">Tidak ada data</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js via jsDelivr -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  (function(){
    const ctx = document.getElementById('salesChart').getContext('2d');
    const labels = <?= $chart_labels ?>;
    const dataVals = <?= $chart_values ?>;
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Total Penjualan (IDR)',
          data: dataVals,
          borderColor: 'rgba(54, 162, 235, 1)',
          backgroundColor: 'rgba(54, 162, 235, 0.2)',
          tension: 0.2,
          fill: true,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'top' },
          tooltip: { callbacks: { label: (ctx) => 'IDR ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y) } }
        },
        scales: {
          y: { ticks: { callback: (val) => 'IDR ' + new Intl.NumberFormat('id-ID').format(val) } }
        }
      }
    });

    // Top Products Chart (Qty & Amount)
    const tpCtx = document.getElementById('topProductsChart').getContext('2d');
    const tpLabels = <?= isset($top_labels) ? $top_labels : '[]' ?>;
    const tpQty = <?= isset($top_qty) ? $top_qty : '[]' ?>;
    const tpAmount = <?= isset($top_amount) ? $top_amount : '[]' ?>;
    new Chart(tpCtx, {
      type: 'bar',
      data: {
        labels: tpLabels,
        datasets: [
          {
            label: 'Qty Terjual',
            data: tpQty,
            backgroundColor: 'rgba(75, 192, 192, 0.6)'
          },
          {
            label: 'Pendapatan (IDR)',
            data: tpAmount,
            yAxisID: 'y1',
            backgroundColor: 'rgba(255, 159, 64, 0.6)'
          }
        ]
      },
      options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        scales: {
          y: { beginAtZero: true },
          y1: {
            beginAtZero: true,
            position: 'right',
            ticks: { callback: (val) => 'IDR ' + new Intl.NumberFormat('id-ID').format(val) }
          }
        }
      }
    });

    // Category Chart (Amount)
    const catCtx = document.getElementById('categoryChart').getContext('2d');
    const catLabels = <?= isset($cat_labels) ? $cat_labels : '[]' ?>;
    const catAmounts = <?= isset($cat_amounts) ? $cat_amounts : '[]' ?>;
    new Chart(catCtx, {
      type: 'doughnut',
      data: {
        labels: catLabels,
        datasets: [{
          label: 'Total Penjualan (IDR)',
          data: catAmounts,
          backgroundColor: [
            '#4dc9f6','#f67019','#f53794','#537bc4','#acc236','#166a8f','#00a950','#58595b','#8549ba','#ffc107'
          ]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    });
  })();
</script>

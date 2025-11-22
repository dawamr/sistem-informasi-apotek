<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Riwayat Penjualan</h1>
      <p>Filter berdasarkan rentang tanggal dan lihat detail invoice.</p>
    </div>
    <div>
      <a href="<?= base_url('sales/pos') ?>" class="btn btn-primary" data-bs-toggle="tooltip" title="Buka POS"><i class="bi bi-bag-check"></i> POS</a>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <form class="row g-3" method="get" action="<?= base_url('sales/history') ?>">
        <div class="col-md-4">
          <label class="form-label">Tanggal Mulai</label>
          <input type="date" name="start" class="form-control" value="<?= htmlspecialchars($start) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Tanggal Akhir</label>
          <input type="date" name="end" class="form-control" value="<?= htmlspecialchars($end) ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <button class="btn btn-secondary me-2"><i class="bi bi-funnel"></i> Filter</button>
          <a class="btn btn-outline-secondary" href="<?= base_url('sales/history') ?>"><i class="bi bi-x-circle"></i> Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table id="historyTable" class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Invoice</th>
              <th>Tanggal</th>
              <th>Waktu</th>
              <th>Items</th>
              <th>Total</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sales as $s): ?>
              <tr>
                <td><strong><?= htmlspecialchars($s['invoice_number']) ?></strong></td>
                <td><?= htmlspecialchars($s['sale_date']) ?></td>
                <td><?= htmlspecialchars($s['sale_time']) ?></td>
                <td><?= (int)$s['total_items'] ?></td>
                <td>Rp <?= number_format((int)$s['total_amount'], 0, ',', '.') ?></td>
                <td>
                  <a href="<?= base_url('sales/invoice/'.$s['id']) ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Lihat Invoice"><i class="bi bi-receipt"></i></a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

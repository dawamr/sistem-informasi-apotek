<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Invoice <?= htmlspecialchars($sale['invoice_number']) ?></h1>
      <p>Tanggal: <?= htmlspecialchars($sale['sale_date']) ?> &nbsp; Waktu: <?= htmlspecialchars($sale['sale_time']) ?></p>
    </div>
    <div>
      <button class="btn btn-outline-secondary" onclick="window.print()" data-bs-toggle="tooltip" title="Cetak"><i class="bi bi-printer"></i> Cetak</button>
      <a class="btn btn-primary" href="<?= base_url('sales/history') ?>"><i class="bi bi-clock-history"></i> Riwayat</a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-6">
          <h5 class="mb-1">Apotek Manager</h5>
          <div class="text-muted">Jl. Contoh No. 123, Jakarta</div>
        </div>
        <div class="col-md-6 text-md-end">
          <div><strong>Kasir:</strong> <?= htmlspecialchars($this->session->userdata('username') ?: 'system') ?></div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Nama Obat</th>
              <th>Qty</th>
              <th>Satuan</th>
              <th>Harga</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; $total=0; foreach ($items as $it): $sub=(int)$it['qty']*(int)$it['price']; $total+=$sub; ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($it['name']) ?></td>
                <td><?= (int)$it['qty'] ?></td>
                <td><?= htmlspecialchars($it['unit']) ?></td>
                <td>Rp <?= number_format((int)$it['price'], 0, ',', '.') ?></td>
                <td>Rp <?= number_format($sub, 0, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="5" class="text-end">Total Items</th>
              <th><?= (int)$sale['total_items'] ?></th>
            </tr>
            <tr>
              <th colspan="5" class="text-end">Total Bayar</th>
              <th>Rp <?= number_format((int)$sale['total_amount'], 0, ',', '.') ?></th>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="text-center text-muted mt-3">
        Terima kasih atas pembelian Anda
      </div>
    </div>
  </div>
</div>

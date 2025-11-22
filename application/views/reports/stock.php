<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Laporan Stok</h1>
      <p>Daftar stok saat ini, pergerakan stok dalam rentang tanggal, dan daftar reorder.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <form method="get" action="<?= base_url('reports/stock') ?>" class="row g-2 align-items-end">
        <div class="col-auto">
          <label class="form-label mb-0">Mulai</label>
          <input type="date" name="start" value="<?= htmlspecialchars($start) ?>" class="form-control" />
        </div>
        <div class="col-auto">
          <label class="form-label mb-0">Selesai</label>
          <input type="date" name="end" value="<?= htmlspecialchars($end) ?>" class="form-control" />
        </div>
        <div class="col-auto">
          <label class="form-label mb-0">Threshold Low Stock</label>
          <input type="number" name="threshold" min="0" value="<?= (int)$threshold ?>" class="form-control" />
        </div>
        <div class="col-auto">
          <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
        </div>
      </form>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="text-muted small">Total SKU</div>
          <div class="h3 mb-0"><?= number_format((int)$total_skus) ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="text-muted small">Total Unit</div>
          <div class="h3 mb-0"><?= number_format((int)$total_units) ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="text-muted small">Nilai Stok (IDR)</div>
          <div class="h3 mb-0">IDR <?= number_format((float)$total_value, 0, ',', '.') ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>Stok Saat Ini</strong>
      <span class="text-muted small">Per <?= date('Y-m-d') ?></span>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama</th>
              <th>Kategori</th>
              <th>Unit</th>
              <th class="text-end">Stok</th>
              <th class="text-end">Harga</th>
              <th class="text-end">Nilai</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($current)): ?>
              <?php foreach ($current as $row): $value = ((int)$row['current_stock']) * (float)$row['price']; ?>
                <tr>
                  <td><?= htmlspecialchars($row['code']) ?></td>
                  <td><?= htmlspecialchars($row['name']) ?></td>
                  <td><?= htmlspecialchars($row['category_name']) ?></td>
                  <td><?= htmlspecialchars($row['unit']) ?></td>
                  <td class="text-end"><?= number_format((int)$row['current_stock']) ?></td>
                  <td class="text-end"><?= number_format((float)$row['price'], 0, ',', '.') ?></td>
                  <td class="text-end"><?= number_format($value, 0, ',', '.') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <strong>Pergerakan Stok (<?= htmlspecialchars($start) ?> s/d <?= htmlspecialchars($end) ?>)</strong>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Kode</th>
              <th>Nama</th>
              <th>Jenis</th>
              <th class="text-end">Qty</th>
              <th>Ref</th>
              <th>Catatan</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($movements)): ?>
              <?php foreach ($movements as $m): ?>
                <tr>
                  <td><?= htmlspecialchars($m['log_date']) ?></td>
                  <td><?= htmlspecialchars($m['code']) ?></td>
                  <td><?= htmlspecialchars($m['name']) ?></td>
                  <td><?= htmlspecialchars(strtoupper($m['type'])) ?></td>
                  <td class="text-end"><?= number_format((int)$m['qty']) ?></td>
                  <td><?= htmlspecialchars(trim(($m['ref_type'] ?: '') . ' ' . ($m['ref_id'] ?: ''))) ?></td>
                  <td><?= htmlspecialchars($m['notes'] ?: '-') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>Reorder List (stok < <?= (int)$threshold ?>)</strong>
      <span class="text-muted small">Threshold: <?= (int)$threshold ?></span>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama</th>
              <th>Kategori</th>
              <th>Unit</th>
              <th class="text-end">Stok</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($reorder)): ?>
              <?php foreach ($reorder as $r): ?>
                <tr>
                  <td><?= htmlspecialchars($r['code']) ?></td>
                  <td><?= htmlspecialchars($r['name']) ?></td>
                  <td><?= htmlspecialchars($r['category_name']) ?></td>
                  <td><?= htmlspecialchars($r['unit']) ?></td>
                  <td class="text-end"><?= number_format((int)$r['current_stock']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center">Tidak ada item yang perlu di-reorder</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

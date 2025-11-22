<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Manajemen Stok</h1>
      <p>Mutasi stok dan daftar stok rendah.</p>
    </div>
    <div class="btn-group">
      <a href="<?= base_url('stock/in') ?>" class="btn btn-success" data-bs-toggle="tooltip" title="Stok Masuk"><i class="bi bi-box-arrow-in-down"></i> Stok Masuk</a>
      <a href="<?= base_url('stock/out') ?>" class="btn btn-danger" data-bs-toggle="tooltip" title="Stok Keluar"><i class="bi bi-box-arrow-up"></i> Stok Keluar</a>
      <a href="<?= base_url('stock/opname') ?>" class="btn btn-warning" data-bs-toggle="tooltip" title="Stok Opname"><i class="bi bi-clipboard-check"></i> Opname</a>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header bg-light">
          <strong>Mutasi Stok</strong>
        </div>
        <div class="card-body">
          <form class="row g-3" method="get" action="<?= base_url('stock') ?>">
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
              <a class="btn btn-outline-secondary" href="<?= base_url('stock') ?>"><i class="bi bi-x-circle"></i> Reset</a>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table id="stockLogsTable" class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Obat</th>
                  <th>Jenis</th>
                  <th>Qty</th>
                  <th>Keterangan</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($logs as $lg): ?>
                  <?php $m = $this->Medicine_model->get_by_id($lg['medicine_id']); ?>
                  <tr>
                    <td><?= htmlspecialchars($lg['log_date']) ?></td>
                    <td><?= $m ? htmlspecialchars($m['name']) : ('#'.$lg['medicine_id']) ?></td>
                    <td>
                      <?php if ($lg['type']==='in'): ?>
                        <span class="badge bg-success">Masuk</span>
                      <?php elseif ($lg['type']==='out'): ?>
                        <span class="badge bg-danger">Keluar</span>
                      <?php else: ?>
                        <span class="badge bg-warning text-dark">Opname</span>
                      <?php endif; ?>
                    </td>
                    <td><?= (int)$lg['qty'] ?></td>
                    <td><?= htmlspecialchars($lg['notes'] ?? '') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header bg-warning text-dark">
          <strong>Stok Rendah</strong>
        </div>
        <div class="card-body">
          <?php if (!empty($low_stock)): ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($low_stock as $ls): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <strong><?= htmlspecialchars($ls['name']) ?></strong>
                    <div class="small text-muted"><?= htmlspecialchars($ls['code']) ?> • <?= htmlspecialchars($ls['unit']) ?></div>
                  </div>
                  <span class="badge bg-danger"><?= (int)$ls['current_stock'] ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <div class="alert alert-info mb-0"><i class="bi bi-info-circle"></i> Tidak ada stok rendah</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

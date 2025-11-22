<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Settings</h1>
      <p>Konfigurasi aplikasi, profil pengguna, dan API keys.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= base_url('settings/profile') ?>" class="btn btn-outline-primary"><i class="bi bi-person"></i> Profile</a>
      <a href="<?= base_url('settings/password') ?>" class="btn btn-outline-secondary"><i class="bi bi-key"></i> Ubah Password</a>
      <a href="<?= base_url('settings/api-keys') ?>" class="btn btn-outline-dark"><i class="bi bi-key-fill"></i> API Keys</a>
    </div>
  </div>

  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= $this->session->flashdata('success') ?></div>
  <?php endif; ?>
  <?php if ($this->session->flashdata('info')): ?>
    <div class="alert alert-info"><i class="bi bi-info-circle"></i> <?= $this->session->flashdata('info') ?></div>
  <?php endif; ?>
  <?php if ($this->session->flashdata('danger')): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= $this->session->flashdata('danger') ?></div>
  <?php endif; ?>

  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="text-muted small">Low Stock Threshold</div>
          <div class="h3 mb-0"><?= (int)$low_threshold ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="text-muted small">Item di bawah threshold</div>
          <div class="h3 mb-0"><?= number_format(count($low_stock)) ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>Low Stock Alerts</strong>
      <span class="text-muted small">Di bawah <?= (int)$low_threshold ?> unit</span>
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
              <th class="text-end">Stok Saat Ini</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($low_stock)): ?>
              <?php foreach ($low_stock as $m): ?>
                <tr>
                  <td><?= htmlspecialchars($m['code']) ?></td>
                  <td><?= htmlspecialchars($m['name']) ?></td>
                  <td><?= htmlspecialchars($m['category_name']) ?></td>
                  <td><?= htmlspecialchars($m['unit']) ?></td>
                  <td class="text-end"><span class="badge bg-danger"><?= (int)$m['current_stock'] ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center">Tidak ada item di bawah threshold</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>API Keys</h1>
      <p>Kelola API key untuk integrasi.</p>
    </div>
    <a href="<?= base_url('settings') ?>" class="btn btn-outline-secondary"><i class="bi bi-gear"></i> Settings</a>
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

  <div class="card mb-3">
    <div class="card-header"><strong>Buat API Key Baru</strong></div>
    <div class="card-body">
      <form method="post" class="row g-2 align-items-end">
        <input type="hidden" name="action" value="create" />
        <div class="col-md-4">
          <label class="form-label">Nama</label>
          <input type="text" name="name" class="form-control" placeholder="contoh: Integrasi N8N" required />
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary"><i class="bi bi-plus"></i> Buat</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><strong>Daftar API Keys Aktif</strong></div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama</th>
              <th>API Key</th>
              <th>Dibuat</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($keys)): ?>
              <?php foreach ($keys as $k): ?>
                <tr>
                  <td><?= (int)$k['id'] ?></td>
                  <td><?= htmlspecialchars($k['name']) ?></td>
                  <td><code><?= htmlspecialchars($k['api_key']) ?></code></td>
                  <td><?= htmlspecialchars($k['created_at'] ?? '-') ?></td>
                  <td class="text-end">
                    <form method="post" class="d-inline">
                      <input type="hidden" name="action" value="deactivate" />
                      <input type="hidden" name="id" value="<?= (int)$k['id'] ?>" />
                      <button class="btn btn-warning btn-sm" onclick="return confirm('Nonaktifkan API key ini?')"><i class="bi bi-slash-circle"></i> Nonaktifkan</button>
                    </form>
                    <form method="post" class="d-inline ms-1">
                      <input type="hidden" name="action" value="delete" />
                      <input type="hidden" name="id" value="<?= (int)$k['id'] ?>" />
                      <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus API key ini?')"><i class="bi bi-trash"></i> Hapus</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center">Belum ada API key aktif</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

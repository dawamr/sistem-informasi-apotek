<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Stok Opname</h1>
      <p>Penyesuaian stok agar sesuai dengan jumlah fisik.</p>
    </div>
    <div>
      <a href="<?= base_url('stock') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
  </div>

  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= $this->session->flashdata('success') ?></div>
  <?php elseif ($this->session->flashdata('danger')): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= $this->session->flashdata('danger') ?></div>
  <?php elseif ($this->session->flashdata('info')): ?>
    <div class="alert alert-info"><i class="bi bi-info-circle"></i> <?= $this->session->flashdata('info') ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="post" action="<?= base_url('stock/opname') ?>">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Obat</label>
            <select name="medicine_id" class="form-select" required>
              <option value="">- Pilih Obat -</option>
              <?php foreach ($medicines as $m): ?>
                <option value="<?= (int)$m['id'] ?>">
                  <?= htmlspecialchars($m['name']) ?> (<?= htmlspecialchars($m['code']) ?>) — Stok sistem: <?= (int)$m['current_stock'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Stok Fisik (Real)</label>
            <input type="number" name="real_stock" class="form-control" value="0" min="0" required>
          </div>
          <div class="col-md-12">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Opsional"></textarea>
          </div>
        </div>
        <div class="mt-4">
          <button class="btn btn-warning"><i class="bi bi-clipboard-check"></i> Simpan Opname</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Stok Masuk</h1>
      <p>Input penambahan stok obat.</p>
    </div>
    <div>
      <a href="<?= base_url('stock') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
  </div>

  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= $this->session->flashdata('success') ?></div>
  <?php elseif ($this->session->flashdata('danger')): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= $this->session->flashdata('danger') ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="post" action="<?= base_url('stock/in') ?>">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Obat</label>
            <select name="medicine_id" class="form-select" required>
              <option value="">- Pilih Obat -</option>
              <?php foreach ($medicines as $m): ?>
                <option value="<?= (int)$m['id'] ?>"><?= htmlspecialchars($m['name']) ?> (<?= htmlspecialchars($m['code']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Qty</label>
            <input type="number" name="qty" class="form-control" value="1" min="1" required>
          </div>
          <div class="col-md-12">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Opsional"></textarea>
          </div>
        </div>
        <div class="mt-4">
          <button class="btn btn-success"><i class="bi bi-box-arrow-in-down"></i> Simpan Stok Masuk</button>
        </div>
      </form>
    </div>
  </div>
</div>

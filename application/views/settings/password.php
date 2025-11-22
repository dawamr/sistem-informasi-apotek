<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Ubah Password</h1>
      <p>Ganti password akun Anda.</p>
    </div>
    <a href="<?= base_url('settings') ?>" class="btn btn-outline-secondary"><i class="bi bi-gear"></i> Settings</a>
  </div>

  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= $this->session->flashdata('success') ?></div>
  <?php endif; ?>
  <?php if ($this->session->flashdata('danger')): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= $this->session->flashdata('danger') ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="post" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Password Saat Ini</label>
          <input type="password" name="current_password" class="form-control" placeholder="••••••••" />
          <small class="text-muted">Wajib jika password lama sudah diset.</small>
        </div>
        <div class="col-md-6"></div>
        <div class="col-md-6">
          <label class="form-label">Password Baru</label>
          <input type="password" name="new_password" class="form-control" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Konfirmasi Password Baru</label>
          <input type="password" name="confirm_password" class="form-control" required />
        </div>
        <div class="col-12">
          <button class="btn btn-primary"><i class="bi bi-key"></i> Ubah Password</button>
        </div>
      </form>
    </div>
  </div>
</div>

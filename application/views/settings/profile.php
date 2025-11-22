<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Profile</h1>
      <p>Perbarui informasi profil Anda.</p>
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
          <label class="form-label">Nama</label>
          <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" class="form-control" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Username</label>
          <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" class="form-control" required />
        </div>
        <div class="col-12">
          <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1><?= $shift ? 'Edit Shift' : 'Tambah Shift' ?></h1>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= base_url('shifts/list') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
  </div>

  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= $this->session->flashdata('success') ?></div>
  <?php elseif ($this->session->flashdata('danger')): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= $this->session->flashdata('danger') ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="post">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Tanggal</label>
            <input type="date" name="date" class="form-control" value="<?= $shift ? htmlspecialchars($shift['date']) : '' ?>" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Nama Shift</label>
            <select name="shift_name" class="form-select" required>
              <?php $name = $shift ? $shift['shift_name'] : 'Pagi'; ?>
              <option value="Pagi" <?= $name==='Pagi'?'selected':'' ?>>Pagi</option>
              <option value="Malam" <?= $name==='Malam'?'selected':'' ?>>Malam</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Mulai</label>
            <input type="time" name="start_time" class="form-control" value="<?= $shift ? htmlspecialchars($shift['start_time']) : '08:00' ?>" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Selesai</label>
            <input type="time" name="end_time" class="form-control" value="<?= $shift ? htmlspecialchars($shift['end_time']) : '16:00' ?>" required>
          </div>
        </div>
        <div class="mt-4">
          <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

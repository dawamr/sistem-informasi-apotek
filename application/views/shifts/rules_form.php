<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1><?= $rule ? 'Edit Aturan Shift' : 'Tambah Aturan Shift' ?></h1>
      <p>Aturan digunakan untuk auto-assign user ke shift pada Atur Jadwal Mingguan.</p>
    </div>
    <div>
      <a href="<?= base_url('shifts/rules') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
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
            <label class="form-label">Pengguna</label>
            <select name="user_id" class="form-select" required>
              <option value="">-- Pilih User --</option>
              <?php foreach ($users as $u): ?>
                <option value="<?= (int)$u['id'] ?>" <?= $rule && (int)$rule['user_id']===(int)$u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['username']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Shift</label>
            <?php $sn = $rule ? $rule['shift_name'] : 'Pagi'; ?>
            <select name="shift_name" class="form-select" required>
              <option value="Pagi" <?= $sn==='Pagi'?'selected':'' ?>>Pagi</option>
              <option value="Malam" <?= $sn==='Malam'?'selected':'' ?>>Malam</option>
            </select>
          </div>
          <div class="col-md-5">
            <label class="form-label">Hari dalam Minggu</label>
            <div class="d-flex flex-wrap gap-2">
              <?php
                $labels = [1=>'Sen',2=>'Sel',3=>'Rab',4=>'Kam',5=>'Jum',6=>'Sab',7=>'Min'];
                $selectedDays = $rule ? array_filter(explode(',', (string)$rule['days_of_week'])) : [];
                foreach ($labels as $k=>$v):
                  $checked = in_array((string)$k, $selectedDays, true) ? 'checked' : '';
              ?>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="d<?= $k ?>" name="days[]" value="<?= $k ?>" <?= $checked ?>>
                  <label class="form-check-label" for="d<?= $k ?>"><?= $v ?></label>
                </div>
              <?php endforeach; ?>
              <div class="text-muted small w-100">Kosongkan semua untuk berlaku ke semua hari.</div>
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label">Efektif Mulai</label>
            <input type="date" name="effective_start" class="form-control" value="<?= $rule ? htmlspecialchars($rule['effective_start']) : '' ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Efektif Selesai</label>
            <input type="date" name="effective_end" class="form-control" value="<?= $rule ? htmlspecialchars($rule['effective_end']) : '' ?>">
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="active" id="active" <?= $rule ? ((int)$rule['active']===1?'checked':'') : 'checked' ?>>
              <label class="form-check-label" for="active">Aktif</label>
            </div>
          </div>
        </div>
        <div class="mt-4">
          <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Assign Pengguna ke Shift</h1>
      <p><strong><?= htmlspecialchars($shift['date']) ?></strong> • <?= htmlspecialchars($shift['shift_name']) ?> (<?= htmlspecialchars($shift['start_time']) ?>–<?= htmlspecialchars($shift['end_time']) ?>)</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <form method="get" action="<?= base_url('shifts/assign/'.$shift['id']) ?>" class="d-flex align-items-center gap-2">
        <label class="form-label mb-0">Role</label>
        <select name="role" class="form-select" style="width:140px">
          <option value="" <?= ($filter_role==='')?'selected':'' ?>>Semua</option>
          <option value="admin" <?= ($filter_role==='admin')?'selected':'' ?>>Admin</option>
          <option value="apoteker" <?= ($filter_role==='apoteker')?'selected':'' ?>>Apoteker</option>
        </select>
        <label class="form-label mb-0">Status</label>
        <select name="active" class="form-select" style="width:140px">
          <option value="" <?= ($filter_active==='')?'selected':'' ?>>Semua</option>
          <option value="1" <?= ($filter_active==='1')?'selected':'' ?>>Aktif</option>
          <option value="0" <?= ($filter_active==='0')?'selected':'' ?>>Nonaktif</option>
        </select>
        <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
      </form>
      <a href="<?= base_url('shifts/list?start='.$shift['date'].'&end='.$shift['date']) ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
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
      <form method="post">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th style="width:60px">Pilih</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Peran</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): $checked = in_array((int)$u['id'], $assigned_ids) ? 'checked' : ''; ?>
                <tr>
                  <td>
                    <input type="checkbox" class="form-check-input" name="user_ids[]" value="<?= (int)$u['id'] ?>" <?= $checked ?>>
                  </td>
                  <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                  <td><?= htmlspecialchars($u['username']) ?></td>
                  <td>
                    <?php if ($u['role'] === 'admin'): ?>
                      <span class="badge bg-warning text-dark">Admin</span>
                    <?php else: ?>
                      <span class="badge bg-info text-dark">Apoteker</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ((int)$u['active'] === 1): ?>
                      <span class="badge bg-success">Aktif</span>
                    <?php else: ?>
                      <span class="badge bg-danger">Nonaktif</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="mt-3 d-flex justify-content-between">
          <div class="text-muted small">
            Menyimpan akan mengganti assignment sebelumnya untuk shift ini pada tanggal terkait.
          </div>
          <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan Penugasan</button>
        </div>
      </form>
    </div>
  </div>
</div>

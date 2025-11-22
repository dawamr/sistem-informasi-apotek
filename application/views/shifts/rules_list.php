<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Aturan Penugasan Shift</h1>
      <p>Aturan hierarkis untuk auto-assign user ke shift berdasarkan hari dan rentang efektif.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="<?= base_url('shifts/rules/create') ?>" class="btn btn-success"><i class="bi bi-plus"></i> Tambah Aturan</a>
      <a href="<?= base_url('shifts/rules/export') ?>" class="btn btn-outline-secondary"><i class="bi bi-download"></i> Export CSV</a>
      <a href="<?= base_url('shifts/rules/import') ?>" class="btn btn-outline-secondary"><i class="bi bi-upload"></i> Import CSV</a>
      <a href="<?= base_url('shifts/list') ?>" class="btn btn-outline-primary"><i class="bi bi-list"></i> Daftar Shift</a>
    </div>
  </div>

  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= $this->session->flashdata('success') ?></div>
  <?php elseif ($this->session->flashdata('danger')): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= $this->session->flashdata('danger') ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Pengguna</th>
              <th>Shift</th>
              <th>Hari</th>
              <th>Efektif</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rules as $r): ?>
              <tr>
                <td><?= isset($user_map[(int)$r['user_id']]) ? htmlspecialchars($user_map[(int)$r['user_id']]) : ('#'.(int)$r['user_id']) ?></td>
                <td><?= htmlspecialchars($r['shift_name']) ?></td>
                <td>
                  <?php
                    $labels = array(1=>'Sen',2=>'Sel',3=>'Rab',4=>'Kam',5=>'Jum',6=>'Sab',7=>'Min');
                    $days = array_filter(explode(',', (string)$r['days_of_week']));
                    $out = array(); foreach ($days as $d) { $d=(int)$d; if (isset($labels[$d])) $out[]=$labels[$d]; }
                    echo !empty($out) ? implode(', ', $out) : 'Semua';
                  ?>
                </td>
                <td>
                  <?php if (!empty($r['effective_start']) || !empty($r['effective_end'])): ?>
                    <?= htmlspecialchars($r['effective_start'] ?: '-') ?> s/d <?= htmlspecialchars($r['effective_end'] ?: '-') ?>
                  <?php else: ?>
                    Berlaku terus
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ((int)$r['active'] === 1): ?>
                    <span class="badge bg-success">Aktif</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Nonaktif</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="<?= base_url('shifts/rules/edit/'.$r['id']) ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></a>
                  <a href="<?= base_url('shifts/rules/delete/'.$r['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus aturan ini?')" data-bs-toggle="tooltip" title="Hapus"><i class="bi bi-trash"></i></a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="text-muted small mt-2">
        Catatan: Aturan diterapkan otomatis saat "Atur Jadwal Mingguan" disimpan, untuk shift yang belum berjalan dan bukan hari libur.
      </div>
    </div>
  </div>
</div>

<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Jadwal Shift (Daftar)</h1>
      <p>Daftar shift dengan filter tanggal. Anda juga dapat beralih ke tampilan kalender.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="<?= base_url('shifts/create') ?>" class="btn btn-success" data-bs-toggle="tooltip" title="Tambah shift baru"><i class="bi bi-plus"></i> Tambah Shift</a>
      <a href="<?= base_url('shifts/setup') ?>" class="btn btn-primary" data-bs-toggle="tooltip" title="Atur jadwal mingguan"><i class="bi bi-sliders"></i> Atur Mingguan</a>
      <a href="<?= base_url('shifts') ?>" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Lihat kalender"><i class="bi bi-calendar3"></i> Kalender</a>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <form class="row g-3" method="get" action="<?= base_url('shifts/list') ?>">
        <div class="col-md-4">
          <label class="form-label">Tanggal Mulai</label>
          <input type="date" name="start" class="form-control" value="<?= htmlspecialchars($start) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Tanggal Akhir</label>
          <input type="date" name="end" class="form-control" value="<?= htmlspecialchars($end) ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <button class="btn btn-secondary me-2"><i class="bi bi-funnel"></i> Filter</button>
          <a class="btn btn-outline-secondary" href="<?= base_url('shifts/list') ?>"><i class="bi bi-x-circle"></i> Reset</a>
        </div>
      </form>
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
      <div class="table-responsive">
        <table id="shiftsTable" class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Shift</th>
              <th>Mulai</th>
              <th>Selesai</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td>
                  <?= htmlspecialchars($r['date']) ?>
                  <?php if (!empty($holiday_map) && isset($holiday_map[$r['date']])): ?>
                    <span class="badge bg-warning text-dark ms-2">Libur (<?= htmlspecialchars($holiday_map[$r['date']]) ?>)</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['shift_name']) ?></td>
                <td><?= htmlspecialchars($r['start_time']) ?></td>
                <td><?= htmlspecialchars($r['end_time']) ?></td>
                <td style="width:160px">
                  <a href="<?= base_url('shifts/edit/'.$r['id']) ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></a>
                  <a href="<?= base_url('shifts/assign/'.$r['id']) ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Assign User"><i class="bi bi-people"></i></a>
                  <a href="<?= base_url('shifts/delete/'.$r['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus shift ini?')" data-bs-toggle="tooltip" title="Hapus"><i class="bi bi-trash"></i></a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Absensi Harian</h1>
      <p>Daftar absensi berdasarkan tanggal terpilih, dengan aksi Check-in/Check-out.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <form method="get" action="<?= base_url('attendance') ?>" class="d-flex align-items-center gap-2">
        <label class="form-label mb-0 me-2">Tanggal</label>
        <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" class="form-control" style="width:180px">
        <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
      </form>
      <a href="<?= base_url('attendance/report') ?>" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Lihat laporan"><i class="bi bi-graph-up"></i> Laporan</a>
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
        <table id="attendanceTable" class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Shift</th>
              <th>Waktu Shift</th>
              <th>Status</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><strong><?= htmlspecialchars($r['user_name']) ?></strong></td>
                <td><?= htmlspecialchars($r['shift_name']) ?></td>
                <td><?= htmlspecialchars($r['start_time']) ?>–<?= htmlspecialchars($r['end_time']) ?></td>
                <td>
                  <?php if ($r['status']==='hadir'): ?>
                    <span class="badge bg-success">Hadir</span>
                  <?php elseif ($r['status']==='izin'): ?>
                    <span class="badge bg-warning text-dark">Izin</span>
                  <?php elseif ($r['status']==='sakit'): ?>
                    <span class="badge bg-info text-dark">Sakit</span>
                  <?php else: ?>
                    <span class="badge bg-danger">Alpha</span>
                  <?php endif; ?>
                </td>
                <td><?= $r['checkin_time'] ? htmlspecialchars($r['checkin_time']) : '-' ?></td>
                <td><?= $r['checkout_time'] ? htmlspecialchars($r['checkout_time']) : '-' ?></td>
                <td>
                  <div class="d-flex gap-2">
                    <form method="post" action="<?= base_url('attendance/checkin') ?>" onsubmit="return confirm('Lakukan check-in?')">
                      <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                      <button class="btn btn-sm btn-primary" <?= $r['checkin_time'] ? 'disabled' : '' ?> data-bs-toggle="tooltip" title="Check-in"><i class="bi bi-box-arrow-in-right"></i></button>
                    </form>
                    <form method="post" action="<?= base_url('attendance/checkout') ?>" onsubmit="return confirm('Lakukan check-out?')">
                      <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                      <button class="btn btn-sm btn-secondary" <?= ($r['checkin_time'] && !$r['checkout_time']) ? '' : 'disabled' ?> data-bs-toggle="tooltip" title="Check-out"><i class="bi bi-box-arrow-right"></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

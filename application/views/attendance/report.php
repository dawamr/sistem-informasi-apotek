<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Laporan Absensi</h1>
      <p>Filter laporan berdasarkan rentang tanggal dan status.</p>
    </div>
    <div></div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <form class="row g-3" method="get" action="<?= base_url('attendance/report') ?>">
        <div class="col-md-3">
          <label class="form-label">Tanggal Mulai</label>
          <input type="date" name="start" class="form-control" value="<?= htmlspecialchars($start) ?>">
        </div>
        <div class="col-md=3">
          <label class="form-label">Tanggal Akhir</label>
          <input type="date" name="end" class="form-control" value="<?= htmlspecialchars($end) ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="">Semua</option>
            <option value="hadir" <?= $status==='hadir'?'selected':'' ?>>Hadir</option>
            <option value="izin" <?= $status==='izin'?'selected':'' ?>>Izin</option>
            <option value="sakit" <?= $status==='sakit'?'selected':'' ?>>Sakit</option>
            <option value="alpha" <?= $status==='alpha'?'selected':'' ?>>Alpha</option>
          </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
          <button class="btn btn-secondary me-2"><i class="bi bi-funnel"></i> Filter</button>
          <a class="btn btn-outline-secondary" href="<?= base_url('attendance/report') ?>"><i class="bi bi-x-circle"></i> Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table id="attendanceReportTable" class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Nama</th>
              <th>Shift</th>
              <th>Waktu Shift</th>
              <th>Status</th>
              <th>Check-in</th>
              <th>Check-out</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><?= htmlspecialchars($r['date']) ?></td>
                <td><?= htmlspecialchars($r['user_name']) ?></td>
                <td><?= htmlspecialchars($r['shift_name']) ?></td>
                <td><?= htmlspecialchars($r['start_time']) ?>–<?= htmlspecialchars($r['end_time']) ?></td>
                <td><?= htmlspecialchars($r['status']) ?></td>
                <td><?= $r['checkin_time'] ? htmlspecialchars($r['checkin_time']) : '-' ?></td>
                <td><?= $r['checkout_time'] ? htmlspecialchars($r['checkout_time']) : '-' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

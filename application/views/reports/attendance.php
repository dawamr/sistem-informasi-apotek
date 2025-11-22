<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Laporan Absensi</h1>
      <p>Rekap bulanan, ringkasan per user, dan tren harian hadir/alpha.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <form method="get" action="<?= base_url('reports/attendance') ?>" class="row g-2 align-items-end">
        <div class="col-auto">
          <label class="form-label mb-0">Bulan</label>
          <input type="month" name="month" value="<?= htmlspecialchars($month) ?>" class="form-control" />
        </div>
        <div class="col-auto">
          <label class="form-label mb-0">User</label>
          <select name="user_id" class="form-select">
            <option value="">Semua</option>
            <?php foreach ($users as $u): ?>
              <option value="<?= (int)$u['id'] ?>" <?= (!empty($user_id) && (int)$user_id === (int)$u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-auto">
          <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
        </div>
      </form>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="card"><div class="card-body">
        <div class="text-muted small">Terjadwal</div>
        <div class="h3 mb-0"><?= number_format((int)($totals['total'] ?? 0)) ?></div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card"><div class="card-body">
        <div class="text-muted small">Hadir</div>
        <div class="h3 mb-0 text-success"><?= number_format((int)($totals['present'] ?? 0)) ?></div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card"><div class="card-body">
        <div class="text-muted small">Izin/Sakit</div>
        <div class="h3 mb-0 text-warning"><?= number_format((int)(($totals['permission'] ?? 0) + ($totals['sick'] ?? 0))) ?></div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card"><div class="card-body">
        <div class="text-muted small">Alpha</div>
        <div class="h3 mb-0 text-danger"><?= number_format((int)($totals['absent'] ?? 0)) ?></div>
      </div></div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>Tren Harian (Hadir vs Alpha)</strong>
      <span class="text-muted small">Periode: <?= htmlspecialchars($start) ?> s/d <?= htmlspecialchars($end) ?></span>
    </div>
    <div class="card-body">
      <canvas id="attChart" height="110"></canvas>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><strong>Rekap Per User (<?= htmlspecialchars($month) ?>)</strong></div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Nama</th>
              <th class="text-end">Terjadwal</th>
              <th class="text-end">Hadir</th>
              <th class="text-end">Izin</th>
              <th class="text-end">Sakit</th>
              <th class="text-end">Alpha</th>
              <th class="text-end">Terlambat</th>
              <th class="text-end">Kehadiran (%)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($per_user)): ?>
              <?php foreach ($per_user as $r): 
                $scheduled = (int)$r['scheduled'];
                $present = (int)$r['present'];
                $rate = $scheduled > 0 ? round(($present / $scheduled) * 100, 2) : 0;
              ?>
                <tr>
                  <td><?= htmlspecialchars($r['user_name']) ?></td>
                  <td class="text-end"><?= number_format($scheduled) ?></td>
                  <td class="text-end"><span class="badge bg-success"><?= number_format($present) ?></span></td>
                  <td class="text-end"><span class="badge bg-warning text-dark"><?= number_format((int)$r['permission']) ?></span></td>
                  <td class="text-end"><span class="badge bg-warning text-dark"><?= number_format((int)$r['sick']) ?></span></td>
                  <td class="text-end"><span class="badge bg-danger"><?= number_format((int)$r['absent']) ?></span></td>
                  <td class="text-end"><?= number_format((int)$r['late_count']) ?></td>
                  <td class="text-end"><?= number_format($rate, 2) ?>%</td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="8" class="text-center">Tidak ada data</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js via jsDelivr -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  (function(){
    const ctx = document.getElementById('attChart').getContext('2d');
    const labels = <?= $chart_labels ?>;
    const present = <?= $chart_present ?>;
    const absent = <?= $chart_absent ?>;
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          { label: 'Hadir', data: present, borderColor: 'rgba(25, 135, 84, 1)', backgroundColor: 'rgba(25, 135, 84, .15)', tension: .2, fill: true },
          { label: 'Alpha', data: absent, borderColor: 'rgba(220, 53, 69, 1)', backgroundColor: 'rgba(220, 53, 69, .15)', tension: .2, fill: true }
        ]
      },
      options: { responsive: true, plugins: { legend: { position: 'top' } } }
    });
  })();
</script>

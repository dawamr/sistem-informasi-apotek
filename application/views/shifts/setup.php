<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Atur Jadwal Mingguan</h1>
      <p>Atur jadwal shift untuk 7 hari ke depan. Default: Pagi 08:00–16:00, Malam 16:00–22:00.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= base_url('shifts') ?>" class="btn btn-outline-primary"><i class="bi bi-calendar3"></i> Kalender</a>
      <a href="<?= base_url('shifts/list') ?>" class="btn btn-outline-secondary"><i class="bi bi-list-task"></i> Daftar</a>
    </div>
  </div>

  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= $this->session->flashdata('success') ?></div>
  <?php elseif ($this->session->flashdata('danger')): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= $this->session->flashdata('danger') ?></div>
  <?php elseif ($this->session->flashdata('info')): ?>
    <div class="alert alert-info"><i class="bi bi-info-circle"></i> <?= $this->session->flashdata('info') ?></div>
  <?php endif; ?>

  <div class="card mb-3">
    <div class="card-body">
      <form method="get" action="<?= base_url('shifts/setup') ?>" class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Tanggal Mulai (Senin)</label>
          <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Terapkan Tanggal</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="post" action="<?= base_url('shifts/setup') ?>">
        <input type="hidden" name="start_date" value="<?= htmlspecialchars($start_date) ?>" />

        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th>Hari</th>
                <th style="width:220px">Shift Pagi</th>
                <th style="width:220px">Shift Malam</th>
                <th style="width:260px">Libur/Cuti</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($days as $i => $d): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($d['label']) ?></strong><input type="hidden" name="days[<?= $i ?>][date]" value="<?= htmlspecialchars($d['date']) ?>"></td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="form-check form-switch">
                        <input class="form-check-input shift-toggle" type="checkbox" name="days[<?= $i ?>][morning][enabled]" id="m-<?= $i ?>" <?= !empty($d['morning_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="m-<?= $i ?>">Aktif</label>
                      </div>
                      <input type="time" class="form-control shift-input" name="days[<?= $i ?>][morning][start]" value="<?= htmlspecialchars($d['morning_start']) ?>" style="max-width:110px">
                      <span>–</span>
                      <input type="time" class="form-control shift-input" name="days[<?= $i ?>][morning][end]" value="<?= htmlspecialchars($d['morning_end']) ?>" style="max-width:110px">
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="form-check form-switch">
                        <input class="form-check-input shift-toggle" type="checkbox" name="days[<?= $i ?>][evening][enabled]" id="e-<?= $i ?>" <?= !empty($d['evening_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="e-<?= $i ?>">Aktif</label>
                      </div>
                      <input type="time" class="form-control shift-input" name="days[<?= $i ?>][evening][start]" value="<?= htmlspecialchars($d['evening_start']) ?>" style="max-width:110px">
                      <span>–</span>
                      <input type="time" class="form-control shift-input" name="days[<?= $i ?>][evening][end]" value="<?= htmlspecialchars($d['evening_end']) ?>" style="max-width:110px">
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="form-check form-switch">
                        <input class="form-check-input toggle-dayoff" type="checkbox" id="d-<?= $i ?>" name="days[<?= $i ?>][day_off][enabled]" <?= !empty($d['day_off_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="d-<?= $i ?>">Libur</label>
                      </div>
                      <input type="text" class="form-control dayoff-title" name="days[<?= $i ?>][day_off][title]" placeholder="Judul libur (opsional)" value="<?= htmlspecialchars($d['day_off_title'] ?? '') ?>" style="max-width:160px">
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-3 d-flex justify-content-between">
          <div class="text-muted small">Default saat ini: Pagi <?= htmlspecialchars($defaults['morning_start']) ?>–<?= htmlspecialchars($defaults['morning_end']) ?>, Malam <?= htmlspecialchars($defaults['evening_start']) ?>–<?= htmlspecialchars($defaults['evening_end']) ?>. Dapat diubah per hari di atas.</div>
          <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan Jadwal Mingguan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  (function(){
    function applyRowState(tr){
      var dayoff = tr.querySelector('.toggle-dayoff');
      if (!dayoff) return;
      var off = dayoff.checked;
      tr.querySelectorAll('input[name*="[morning]"], input[name*="[evening]"], .shift-toggle').forEach(function(el){
        // jangan disable checkbox libur dan title
        if (el.classList.contains('toggle-dayoff') || el.classList.contains('dayoff-title')) return;
        el.disabled = off;
      });
    }
    document.addEventListener('DOMContentLoaded', function(){
      document.querySelectorAll('table tr').forEach(function(tr){ applyRowState(tr); });
      document.querySelectorAll('.toggle-dayoff').forEach(function(chk){
        chk.addEventListener('change', function(){ applyRowState(this.closest('tr')); });
      });
    });
  })();
</script>

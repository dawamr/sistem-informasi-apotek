<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>Jadwal Shift (Kalender)</h1>
      <p>Tampilkan jadwal shift dalam tampilan kalender. Gunakan filter untuk memilih bulan.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <form method="get" action="<?= base_url('shifts') ?>" class="d-flex align-items-center gap-2">
        <label class="form-label mb-0 me-2">Bulan</label>
        <input type="month" name="month" value="<?= htmlspecialchars($month) ?>" class="form-control" style="width:180px">
        <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
      </form>
      <a href="<?= base_url('shifts/setup') ?>" class="btn btn-primary" data-bs-toggle="tooltip" title="Atur jadwal mingguan berdasarkan konfigurasi default"><i class="bi bi-sliders"></i> Atur Mingguan</a>
      <a href="<?= base_url('shifts/list') ?>" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Lihat dalam bentuk daftar"><i class="bi bi-list-task"></i> Daftar</a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <!-- FullCalendar container -->
      <div id="calendar"></div>
    </div>
  </div>
</div>

<!-- FullCalendar CSS (jsDelivr) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" />
<!-- FullCalendar JS (jsDelivr) provided via page script as well, but include library here -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

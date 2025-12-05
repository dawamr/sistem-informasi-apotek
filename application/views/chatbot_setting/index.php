<main class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"><i class="bi bi-robot"></i> Chatbot Setting</h2>
                <p class="text-muted mb-0">Kelola akses pengguna chatbot berdasarkan nomor telepon</p>
            </div>
            <a href="<?= base_url('chatbot_setting/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Akses
            </a>
        </div>

        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?= $this->session->flashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('danger')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?= $this->session->flashdata('danger') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('warning')): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> <?= $this->session->flashdata('warning') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Info Card -->
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-info-circle"></i> Informasi</h6>
                <ul class="mb-0 small">
                    <li><strong>Default (*)</strong> - Akses default untuk semua nomor yang tidak terdaftar. Hanya bisa mengakses informasi produk.</li>
                    <li><strong>Lifetime</strong> - Akses selamanya tanpa batas waktu.</li>
                    <li><strong>Part-time</strong> - Akses dengan batas waktu tertentu (tanggal mulai & berakhir).</li>
                </ul>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="chatbot-access-table" class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No. Telepon</th>
                                <th>Tipe Akses</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th>Fitur Diizinkan</th>
                                <th>Catatan</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entries as $entry): ?>
                            <tr>
                                <td>
                                    <?php if ($entry['phone_number'] === '*'): ?>
                                        <span class="badge bg-secondary"><i class="bi bi-asterisk"></i> Default (Semua)</span>
                                    <?php else: ?>
                                        <i class="bi bi-telephone"></i> <?= htmlspecialchars($entry['phone_number']) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($entry['access_type'] === 'lifetime'): ?>
                                        <span class="badge bg-success"><i class="bi bi-infinity"></i> Lifetime</span>
                                    <?php else: ?>
                                        <span class="badge bg-info"><i class="bi bi-clock"></i> Part-time</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($entry['access_type'] === 'parttime' && ($entry['start_date'] || $entry['end_date'])): ?>
                                        <small>
                                            <?= $entry['start_date'] ? date('d/m/Y', strtotime($entry['start_date'])) : '-' ?>
                                            s/d
                                            <?= $entry['end_date'] ? date('d/m/Y', strtotime($entry['end_date'])) : '-' ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($entry['is_allowed']): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-lg"></i> Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="bi bi-x-lg"></i> Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $features = isset($entry['features']) ? $entry['features'] : array();
                                    $active_features = array();
                                    foreach ($features as $key => $val) {
                                        if ($val && isset($available_features[$key])) {
                                            $active_features[] = $available_features[$key];
                                        }
                                    }
                                    ?>
                                    <?php if (!empty($active_features)): ?>
                                        <?php foreach ($active_features as $feat): ?>
                                            <span class="badge bg-light text-dark mb-1"><?= $feat ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?= $entry['notes'] ? htmlspecialchars($entry['notes']) : '-' ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('chatbot_setting/edit/' . $entry['id']) ?>" 
                                           class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?= base_url('chatbot_setting/toggle/' . $entry['id']) ?>" 
                                           class="btn btn-outline-<?= $entry['is_allowed'] ? 'warning' : 'success' ?>"
                                           title="<?= $entry['is_allowed'] ? 'Nonaktifkan' : 'Aktifkan' ?>"
                                           onclick="return confirm('<?= $entry['is_allowed'] ? 'Nonaktifkan' : 'Aktifkan' ?> akses untuk <?= $entry['phone_number'] ?>?')">
                                            <i class="bi bi-<?= $entry['is_allowed'] ? 'pause' : 'play' ?>"></i>
                                        </a>
                                        <?php if ($entry['phone_number'] !== '*'): ?>
                                        <a href="<?= base_url('chatbot_setting/delete/' . $entry['id']) ?>" 
                                           class="btn btn-outline-danger" title="Hapus"
                                           onclick="return confirm('Hapus akses untuk <?= $entry['phone_number'] ?>?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Feature Legend -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-list-check"></i> Daftar Fitur Chatbot</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($available_features as $key => $label): ?>
                    <div class="col-md-4 col-lg-2 mb-2">
                        <span class="badge bg-primary"><?= $label ?></span>
                        <small class="text-muted d-block"><?= $key ?></small>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<main class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="bi bi-<?= $entry ? 'pencil' : 'plus-lg' ?>"></i>
                    <?= $entry ? 'Edit' : 'Tambah' ?> Akses Chatbot
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('chatbot_setting') ?>">Chatbot Setting</a></li>
                        <li class="breadcrumb-item active"><?= $entry ? 'Edit' : 'Tambah' ?></li>
                    </ol>
                </nav>
            </div>
            <a href="<?= base_url('chatbot_setting') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('danger')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?= $this->session->flashdata('danger') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="card">
            <div class="card-body">
                <form method="post" action="">
                    <div class="row">
                        <!-- Phone Number -->
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">
                                <i class="bi bi-telephone"></i> Nomor Telepon <span class="text-danger">*</span>
                            </label>
                            <?php if ($entry && $entry['phone_number'] === '*'): ?>
                                <input type="text" class="form-control" value="* (Default - Semua Nomor)" disabled>
                                <input type="hidden" name="phone_number" value="*">
                                <small class="text-muted">Default akses tidak dapat diubah nomor teleponnya</small>
                            <?php else: ?>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" 
                                       value="<?= $entry ? htmlspecialchars($entry['phone_number']) : '' ?>"
                                       placeholder="Contoh: 6281234567890" required>
                                <small class="text-muted">Format: kode negara + nomor (tanpa + atau spasi)</small>
                            <?php endif; ?>
                        </div>

                        <!-- Access Type -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-clock-history"></i> Tipe Akses <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="access_type" 
                                           id="access_lifetime" value="lifetime"
                                           <?= (!$entry || $entry['access_type'] === 'lifetime') ? 'checked' : '' ?>
                                           onchange="toggleDateFields()">
                                    <label class="form-check-label" for="access_lifetime">
                                        <i class="bi bi-infinity"></i> Lifetime (Selamanya)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="access_type" 
                                           id="access_parttime" value="parttime"
                                           <?= ($entry && $entry['access_type'] === 'parttime') ? 'checked' : '' ?>
                                           onchange="toggleDateFields()">
                                    <label class="form-check-label" for="access_parttime">
                                        <i class="bi bi-calendar-range"></i> Part-time (Waktu Tertentu)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date Range (for parttime) -->
                    <div class="row" id="date-fields" style="<?= ($entry && $entry['access_type'] === 'parttime') ? '' : 'display:none;' ?>">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">
                                <i class="bi bi-calendar-event"></i> Tanggal Mulai
                            </label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                   value="<?= ($entry && $entry['start_date']) ? $entry['start_date'] : '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">
                                <i class="bi bi-calendar-check"></i> Tanggal Berakhir
                            </label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                   value="<?= ($entry && $entry['end_date']) ? $entry['end_date'] : '' ?>">
                        </div>
                    </div>

                    <div class="row">
                        <!-- Status -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-toggle-on"></i> Status
                            </label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_allowed" 
                                           id="status_allowed" value="1"
                                           <?= (!$entry || $entry['is_allowed']) ? 'checked' : '' ?>>
                                    <label class="form-check-label text-success" for="status_allowed">
                                        <i class="bi bi-check-circle"></i> Diizinkan (Allow)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_allowed" 
                                           id="status_disallowed" value="0"
                                           <?= ($entry && !$entry['is_allowed']) ? 'checked' : '' ?>>
                                    <label class="form-check-label text-danger" for="status_disallowed">
                                        <i class="bi bi-x-circle"></i> Diblokir (Disallow)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="col-md-6 mb-3">
                            <label for="notes" class="form-label">
                                <i class="bi bi-card-text"></i> Catatan
                            </label>
                            <input type="text" class="form-control" id="notes" name="notes"
                                   value="<?= $entry ? htmlspecialchars($entry['notes']) : '' ?>"
                                   placeholder="Catatan tambahan (opsional)">
                        </div>
                    </div>

                    <!-- Custom Access / Features -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="bi bi-list-check"></i> Fitur yang Diizinkan
                        </label>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <?php 
                                    $current_features = ($entry && isset($entry['features'])) ? $entry['features'] : array();
                                    foreach ($available_features as $key => $label): 
                                        $is_checked = isset($current_features[$key]) && $current_features[$key];
                                    ?>
                                    <div class="col-md-4 col-lg-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="features[]" value="<?= $key ?>" 
                                                   id="feature_<?= $key ?>"
                                                   <?= $is_checked ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="feature_<?= $key ?>">
                                                <?= $label ?>
                                            </label>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <hr>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllFeatures()">
                                        <i class="bi bi-check-all"></i> Pilih Semua
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllFeatures()">
                                        <i class="bi bi-x-lg"></i> Hapus Semua
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="selectDefaultFeatures()">
                                        <i class="bi bi-star"></i> Default (Hanya Info Produk)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> <?= $entry ? 'Simpan Perubahan' : 'Tambah Akses' ?>
                        </button>
                        <a href="<?= base_url('chatbot_setting') ?>" class="btn btn-secondary">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
function toggleDateFields() {
    const isParttime = document.getElementById('access_parttime').checked;
    document.getElementById('date-fields').style.display = isParttime ? '' : 'none';
}

function selectAllFeatures() {
    document.querySelectorAll('input[name="features[]"]').forEach(cb => cb.checked = true);
}

function clearAllFeatures() {
    document.querySelectorAll('input[name="features[]"]').forEach(cb => cb.checked = false);
}

function selectDefaultFeatures() {
    clearAllFeatures();
    const defaultFeature = document.getElementById('feature_product_info');
    if (defaultFeature) defaultFeature.checked = true;
}
</script>

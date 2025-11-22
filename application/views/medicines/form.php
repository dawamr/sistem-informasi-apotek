<div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1><?= $medicine ? 'Edit Obat' : 'Tambah Obat' ?></h1>
        </div>
        <div>
            <a href="<?= base_url('medicines') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post">
                <div class="row g-3">
                    <?php if (!$medicine): ?>
                    <div class="col-md-4">
                        <label class="form-label">Kode</label>
                        <input type="text" name="code" class="form-control" value="" required>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-8">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" value="<?= $medicine ? htmlspecialchars($medicine['name']) : '' ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-select">
                            <option value="">- Pilih Kategori -</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $medicine && $medicine['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= $cat['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="unit" class="form-control" value="<?= $medicine ? htmlspecialchars($medicine['unit']) : '' ?>" <?= $medicine ? 'readonly' : 'required' ?>>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga</label>
                        <input type="number" name="price" class="form-control" value="<?= $medicine ? (int)$medicine['price'] : '' ?>" required>
                    </div>
                    <?php if (!$medicine): ?>
                    <div class="col-md-4">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" name="current_stock" class="form-control" value="0" required>
                    </div>
                    <?php endif; ?>
                    <?php if ($medicine): ?>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" <?= $medicine['is_active'] ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= !$medicine['is_active'] ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

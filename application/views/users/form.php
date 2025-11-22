<div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1><?= $user ? 'Edit Pengguna' : 'Tambah Pengguna' ?></h1>
        </div>
        <div>
            <a href="<?= base_url('users') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" value="<?= $user ? htmlspecialchars($user['name']) : '' ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= $user ? htmlspecialchars($user['username']) : '' ?>" <?= $user ? 'readonly' : 'required' ?>>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Peran</label>
                        <select name="role" class="form-select" required>
                            <?php $role = $user ? $user['role'] : 'apoteker'; ?>
                            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="apoteker" <?= $role === 'apoteker' ? 'selected' : '' ?>>Apoteker</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <?php $active = $user ? (int)$user['active'] : 1; ?>
                        <select name="active" class="form-select" required>
                            <option value="1" <?= $active === 1 ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= $active === 0 ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password <?= $user ? '(kosongkan jika tidak diubah)' : '' ?></label>
                        <input type="password" name="password" class="form-control" <?= $user ? '' : 'required' ?> autocomplete="new-password">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

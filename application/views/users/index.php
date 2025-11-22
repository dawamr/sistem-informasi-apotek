<div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Manajemen Pengguna</h1>
            <p>Kelola akun pengguna, peran, dan status aktif.</p>
        </div>
        <div>
            <a href="<?= base_url('users/create') ?>" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Pengguna</a>
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
                <table id="usersTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Peran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                                <td><?= htmlspecialchars($u['username']) ?></td>
                                <td>
                                    <?php if ($u['role'] === 'admin'): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-shield-lock"></i> Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark"><i class="bi bi-capsule-pill"></i> Apoteker</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ((int)$u['active'] === 1): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('users/edit/'.$u['id']) ?>" class="btn btn-sm btn-info"><i class="bi bi-pencil"></i></a>
                                    <?php if ((int)$u['active'] === 1): ?>
                                        <a href="<?= base_url('users/deactivate/'.$u['id']) ?>" class="btn btn-sm btn-warning" onclick="return confirm('Nonaktifkan pengguna ini?')"><i class="bi bi-slash-circle"></i></a>
                                    <?php else: ?>
                                        <a href="<?= base_url('users/activate/'.$u['id']) ?>" class="btn btn-sm btn-success" onclick="return confirm('Aktifkan kembali pengguna ini?')"><i class="bi bi-check-circle"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    (function(){
        if (window.jQuery && $.fn.DataTable) {
            $('#usersTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                order: [[0, 'asc']]
            });
        }
    })();
</script>

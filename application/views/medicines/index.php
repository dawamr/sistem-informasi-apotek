<div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Manajemen Obat</h1>
            <p>Daftar obat dengan filter kategori dan indikator stok.</p>
        </div>
        <div>
            <a href="<?= base_url('medicines/create') ?>" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Obat</a>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= $this->session->flashdata('success') ?></div>
    <?php elseif ($this->session->flashdata('danger')): ?>
        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= $this->session->flashdata('danger') ?></div>
    <?php elseif ($this->session->flashdata('info')): ?>
        <div class="alert alert-info"><i class="bi bi-info-circle"></i> <?= $this->session->flashdata('info') ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3" method="get" action="<?= base_url('medicines') ?>">
                <div class="col-md-4">
                    <label class="form-label">Filter Kategori</label>
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($selected_category == $cat['id']) ? 'selected' : '' ?>>
                                <?= $cat['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="medicinesTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medicines as $m): ?>
                            <tr>
                                <td><strong><?= $m['code'] ?></strong></td>
                                <td><?= $m['name'] ?></td>
                                <td><?= isset($m['category_name']) ? $m['category_name'] : '-' ?></td>
                                <td><?= $m['unit'] ?></td>
                                <td>Rp <?= number_format($m['price'], 0, ',', '.') ?></td>
                                <td>
                                    <?php if ((int)$m['current_stock'] < 50): ?>
                                        <span class="badge bg-danger"><?= (int)$m['current_stock'] ?> (rendah)</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= (int)$m['current_stock'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('medicines/edit/' . $m['id']) ?>" class="btn btn-sm btn-info"><i class="bi bi-pencil"></i></a>
                                    <a href="<?= base_url('medicines/delete/' . $m['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus obat ini?')"><i class="bi bi-trash"></i></a>
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
            $('#medicinesTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                order: [[1, 'asc']]
            });
        }
    })();
</script>

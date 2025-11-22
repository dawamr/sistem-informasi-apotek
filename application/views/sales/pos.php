<div class="main-content">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div>
      <h1>POS Penjualan</h1>
      <p>Cari obat, tambahkan ke keranjang, lalu checkout.</p>
    </div>
    <div>
      <button class="btn btn-success" id="btnCheckout" data-bs-toggle="tooltip" title="Simpan transaksi"><i class="bi bi-cash-stack"></i> Checkout</button>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-3 align-items-end">
        <div class="col-md-6">
          <label class="form-label">Cari Obat</label>
          <input type="text" id="searchInput" class="form-control" placeholder="Ketik nama/kode obat..." autocomplete="off">
          <div id="searchResults" class="list-group mt-2" style="max-height:220px; overflow:auto;"></div>
        </div>
        <div class="col-md-3">
          <label class="form-label">Qty</label>
          <input type="number" id="qtyInput" class="form-control" value="1" min="1">
        </div>
        <div class="col-md-3">
          <button class="btn btn-primary w-100" id="btnAddManual"><i class="bi bi-plus"></i> Tambah</button>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header bg-light">
      <strong>Keranjang</strong>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped" id="cartTable">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama</th>
              <th>Harga</th>
              <th>Qty</th>
              <th>Satuan</th>
              <th>Subtotal</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php $total=0; $items=0; foreach (($cart??[]) as $it): $sub= (int)$it['price']*(int)$it['qty']; $total+=$sub; $items+=(int)$it['qty']; ?>
              <tr data-id="<?= (int)$it['id'] ?>">
                <td><?= htmlspecialchars($it['code']) ?></td>
                <td><?= htmlspecialchars($it['name']) ?></td>
                <td>Rp <?= number_format((int)$it['price'],0,',','.') ?></td>
                <td style="width:120px">
                  <input type="number" class="form-control form-control-sm input-qty" value="<?= (int)$it['qty'] ?>" min="1">
                </td>
                <td><?= htmlspecialchars($it['unit']) ?></td>
                <td class="cell-subtotal">Rp <?= number_format($sub,0,',','.') ?></td>
                <td>
                  <button class="btn btn-sm btn-danger btn-remove" data-bs-toggle="tooltip" title="Hapus"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-end mt-3">
        <div class="text-end">
          <div>Total Item: <strong id="totalItems"><?= (int)($items??0) ?></strong></div>
          <div>Total Bayar: <strong id="totalAmount">Rp <?= number_format((int)($total??0),0,',','.') ?></strong></div>
        </div>
      </div>
    </div>
  </div>
</div>

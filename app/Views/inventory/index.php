          <?= $this->extend('layouts/main') ?>

          <?= $this->section('content') ?>

          <div class="container-xxl flex-grow-1 container-p-y">
            
            <div class="row">
              <div class="col-lg-12">

                <div class="card">

                  <div class="card-body">

                    <!-- HEADER -->
                    <div class="row mb-4">
                      <div class="col-md-6">
                        <h4 class="mb-2">Form Pengajuan Barang</h4>
                        <p class="mb-0"><?= session('branch_name') ?></p>
                        <small><?= session('branch_address') ?></small>
                      </div>

                      <div class="col-md-6 text-end">
                        <input type="date" id="tanggal" class="form-control w-auto d-inline" value="<?= date('Y-m-d') ?>">
                      </div>
                    </div>

                    <hr>

                    <!-- INFO PEMOHON -->
                    <div class="row mb-4">

                      <div class="col-md-4">
                        <label>Nama</label>
                        <input type="text" id="nama" class="form-control" placeholder="Nama Pemohon">
                      </div>

                      <div class="col-md-4">
                        <label>Divisi</label>
                        <input type="text" id="divisi" class="form-control" placeholder="Divisi">
                      </div>

                      <div class="col-md-4">
                        <label>Jabatan</label>
                        <input type="text" id="jabatan" class="form-control" placeholder="Jabatan">
                      </div>

                    </div>

                    <hr>

                    <!-- ITEM LIST -->
                    <form id="formPengajuan" class="source-item">

                      <div data-repeater-list="items">

                        <div data-repeater-item class="repeater-wrapper mb-3">

                          <div class="row border rounded p-3">

                            <div class="col-md-3">
                              <label>Item (Vendor)</label>
                              <select class="form-select vendor-item">
                                <option value="">Pilih Item</option>
                              </select>
                            </div>

                            <div class="col-md-2">
                              <label>Sparepart</label>
                              <input type="text" name="sparepart" class="form-control">
                            </div>

                            <div class="col-md-1">
                              <label>Qty</label>
                              <input type="number" name="qty" class="form-control" value="1">
                            </div>

                            <div class="col-md-2">
                              <label>Harga</label>
                              <input type="number" name="harga" class="form-control">
                            </div>

                            <div class="col-md-2">
                              <label>Kondisi</label>
                              <input type="text" name="kondisi" class="form-control">
                            </div>

                            <div class="col-md-1">
                              <label>Bon</label>
                              <select name="is_bon" class="form-select">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                              </select>
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                              <button data-repeater-delete type="button" class="btn btn-danger">
                                <i class="ti ti-trash"></i>
                              </button>
                            </div>

                          </div>

                        </div>

                      </div>

                      <button type="button" data-repeater-create class="btn btn-primary mt-3">
                        <i class="ti ti-plus"></i> Tambah Item
                      </button>

                    </form>

                    <hr>

                    <!-- SUBMIT -->
                    <button id="btnSubmit" class="btn btn-success w-100 mt-3">
                      Submit Pengajuan
                    </button>

                  </div>

                </div>

              </div>
            </div>

          </div>

          <?= $this->endSection() ?>

          <?= $this->section('scripts') ?>
              <!-- Vendors JS -->
              <script src="<?= base_url('assets/vendor/libs/flatpickr/flatpickr.js') ?>"></script>
              <script src="<?= base_url('assets/vendor/libs/cleavejs/cleave.js') ?>"></script>
              <script src="<?= base_url('assets/vendor/libs/cleavejs/cleave-phone.js') ?>"></script>
              <script src="<?= base_url('assets/vendor/libs/jquery-repeater/jquery-repeater.js') ?>"></script>
              <script>
              $('#btnSubmit').on('click', function () {

                let items = [];

                $('[data-repeater-item]').each(function () {

                  let row = $(this);

                  items.push({
                    vendor_item_id: row.find('.vendor-item').val(),
                    sparepart: row.find('[name="sparepart"]').val(),
                    qty: row.find('[name="qty"]').val(),
                    harga: row.find('[name="harga"]').val(),
                    kondisi: row.find('[name="kondisi"]').val(),
                    is_bon: row.find('[name="is_bon"]').val()
                  });

                });

                let payload = {
                  nama: $('#nama').val(),
                  divisi: $('#divisi').val(),
                  jabatan: $('#jabatan').val(),
                  tanggal: $('#tanggal').val(),
                  items: items
                };

                $.ajax({
                  url: '/api/pengajuan',
                  type: 'POST',
                  headers: {
                    Authorization: 'Bearer ' + window.jwtToken
                  },
                  contentType: 'application/json',
                  data: JSON.stringify(payload),
                  success: function (res) {

                    if (res.status) {
                      Swal.fire('Success', 'Pengajuan berhasil disimpan', 'success');
                      location.reload();
                    } else {
                      Swal.fire('Error', res.message, 'error');
                    }

                  }
                });

              });
              
              $(document).on('change', '.vendor-item', function () {

                let selected = $(this).find(':selected');

                let harga = selected.data('harga');
                let sparepart = selected.text();

                let row = $(this).closest('[data-repeater-item]');

                row.find('[name="harga"]').val(harga);
                row.find('[name="sparepart"]').val(sparepart);

              });
              </script>
          <?= $this->endSection() ?>
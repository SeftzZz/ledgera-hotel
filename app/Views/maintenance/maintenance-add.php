          <?= $this->extend('layouts/main') ?>
          <?= $this->section('content') ?>

          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-body">

                    <!-- HEADER -->
                    <div class="row mb-4">
                      <div class="col-md-6">
                        <h4>Form Preventive & Maintenance</h4>
                      </div>
                    </div>

                    <!-- FORM -->
                    <form id="formMaintenance" class="source-item">

                      <!-- =======================
                      HEADER
                      ======================= -->
                      <div class="row mb-4">

                        <div class="col-md-3">
                          <label>Nama</label>
                          <input type="text" name="nama" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                          <label>Divisi</label>
                          <input type="text" name="divisi" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                          <label>Jabatan</label>
                          <input type="text" name="jabatan" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                          <label>Tanggal</label>
                          <input type="date" name="tanggal" class="form-control" required>
                        </div>

                      </div>

                      <hr>

                      <!-- =======================
                      DETAIL ITEM
                      ======================= -->
                      <div data-repeater-list="group-a">

                        <div data-repeater-item class="mb-3 repeater-wrapper">
                          <div class="row border rounded p-3">

                            <!-- ITEM -->
                            <div class="col-md-3">
                              <label>Item</label>
                              <select name="vendor_item_id" class="form-select item-details" required>
                                <option value="">Pilih Item</option>
                                <?php foreach($vendor_items as $item): ?>
                                  <option value="<?= $item['id'] ?>">
                                    <?= $item['sparepart'] ?> (<?= $item['harga'] ?>)
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>

                            <!-- SPAREPART -->
                            <div class="col-md-2">
                              <label>Sparepart</label>
                              <input type="text" name="sparepart" class="form-control">
                            </div>

                            <!-- QTY -->
                            <div class="col-md-1">
                              <label>Qty</label>
                              <input type="text" class="form-control invoice-item-qty" placeholder="0">
                            </div>

                            <!-- HARGA -->
                            <div class="col-md-2">
                              <label>Harga</label>
                              <input type="text" class="form-control invoice-item-price" placeholder="0">
                            </div>

                            <!-- KONDISI -->
                            <div class="col-md-2">
                              <label>Kondisi</label>
                              <select name="kondisi" class="form-select">
                                <option value="">-</option>
                                <option value="Baru">Baru</option>
                                <option value="Bekas">Bekas</option>
                              </select>
                            </div>

                            <!-- DELETE -->
                            <div class="col-md-2 d-flex align-items-end">
                              <button type="button" data-repeater-delete class="btn btn-danger w-100">
                                Hapus
                              </button>
                            </div>

                          </div>
                        </div>

                      </div>

                      <!-- ADD ITEM -->
                      <button type="button" data-repeater-create class="btn btn-primary mt-3">
                        + Tambah Item
                      </button>

                      <hr>

                      <!-- SUBMIT -->
                      <button type="button" id="btnSubmit" class="btn btn-success w-100 mt-3">
                        Submit Maintenance
                      </button>

                    </form>

                  </div>
                </div>
              </div>
            </div>
          </div>

          <?= $this->endSection() ?>

          <?= $this->section('scripts') ?>

          <script src="<?= base_url('assets/vendor/libs/flatpickr/flatpickr.js') ?>"></script>
          <script src="<?= base_url('assets/vendor/libs/cleavejs/cleave.js') ?>"></script>
          <script src="<?= base_url('assets/vendor/libs/cleavejs/cleave-phone.js') ?>"></script>
          <script src="<?= base_url('assets/vendor/libs/jquery-repeater/jquery-repeater.js') ?>"></script>
          <script src="<?= base_url('assets/js/app-maintenance-add.js') ?>"></script>

          <?= $this->endSection() ?>
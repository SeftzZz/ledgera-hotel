          <?= $this->extend('layouts/main') ?>

          <?= $this->section('content') ?>

          <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Order List Widget -->

            <div class="card mb-4">
              <div class="card-widget-separator-wrapper">
                <div class="card-body card-widget-separator">
                  <div class="row gy-4 gy-sm-1">

                    <!-- PENGAJUAN -->
                    <div class="col-sm-6 col-lg-2">
                      <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                        <div>
                          <h4 class="mb-2" id="pengajuan_pending">0</h4>
                          <p class="mb-0 fw-medium">Pengajuan</p>
                        </div>
                        <span class="avatar me-sm-4">
                          <span class="avatar-initial bg-label-warning rounded">
                            <i class="ti-md ti ti-file text-body"></i>
                          </span>
                        </span>
                      </div>
                    </div>

                    <!-- SELESAI -->
                    <div class="col-sm-6 col-lg-2">
                      <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                        <div>
                          <h4 class="mb-2" id="pengajuan_selesai">0</h4>
                          <p class="mb-0 fw-medium">Selesai</p>
                        </div>
                        <span class="avatar p-2 me-lg-4">
                          <span class="avatar-initial bg-label-success rounded">
                            <i class="ti-md ti ti-check text-body"></i>
                          </span>
                        </span>
                      </div>
                    </div>

                    <!-- PROSES -->
                    <div class="col-sm-6 col-lg-2">
                      <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                        <div>
                          <h4 class="mb-2" id="pengajuan_proses">0</h4>
                          <p class="mb-0 fw-medium">Proses</p>
                        </div>
                        <span class="avatar p-2 me-lg-4">
                          <span class="avatar-initial bg-label-info rounded">
                            <i class="ti-md ti ti-loader text-body"></i>
                          </span>
                        </span>
                      </div>
                    </div>

                    <!-- TOTAL -->
                    <div class="col-sm-6 col-lg-2">
                      <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                        <div>
                          <h4 class="mb-2" id="pengajuan_total">0</h4>
                          <p class="mb-0 fw-medium">Total</p>
                        </div>
                        <span class="avatar p-2 me-sm-4">
                          <span class="avatar-initial bg-label-primary rounded">
                            <i class="ti-md ti ti-database text-body"></i>
                          </span>
                        </span>
                      </div>
                    </div>

                    <!-- HARI INI -->
                    <div class="col-sm-6 col-lg-2">
                      <div class="d-flex justify-content-between align-items-start">
                        <div>
                          <h4 class="mb-2" id="pengajuan_today">0</h4>
                          <p class="mb-0 fw-medium">Hari Ini</p>
                        </div>
                        <span class="avatar p-2">
                          <span class="avatar-initial bg-label-danger rounded">
                            <i class="ti-md ti ti-calendar text-body"></i>
                          </span>
                        </span>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>

            <!-- Order List Table -->
            <div class="card">
              <div class="card-datatable table-responsive">
                <table class="datatables-pengajuan table border-top">
                  <thead>
                    <tr>
                      <th></th>
                      <th></th>
                      <th>No Pengajuan</th>
                      <th>Tanggal</th>
                      <th>Pemohon</th>
                      <th>Divisi</th>
                      <th>Status</th>
                      <th>Jabatan</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>

            <!-- MODAL PENGAJUAN -->
            <div class="modal fade" id="modalPengajuan" tabindex="-1">
              <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">

                  <div class="modal-header">
                    <h5 class="modal-title">Form Pengajuan Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>

                  <div class="modal-body">

                    <!-- HEADER -->
                    <div class="row mb-4">
                      <div class="col-md-6">
                        <p class="mb-0 fw-bold"><?= session('branch_name') ?></p>
                        <small><?= session('branch_address') ?></small>
                      </div>

                      <div class="col-md-6 text-end">
                        <!-- 🔥 tambahin name -->
                        <input type="date" name="tanggal" id="tanggal" 
                          class="form-control w-auto d-inline" 
                          value="<?= date('Y-m-d') ?>">
                      </div>
                    </div>

                    <hr>

                    <!-- INFO PEMOHON -->
                    <div class="row mb-4">

                      <div class="col-md-4">
                        <label>Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama Pemohon">
                      </div>

                      <div class="col-md-4">
                        <label>Divisi</label>
                        <input type="text" name="divisi" id="divisi" class="form-control" placeholder="Divisi">
                      </div>

                      <div class="col-md-4">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" id="jabatan" class="form-control" placeholder="Jabatan">
                      </div>

                    </div>

                    <hr>

                    <!-- ITEM LIST -->
                    <form id="formPengajuan" class="source-item pt-4 px-0 px-sm-4">

                      <!-- 🔥 WAJIB: list -->
                      <div class="mb-3" data-repeater-list="items">

                        <!-- 🔥 WAJIB: item -->
                        <div data-repeater-item>

                          <div class="repeater-wrapper pt-0 pt-md-4">
                            <div class="d-flex border rounded position-relative pe-0">

                              <div class="row w-100 p-3">

                                <!-- ITEM -->
                                <div class="col-md-4 col-12 mb-md-0 mb-3">
                                  <p class="mb-2 repeater-title">Nama Barang</p>
                                  <select name="vendor_item_id" class="form-select vendor-item">
                                    <option value="">Pilih Item</option>
                                  </select>
                                </div>

                                <!-- QTY -->
                                <div class="col-md-4 col-12 mb-md-0 mb-3">
                                  <p class="mb-2 repeater-title">Qty</p>
                                  <input type="number" name="qty" class="form-control" placeholder="0" value="1" min="1">
                                </div>

                                <!-- DELETE -->
                                <div class="col-md-4 col-12 d-flex align-items-end">
                                  <button type="button" data-repeater-delete class="btn btn-danger w-100">
                                    <i class="ti ti-trash"></i>
                                  </button>
                                </div>

                              </div>

                            </div>
                          </div>

                        </div>

                      </div>

                      <!-- ADD BUTTON -->
                      <div class="row pb-4">
                        <div class="col-12">
                          <button type="button" data-repeater-create class="btn btn-primary">
                            <i class="ti ti-plus"></i> Tambah Item
                          </button>
                        </div>
                      </div>

                    </form>

                  </div>

                  <div class="modal-footer">
                    <button class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="btnSubmit" class="btn btn-success">
                      Submit Pengajuan
                    </button>
                  </div>

                </div>
              </div>
            </div>

            <!-- MODAL DETAIL PENGAJUAN -->
            <div class="modal fade" id="modalDetailPengajuan" tabindex="-1">
              <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">

                  <div class="modal-header">
                    <h5 class="modal-title">Detail Pengajuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>

                  <div class="modal-body">

                    <!-- HEADER -->
                    <div class="row mb-4">
                      <div class="col-md-6">
                        <p class="mb-0 fw-bold"><?= session('branch_name') ?></p>
                        <small><?= session('branch_address') ?></small>
                      </div>

                      <div class="col-md-6 text-end">
                        <input type="text" id="detail_tanggal" class="form-control w-auto d-inline" readonly>
                      </div>
                    </div>

                    <hr>

                    <!-- INFO PEMOHON -->
                    <div class="row mb-4">

                      <div class="col-md-4">
                        <label>Nama</label>
                        <input type="text" id="detail_nama" class="form-control" readonly>
                      </div>

                      <div class="col-md-4">
                        <label>Divisi</label>
                        <input type="text" id="detail_divisi" class="form-control" readonly>
                      </div>

                      <div class="col-md-4">
                        <label>Jabatan</label>
                        <input type="text" id="detail_jabatan" class="form-control" readonly>
                      </div>

                    </div>

                    <hr>

                    <!-- ITEM LIST -->
                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>Nama Barang</th>
                            <th>Vendor</th>
                            <th>Qty</th>
                            <th>Harga</th>
                          </tr>
                        </thead>
                        <tbody id="detail_items"></tbody>
                      </table>
                    </div>

                  </div>

                  <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
              <script src="<?= base_url('assets/js/app-pengajuan-add.js') ?>"></script>
              <script src="<?= base_url('assets/js/app-ecommerce-pengajuan-list.js') ?>"></script>
          <?= $this->endSection() ?>
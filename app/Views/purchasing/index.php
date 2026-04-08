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
                          <h4 class="mb-2" id="purchasing_pending">0</h4>
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
                          <h4 class="mb-2" id="purchasing_selesai">0</h4>
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
                          <h4 class="mb-2" id="purchasing_proses">0</h4>
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
                          <h4 class="mb-2" id="purchasing_total">0</h4>
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
                          <h4 class="mb-2" id="purchasing_today">0</h4>
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
                <table class="datatables-purchasing table border-top">
                  <thead>
                    <tr>
                      <th>No Pengajuan</th>
                      <th>Tanggal</th>
                      <th>Pemohon</th>
                      <th>Total Item</th>
                      <th>Total Harga</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>

            <div class="modal fade" id="modalDetailPO" tabindex="-1">
              <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">

                  <div class="modal-header">
                    <h5 class="modal-title">Form Purchasing (PO)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>

                  <div class="modal-body">

                    <form id="formPO">

                      <input type="hidden" name="pengajuan_id" id="po_pengajuan_id">

                      <div class="row">

                        <!-- ================= HEADER ================= -->
                        <div class="col-md-6">
                          <table class="table">
                            <tr>
                              <td>Nama</td>
                              <td><input type="text" id="po_nama" class="form-control"></td>
                            </tr>
                            <tr>
                              <td>Jabatan</td>
                              <td><input type="text" id="po_jabatan" class="form-control"></td>
                            </tr>
                            <tr>
                              <td>Divisi</td>
                              <td><input type="text" id="po_divisi" class="form-control"></td>
                            </tr>
                            <tr>
                              <td>Tanggal</td>
                              <td>
                                <input type="date" id="po_tanggal" class="form-control">
                              </td>
                            </tr>
                          </table>
                        </div>

                        <!-- ================= VENDOR ================= -->
                        <div class="col-md-6">
                          <table class="table table-bordered" id="vendorTable">
                            <thead>
                              <tr>
                                <th>No</th>
                                <th>Vendor</th>
                                <th>Bon</th>
                                <th>No PO</th>
                              </tr>
                            </thead>
                            <tbody></tbody>
                          </table>
                        </div>

                      </div>

                      <!-- ================= ITEMS ================= -->
                      <table class="table table-bordered" id="po_items_table">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Vendor</th>
                            <th>Pilih</th>
                            <th>Harga</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                      </table>

                      <!-- ================= TOTAL ================= -->
                      <div class="text-end mt-3">
                        <strong>Total: </strong>
                        <strong id="po_grand_total">Rp 0</strong>
                      </div>

                    </form>

                  </div>

                  <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" id="btnSavePO" class="btn btn-primary">
                      Simpan PO
                    </button>
                  </div>

                </div>
              </div>
            </div>

            <div class="modal fade" id="modalVendor" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">

                  <div class="modal-header">
                    <h5 class="modal-title">Pilih Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>

                  <div class="modal-body">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Vendor</th>
                          <th>Harga</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="vendorOptions"></tbody>
                    </table>
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
              <script src="<?= base_url('assets/js/app-purchasing-add.js') ?>"></script>
          <?= $this->endSection() ?>
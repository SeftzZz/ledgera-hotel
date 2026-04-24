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

                      <?php 
                          $departments = $departmentSummary ?? [];
                          $categoryId  = session('category_id');

                          $dept = null;

                          foreach ($departments as $d) {
                              if ((int)$d['id'] === (int)$categoryId) {
                                  $dept = $d;
                                  break;
                              }
                          }

                          // fallback kalau gak ketemu
                          if (!$dept && !empty($departments)) {
                              $dept = $departments[0];
                          }

                          // ======================
                          // DEFAULT SAFE
                          // ======================
                          $limit = 0;
                          $estimatedLimit = 0;
                          $estimatedPercent = 0;
                          $actual = 0;
                          $actualPercent = 0;

                          $isOver = false;
                          $isBlocked = true; // default block

                          if ($dept) {

                              // LIMIT (target)
                              $limit = ($dept['target'] * $dept['spend_ratio'] / 100);

                              // ESTIMATED LIMIT (INI YANG DIPAKAI)
                              $estimatedLimit = ($dept['estimated'] * $dept['spend_ratio'] / 100);

                              $estimatedPercent = $limit > 0 
                                  ? ($estimatedLimit / $limit) * 100 
                                  : 0;

                              // ACTUAL
                              $actual = $dept['expense'] ?? 0;

                              $actualPercent = $estimatedLimit > 0
                                  ? ($actual / $estimatedLimit) * 100
                                  : 0;

                              // ======================
                              // FINAL RULE
                              // ======================
                              $totalPO = 0; // default saat load awal (belum ada PO)

                              $isOver = ($actual + $totalPO) > $estimatedLimit;

                              $isBlocked = ($estimatedLimit <= 0);
                          }
                      ?>

                      <!-- ======================
                           SPEND LIMIT (ESTIMATED)
                      ====================== -->
                      <small>
                          Spend Limit (Est) <?= number_format($estimatedPercent,2) ?>%
                      </small>

                      <div class="progress" style="height:6px;">
                          <div class="progress-bar bg-warning"
                               style="width: <?= min($estimatedPercent,100) ?>%">
                          </div>
                      </div>

                      <div class="fs-6 pt-1 pb-1" id="estimatedLimit">
                          Rp <?= number_format($estimatedLimit,0,',','.') ?>
                      </div>

                      <?php if ($isOver): ?>
                          <div class="alert alert-danger">
                              Actual spend melebihi estimated limit!
                          </div>
                      <?php endif; ?>

                      <?php if ($estimatedLimit <= 0): ?>
                          <div class="alert alert-warning">
                              Estimated revenue belum tersedia!
                          </div>
                      <?php endif; ?>
                      <!-- ================= FORM ================= -->
                      <form id="formPO">

                        <input type="hidden" name="pengajuan_id" id="po_pengajuan_id">

                        <div class="row">

                          <!-- ================= HEADER ================= -->
                          <div class="col-md-6">
                            <table class="table table-bordered">
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
                              <tr>
                                <td>DP</td>
                                <td>
                                  <input type="number" id="po_dp" class="form-control">
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
                        <table class="table table-bordered mt-3" id="po_items_table">
                          <thead>
                            <tr>
                              <th>No</th>
                              <th>Barang</th>
                              <th>Qty</th>
                              <th>Satuan</th>
                              <th>Purpose</th>
                              <th>Vendor</th>
                              <th>Pilih</th>
                              <th>Harga</th>
                              <th>Sub total</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>

                        <!-- ================= TOTAL ================= -->
                        <div class="text-end mt-3">
                          <strong>GRAND TOTAL: </strong>
                          <strong id="po_grand_total">Rp 0</strong>
                        </div>

                      </form>

                  </div>

                  <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>

                      <button type="button" 
                              id="btnSavePO" 
                              class="btn btn-primary"
                              <?= $isBlocked ? 'disabled style="pointer-events:none;opacity:0.6;"' : '' ?>>
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
              <script>
                function checkLimit() {
                  const estimatedLimit = <?= $estimatedLimit ?>;
                  const actual = <?= $actual ?>;

                  const totalPO = window.totalPayment || 0;

                  const isOver = (actual + totalPO) > estimatedLimit;

                  return isOver;
                }
              </script>
              <script src="<?= base_url('assets/js/app-purchasing-add.js') ?>"></script>
              <script>
                window.dashboardData = {

                  // ======================
                  // DEPARTMENT
                  // ======================
                  departmentSummary: <?= json_encode($departmentSummary, JSON_NUMERIC_CHECK) ?>

                };

                console.log(window.dashboardData.departmentSummary[0]);
              </script>
          <?= $this->endSection() ?>
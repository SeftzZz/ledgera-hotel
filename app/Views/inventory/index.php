          <?= $this->extend('layouts/main') ?>

          <?= $this->section('content') ?>

          <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Order List Widget -->

            <div class="card mb-4">
              <div class="card-widget-separator-wrapper">
                <div class="card-body card-widget-separator">
                  <div class="row gy-4 gy-sm-1">

                    <!-- TOTAL ITEM -->
                    <div class="col-sm-6 col-lg-2">
                      <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0">
                        <div>
                          <h4 class="mb-2" id="inventory_total">0</h4>
                          <p class="mb-0 fw-medium">Total Items</p>
                        </div>
                        <span class="avatar me-sm-4">
                          <span class="avatar-initial bg-label-primary rounded">
                            <i class="ti-md ti ti-database text-body"></i>
                          </span>
                        </span>
                      </div>
                    </div>

                    <!-- STOK TERSEDIA -->
                    <div class="col-sm-6 col-lg-2">
                      <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0">
                        <div>
                          <h4 class="mb-2" id="inventory_available">0</h4>
                          <p class="mb-0 fw-medium">Stok Tersedia</p>
                        </div>
                        <span class="avatar p-2 me-lg-4">
                          <span class="avatar-initial bg-label-success rounded">
                            <i class="ti-md ti ti-check text-body"></i>
                          </span>
                        </span>
                      </div>
                    </div>

                    <!-- STOK HABIS -->
                    <div class="col-sm-6 col-lg-2">
                      <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0">
                        <div>
                          <h4 class="mb-2" id="inventory_empty">0</h4>
                          <p class="mb-0 fw-medium">Stok Habis</p>
                        </div>
                        <span class="avatar p-2 me-lg-4">
                          <span class="avatar-initial bg-label-danger rounded">
                            <i class="ti-md ti ti-alert-triangle text-body"></i>
                          </span>
                        </span>
                      </div>
                    </div>

                    <!-- STOK RENDAH -->
                    <div class="col-sm-6 col-lg-2">
                      <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0">
                        <div>
                          <h4 class="mb-2" id="inventory_low">0</h4>
                          <p class="mb-0 fw-medium">Stok Rendah</p>
                        </div>
                        <span class="avatar p-2 me-sm-4">
                          <span class="avatar-initial bg-label-warning rounded">
                            <i class="ti-md ti ti-alert-circle text-body"></i>
                          </span>
                        </span>
                      </div>
                    </div>

                    <!-- HARI INI -->
                    <div class="col-sm-6 col-lg-2">
                      <div class="d-flex justify-content-between align-items-start">
                        <div>
                          <h4 class="mb-2" id="inventory_today">0</h4>
                          <p class="mb-0 fw-medium">Masuk Hari Ini</p>
                        </div>
                        <span class="avatar p-2">
                          <span class="avatar-initial bg-label-info rounded">
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
                      <th></th> <!-- responsive -->
                      <th></th> <!-- checkbox -->
                      <th>Nama Barang</th>
                      <th>Qty Masuk</th>
                      <th>Terpakai</th>
                      <th>Sisa Stok</th>
                      <th>Satuan</th>
                      <th>Vendor</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                </table>
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
              <script src="<?= base_url('assets/js/app-inventory-list.js') ?>"></script>
          <?= $this->endSection() ?>
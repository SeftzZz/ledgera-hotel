          <?= $this->extend('layouts/main') ?>

          <?= $this->section('content') ?>

              <!-- Content -->

              <div class="container-xxl flex-grow-1 container-p-y">
              
                <!-- Product List Table -->
                <div class="card">
                  <div class="card-header">
                    <h5 class="card-title mb-0">Filter</h5>
                    <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0">
                      <div class="col-md-4 product_status"></div>
                      <div class="col-md-4 product_category"></div>
                      <div class="col-md-4 product_branch"></div>
                    </div>
                  </div>
                  <div class="card-datatable table-responsive">
                    <table class="datatables-products table">
                      <thead class="border-top">
                        <tr>
                          <th></th>
                          <th>product</th>
                          <th>branch</th>
                          <th>category</th>
                          <th>price</th>
                          <th>stock</th>
                          <th>status</th>
                          <th>actions</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
              <!-- / Content -->

          <?= $this->endSection() ?>

          <?= $this->section('scripts') ?>
              <script src="<?= base_url('assets/js/app-ecommerce-product-list.js') ?>"></script>
          <?= $this->endSection() ?>
          <?= $this->extend('layouts/main') ?>

          <?= $this->section('content') ?>

            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">

              <!-- customers List Table -->
              <div class="card">
                <div class="card-datatable table-responsive">
                  <table class="datatables-customers table border-top">
                    <thead>
                      <tr>
                        <th></th>
                        <th></th>
                        <th>Customer</th>
                        <th class="text-nowrap">Customer Id</th>
                        <th>Phone</th>
                        <th>Order</th>
                        <th class="text-nowrap">Total Spent</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
            <!-- / Content -->

          <?= $this->endSection() ?>

          <?= $this->section('scripts') ?>
              <script src="<?= base_url('dashboard/assets/js/app-ecommerce-customer-all.js') ?>"></script>
          <?= $this->endSection() ?>
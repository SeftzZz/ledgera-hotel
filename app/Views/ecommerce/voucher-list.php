        <?= $this->extend('layouts/main') ?>

        <?= $this->section('content') ?>

        <div class="container-xxl flex-grow-1 container-p-y">
          
          <div class="app-ecommerce-voucher">

            <div class="card">

              <div class="card-datatable table-responsive">

                <table class="datatables-voucher-list table border-top">

                  <thead>
                    <tr>
                      <th></th>
                      <th>Code</th>
                      <th>Discount</th>
                      <th>Usage</th>
                      <th>Start Date</th>
                      <th>End Date</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>

                </table>

              </div>

            </div>


            <!-- Offcanvas Add Voucher -->
            <div
              class="offcanvas offcanvas-end"
              tabindex="-1"
              id="offcanvasVoucher"
              aria-labelledby="offcanvasVoucherLabel">

              <div class="offcanvas-header py-4">

                <h5 id="offcanvasVoucherLabel" class="offcanvas-title">
                  Create Voucher
                </h5>

                <button
                  type="button"
                  class="btn-close bg-label-secondary text-reset"
                  data-bs-dismiss="offcanvas">
                </button>

              </div>


              <div class="offcanvas-body border-top">

                <form id="voucherForm">

                  <!-- Voucher Code -->
                  <div class="mb-3">
                    <label class="form-label">Voucher Code</label>

                    <input
                      type="text"
                      name="code"
                      class="form-control"
                      placeholder="Example: DISC10"
                      required>
                  </div>


                  <!-- Discount Type -->
                  <div class="mb-3">

                    <label class="form-label">Discount Type</label>

                    <select
                      name="discount_type"
                      class="form-select"
                      required>

                      <option value="">Select type</option>
                      <option value="percent">Percent (%)</option>
                      <option value="fixed">Fixed Amount</option>

                    </select>

                  </div>


                  <!-- Discount Value -->
                  <div class="mb-3">

                    <label class="form-label">Discount Value</label>

                    <input
                      type="number"
                      name="discount_value"
                      class="form-control"
                      placeholder="Example: 10"
                      required>

                  </div>


                  <!-- Max Usage -->
                  <div class="mb-3">

                    <label class="form-label">Max Usage</label>

                    <input
                      type="number"
                      name="max_usage"
                      class="form-control"
                      placeholder="Example: 100">

                  </div>


                  <!-- Start Date -->
                  <div class="mb-3">

                    <label class="form-label">Start Date</label>

                    <input
                      type="datetime-local"
                      name="start_date"
                      class="form-control">

                  </div>


                  <!-- End Date -->
                  <div class="mb-3">

                    <label class="form-label">End Date</label>

                    <input
                      type="datetime-local"
                      name="end_date"
                      class="form-control">

                  </div>


                  <!-- Status -->
                  <div class="mb-3">

                    <label class="form-label">Status</label>

                    <select
                      name="status"
                      class="form-select"
                      required>

                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>

                    </select>

                  </div>


                  <!-- Submit -->
                  <div class="mt-4">

                    <button
                      type="submit"
                      class="btn btn-primary me-2">

                      Save Voucher

                    </button>

                    <button
                      type="reset"
                      class="btn bg-label-danger"
                      data-bs-dismiss="offcanvas">

                      Cancel

                    </button>

                  </div>

                </form>

              </div>

            </div>

          </div>

        </div>

        <?= $this->endSection() ?>



        <?= $this->section('scripts') ?>

        <script src="<?= base_url('dashboard/assets/vendor/libs/@form-validation/popular.js') ?>"></script>
        <script src="<?= base_url('dashboard/assets/vendor/libs/@form-validation/bootstrap5.js') ?>"></script>
        <script src="<?= base_url('dashboard/assets/vendor/libs/@form-validation/auto-focus.js') ?>"></script>

        <script src="<?= base_url('dashboard/assets/js/app-ecommerce-voucher-list.js') ?>"></script>

        <?= $this->endSection() ?>
          <?= $this->extend('layouts/main') ?>

          <?= $this->section('content') ?>

            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <!-- Target Budget List Widget -->

              <div class="card mb-4">
                <div class="card-widget-separator-wrapper">
                  <div class="card-body card-widget-separator">
                    <div class="row gy-4 gy-sm-1">
                      <div class="col-sm-6 col-lg-4">
                        <div
                          class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                          <div>
                            <h4 class="mb-2" id="order_pending">0</h4>
                            <p class="mb-0 fw-medium">Target Budget</p>
                          </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none me-4" />
                      </div>
                      <div class="col-sm-6 col-lg-2">
                        <div
                          class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                          <div>
                            <h4 class="mb-2" id="order_completed">0</h4>
                            <p class="mb-0 fw-medium">Room Revenue</p>
                          </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none" />
                      </div>
                      <div class="col-sm-6 col-lg-2">
                        <div
                          class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                          <div>
                            <h4 class="mb-2" id="order_processing">0</h4>
                            <p class="mb-0 fw-medium">FB Revenue</p>
                          </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none" />
                      </div>
                      <div class="col-sm-6 col-lg-2">
                        <div
                          class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                          <div>
                            <h4 class="mb-2" id="order_refunded">0</h4>
                            <p class="mb-0 fw-medium">Tax Service</p>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6 col-lg-2">
                        <div class="d-flex justify-content-between align-items-start">
                          <div>
                            <h4 class="mb-2" id="order_failed">0</h4>
                            <p class="mb-0 fw-medium">Total Margin</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="app-ecommerce-category">
                <!-- Items List Table -->
                <div class="card">
                  <div class="card-datatable table-responsive">
                    <table class="datatables-category-list table border-top">
                      <thead>
                        <tr>
                          <th></th>
                          <th>Items</th>
                          <th>Budget Spend</th>
                          <th>Budget Worker</th>
                          <th>Budget DW</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr>
                          <th></th>
                          <th></th>
                          <th>Total Spend</th>
                          <th>Total Worker</th>
                          <th>Total DW</th>
                          <th></th>
                          <th></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
                <!-- Offcanvas to add new customer -->
                <div
                  class="offcanvas offcanvas-end"
                  tabindex="-1"
                  id="offcanvasEcommerceCategoryList"
                  aria-labelledby="offcanvasEcommerceCategoryListLabel">

                  <div class="offcanvas-header py-4">
                    <h5 class="offcanvas-title">
                      Add Items Ratio
                    </h5>

                    <button
                      type="button"
                      class="btn-close bg-label-secondary text-reset"
                      data-bs-dismiss="offcanvas">
                    </button>
                  </div>

                  <div class="offcanvas-body border-top">

                    <form id="formRatio">

                      <!-- Items -->
                      <div class="mb-3">
                        <label class="form-label">Items</label>
                        <input
                          type="text"
                          name="department_category"
                          class="form-control"
                          placeholder="e.g. Front Office"
                          required>
                      </div>

                      <!-- Type -->
                      <div class="mb-3">
                        <label class="form-label">Ratio Type</label>
                        <select name="type" class="form-select" required>
                          <option value="">Select Type</option>
                          <option value="spend">Spend</option>
                          <option value="worker">Worker</option>
                          <option value="dw">DW</option>
                        </select>
                      </div>

                      <!-- Range -->
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Max (%)</label>
                          <input
                            type="number"
                            name="max_value"
                            class="form-control"
                            required>
                        </div>
                      </div>

                      <!-- Submit -->
                      <div class="mt-4">

                        <button type="submit" class="btn btn-primary me-2">
                          Save Ratio
                        </button>

                        <button type="reset" class="btn bg-label-danger" data-bs-dismiss="offcanvas">
                          Cancel
                        </button>

                      </div>

                    </form>

                  </div>
                </div>
              </div>
            </div>
            <!-- / Content -->

          <?= $this->endSection() ?>

          <?= $this->section('scripts') ?>
              <script src="<?= base_url('assets/vendor/libs/@form-validation/popular.js') ?>"></script>
              <script src="<?= base_url('assets/vendor/libs/@form-validation/bootstrap5.js') ?>"></script>
              <script src="<?= base_url('assets/vendor/libs/@form-validation/auto-focus.js') ?>"></script>
              <script src="<?= base_url('assets/js/app-ecommerce-ratio-list.js') ?>"></script>
          <?= $this->endSection() ?>
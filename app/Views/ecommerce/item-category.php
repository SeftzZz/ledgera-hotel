          <?= $this->extend('layouts/main') ?>

          <?= $this->section('content') ?>

            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              
              <div class="app-ecommerce-category">
                <!-- Category List Table -->
                <div class="card">
                  <div class="card-datatable table-responsive">
                    <table class="datatables-category-list table border-top">
                      <thead>
                        <tr>
                          <th></th>
                          <th>Category</th>
                          <th>Items</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
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
                    <h5 id="offcanvasEcommerceCategoryListLabel" class="offcanvas-title">
                      Add Category
                    </h5>

                    <button
                      type="button"
                      class="btn-close bg-label-secondary text-reset"
                      data-bs-dismiss="offcanvas">
                    </button>
                  </div>

                  <div class="offcanvas-body border-top">

                    <form id="eCommerceCategoryListForm">

                      <!-- Category Name -->
                      <div class="mb-3">
                        <label class="form-label" for="categoryName">Category Name</label>

                        <input
                          type="text"
                          id="categoryName"
                          name="name"
                          class="form-control"
                          placeholder="Enter category name"
                          required>
                      </div>

                      <!-- Icon -->
                      <div class="mb-3">
                        <label class="form-label" for="iconUpload">Icon</label>

                        <input
                          type="file"
                          id="iconUpload"
                          class="form-control">

                        <input
                          type="hidden"
                          name="icon"
                          id="iconPath">
                      </div>

                      <!-- Status -->
                      <div class="mb-3">
                        <label class="form-label" for="categoryStatus">Status</label>

                        <select
                          id="categoryStatus"
                          name="status"
                          class="form-select"
                          required>

                          <option value="">Select status</option>
                          <option value="active">Active</option>
                          <option value="inactive">Inactive</option>

                        </select>
                      </div>

                      <!-- Submit -->
                      <div class="mt-4">

                        <button
                          type="submit"
                          class="btn btn-primary me-2">
                          Save Category
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
            <!-- / Content -->

          <?= $this->endSection() ?>

          <?= $this->section('scripts') ?>
              <script src="<?= base_url('assets/vendor/libs/@form-validation/popular.js') ?>"></script>
              <script src="<?= base_url('assets/vendor/libs/@form-validation/bootstrap5.js') ?>"></script>
              <script src="<?= base_url('assets/vendor/libs/@form-validation/auto-focus.js') ?>"></script>
              <script src="<?= base_url('assets/js/app-ecommerce-category-list.js') ?>"></script>
          <?= $this->endSection() ?>
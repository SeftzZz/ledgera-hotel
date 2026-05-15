          <?= $this->extend('layouts/main') ?>

          <?= $this->section('content') ?>

            <div class="container-xxl flex-grow-1 container-p-y">

              <form id="formProduct">

                <div class="card mb-4">
                  <div class="card-header">
                    <h5>Add Item</h5>
                  </div>

                  <div class="card-body">

                    <div class="row">

                      <div class="col-md-6 mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                      </div>

                      <div class="col-md-6 mb-3">
                        <label>Department</label>
                        <select name="category_id" class="form-control">
                          <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>">
                              <?= $c['name'] ?>
                            </option>
                          <?php endforeach ?>
                        </select>
                      </div>

                      <div class="col-12 mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                      </div>

                      <div class="col-md-6 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                          <option value="available">Available</option>
                          <option value="unavailable">Unavailable</option>
                        </select>
                      </div>

                      <div class="col-md-6 mb-3">
                        <label>Image</label>
                        <input type="file" id="imageUpload" class="form-control">
                        <input type="hidden" name="image" id="imagePath">
                      </div>

                    </div>

                  </div>
                </div>

                <!-- BRANCH PRICE -->

                <div class="card mb-4">
                  <div class="card-header">
                    <h5>Branch Pricing</h5>
                  </div>

                  <div class="card-body">

                    <table class="table">

                      <thead>
                        <tr>
                          <th>Branch</th>
                          <th>Price</th>
                          <th>Stock</th>
                        </tr>
                      </thead>

                      <tbody>

                        <?php foreach ($branches as $b): ?>

                          <tr>

                            <td>
                              <?= $b['branch_name'] ?>
                              <input type="hidden" name="branch_id[]" value="<?= $b['id'] ?>">
                            </td>

                            <td>
                              <input type="number" name="price[]" class="form-control" placeholder="Enter price">
                            </td>

                            <td>
                              <input type="number" name="stock[]" class="form-control" value="0">
                            </td>

                          </tr>

                        <?php endforeach ?>

                      </tbody>

                    </table>

                  </div>
                </div>

                <button class="btn btn-primary">Save Item</button>

              </form>

            </div>

          <?= $this->endSection() ?>

          <?= $this->section('scripts') ?>
              <script src="<?= base_url('assets/js/app-ecommerce-product-add.js') ?>"></script>
          <?= $this->endSection() ?>
          <?= $this->extend('layouts/main') ?>

          <?= $this->section('content') ?>

              <!-- Content -->

              <div class="container-xxl flex-grow-1 container-p-y">
                <div class="row invoice-add">
                  <!-- Invoice Add-->
                  <div class="col-lg-12 col-12 mb-lg-0 mb-4">
                    <div class="card invoice-preview-card">
                      <div class="card-body">
                        <div class="row m-sm-4 m-0">
                          <div class="col-md-7 mb-md-0 mb-4 ps-0">
                            <div class="d-flex svg-illustration mb-4 gap-2 align-items-center">
                              <div class="app-brand-logo demo">
                                <img src="../../<?= session('branch_logo') ?>" alt="Avatar" width="100%" />
                              </div>
                              <span class="app-brand-text fw-bold fs-4"> <?= session('branch_name') ?></span>
                            </div>
                            <p class="mb-2"><?= session('branch_address') ?></p>
                          </div>
                          <div class="col-md-5">
                            <dl class="row mb-2">
                              <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end ps-0">
                                <span class="h4 text-capitalize mb-0 text-nowrap">Income ID</span>
                              </dt>
                              <dd class="col-sm-6 d-flex justify-content-md-end pe-0 ps-0 ps-sm-2">
                                <div class="input-group input-group-merge disabled w-px-300">                                  
                                  <input
                                    type="text"
                                    class="form-control"
                                    disabled
                                    placeholder="<?= $order_number ?>"
                                    value="<?= $order_number ?>"
                                    id="invoiceId" />
                                </div>
                              </dd>
                            </dl>
                          </div>
                        </div>

                        <hr class="my-3 mx-n4" />

                        <form class="source-item pt-4 px-0 px-sm-4">
                          <div class="mb-3" data-repeater-list="group-a">
                            <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item>
                              <div class="d-flex border rounded position-relative pe-0">
                                <div class="row w-100 p-3">
                                  <div class="col-md-4 col-12 mb-md-0 mb-3">
                                    <p class="mb-2 repeater-title">Item</p>
                                    <select class="form-select item-details mb-3">
                                      <option selected disabled>Select Item</option>
                                      <option value="App Design">App Design</option>
                                      <option value="App Customization">App Customization</option>
                                      <option value="ABC Template">ABC Template</option>
                                      <option value="App Development">App Development</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 col-12 mb-md-0 mb-3">
                                    <p class="mb-2 repeater-title">Qty</p>
                                    <input
                                      type="text"
                                      class="form-control invoice-item-qty"
                                      placeholder="1"
                                      min="1" />
                                  </div>
                                  <div class="col-md-2 col-12 mb-md-0 mb-3">
                                    <p class="mb-2 repeater-title">Deposit</p>
                                    <input
                                      type="text"
                                      class="form-control invoice-item-deposit"
                                      placeholder="0"
                                      id="deposit" />
                                  </div>
                                  <div class="col-md-2 col-12 mb-md-0 mb-3">
                                    <p class="mb-2 repeater-title">Payment</p>
                                    <input
                                      type="text"
                                      class="form-control invoice-item-price"
                                      placeholder="0"
                                      id="price" />
                                  </div>
                                  <div class="col-md-2 col-12 mb-md-0 mb-3">
                                    <p class="mb-2 repeater-title">Date</p>
                                    <input
                                      type="text"
                                      class="form-control w-100 date-picker invoice-item-date"
                                      placeholder="YYYY-MM-DD"/>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row pb-4">
                            <div class="col-12">
                              <button type="button" class="btn btn-primary" data-repeater-create>Add Item</button>
                            </div>
                          </div>
                        </form>

                        <hr class="my-3 mx-n4" />

                        <div class="row pb-4">
                          <div class="col-12">
                            <button type="button" id="btnSubmit" class="btn btn-success d-grid w-100 mb-2">
                              Submit
                            </button>
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
                  <!-- /Invoice Add-->

                <!-- /Offcanvas -->
              </div>
              <!-- / Content -->

          <?= $this->endSection() ?>

          <?= $this->section('scripts') ?>
              <script>
                window.customerOptions = `
                  <?php foreach ($customers as $user): ?>
                    <option 
                      value="<?= $user['name'] ?>" 
                      data-phone="<?= $user['phone'] ?>" 
                      data-email="<?= $user['email'] ?>"
                    >
                      <?= esc($user['name']) ?>
                    </option>
                  <?php endforeach; ?>
                `;
              </script>
              <!-- Vendors JS -->
              <script src="<?= base_url('assets/vendor/libs/flatpickr/flatpickr.js') ?>"></script>
              <script src="<?= base_url('assets/vendor/libs/cleavejs/cleave.js') ?>"></script>
              <script src="<?= base_url('assets/vendor/libs/cleavejs/cleave-phone.js') ?>"></script>
              <script src="<?= base_url('assets/vendor/libs/jquery-repeater/jquery-repeater.js') ?>"></script>
              <script src="<?= base_url('assets/js/app-invoice-add.js') ?>"></script>
          <?= $this->endSection() ?>
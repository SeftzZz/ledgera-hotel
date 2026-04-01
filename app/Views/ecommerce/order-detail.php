          <?= $this->extend('layouts/main') ?>

          <?= $this->section('content') ?>

            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                <div class="d-flex flex-column justify-content-center gap-2 gap-sm-0">
                  <h5 class="mb-1 mt-3 d-flex flex-wrap gap-2 align-items-end">

                    Income ID #
                    <span id="order_number"></span>

                    <span class="badge bg-label-success" id="order_status"></span>

                  </h5>

                  <p class="text-body" id="order_date"></p>
                </div>
                <div class="d-flex align-content-center flex-wrap gap-2">
                  <button class="btn btn-label-danger delete-order">Delete Income</button>
                </div>
              </div>

              <!-- Income Details Table -->

              <div class="row">
                <div class="col-12 col-lg-8">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="card-title m-0">Order details</h5>
                      <h6 class="m-0"><a href=" javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editIncome">Edit</a></h6>
                    </div>
                    <div class="card-datatable table-responsive">
                      <table class="datatables-order-details table border-top">
                        <thead>
                          <tr>
                            <th class="w-50">products</th>
                            <th class="w-25">price</th>
                            <th class="w-25">qty</th>
                            <th>total</th>
                          </tr>
                        </thead>
                      </table>
                      <div class="d-flex justify-content-end align-items-center m-3 mb-2 p-1">
                        <div class="order-calculations">
                          <div class="d-flex justify-content-between mb-2">
                            <span class="w-px-100 text-heading">Subtotal:</span>
                            <h6 class="mb-0" id="order_subtotal"></h6>
                          </div>
                          <div class="d-flex justify-content-between mb-2">
                            <span class="w-px-100 text-heading">Deposit:</span>
                            <h6 class="mb-0" id="order_deposit"></h6>
                          </div>
                          <div class="d-flex justify-content-between mb-2">
                            <span class="w-px-100 text-heading">Discount:</span>
                            <h6 class="mb-0" id="order_discount"></h6>
                          </div>
                          <div class="d-flex justify-content-between mb-2">
                            <span class="w-px-100 text-heading">Tax:</span>
                            <h6 class="mb-0">0</h6>
                          </div>
                          <div class="d-flex justify-content-between">
                            <h6 class="w-px-100 mb-0">Total:</h6>
                            <h6 class="mb-0" id="order_total"></h6>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-12 col-lg-4">
                  <div class="card mb-4">
                    <div class="card-header">
                      <h6 class="card-title m-0">Customer details</h6>
                    </div>
                    <div class="card-body">
                      <div class="d-flex justify-content-start align-items-center mb-4">
                        <div class="avatar me-2">
                          <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle" />
                        </div>
                        <div class="d-flex flex-column">
                          <a href="app-user-view-account.html" class="text-body text-nowrap">
                            <h6 class="mb-0" id="customer_name"></h6>
                          </a>
                          <small class="text-muted" id="customer_id"></small>
                        </div>
                      </div>
                      <div class="d-flex justify-content-start align-items-center mb-4">
                        <span
                          class="avatar rounded-circle bg-label-success me-2 d-flex align-items-center justify-content-center"
                          ><i class="ti ti-shopping-cart ti-sm"></i
                        ></span>
                        <h6 class="text-body text-nowrap mb-0">12 Orders</h6>
                      </div>
                      <div class="d-flex justify-content-between">
                        <h6>Contact info</h6>
                      </div>
                      <p class="mb-1">Email: <span id="customer_email"></span></p>
                      <p class="mb-0">Mobile: +1 (609) 972-22-22</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Edit Income Modal -->
              <div class="modal fade" id="editIncome" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                  <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      <div class="text-center mb-4">
                        <h3 class="mb-2">Edit Income Information</h3>
                        <p class="text-muted">Updating income details will receive a privacy audit.</p>
                      </div>
                      <form id="editIncomeForm" class="row g-3" onsubmit="return false">
                        <div class="col-12 col-md-6">
                          <label class="form-label" for="modalEditDeposit">Deposit</label>
                          <input
                            type="text"
                            id="modalEditDeposit"
                            name="modalEditDeposit"
                            class="form-control"
                            placeholder="0" />
                        </div>
                        <div class="col-12 col-md-6">
                          <label class="form-label" for="modalEditStatus">Status</label>
                          <select
                            id="modalEditStatus"
                            name="modalEditStatus"
                            class="select2 form-select"
                            aria-label="Default select example">
                            <option selected>Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="paid">Paid</option>
                          </select>
                        </div>
                        <div class="col-12 text-center">
                          <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                          <button
                            type="reset"
                            class="btn btn-label-secondary"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                            Cancel
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Edit Income Modal -->

            </div>
            <!-- / Content -->

          <?= $this->endSection() ?>

          <?= $this->section('scripts') ?>
              <script src="<?= base_url('assets/vendor/libs/cleavejs/cleave.js') ?>"></script>
              <script src="<?= base_url('assets/vendor/libs/cleavejs/cleave-phone.js') ?>"></script>

              <script src="<?= base_url('assets/js/app-ecommerce-order-details.js') ?>"></script>
          <?= $this->endSection() ?>
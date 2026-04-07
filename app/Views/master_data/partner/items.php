          <?= $this->extend('layouts/main') ?>

          <?= $this->section('content') ?>

          <div class="container-xxl flex-grow-1 container-p-y">

            <!-- 🔥 HIDDEN VENDOR ID -->
            <input type="hidden" id="vendor_id" value="<?= $vendor_id ?>">

            <!-- Vendor Items Table -->
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Vendor Items</h5>

                <button class="btn btn-primary" id="btnAddItem">
                  <i class="ti ti-plus"></i> Add Item
                </button>
              </div>

              <div class="card-datatable table-responsive">
                <table class="table dtVendorItems">
                  <thead class="border-top">
                    <tr>
                      <th></th>
                      <th>No</th>
                      <th>Sparepart</th>
                      <th>Type</th>
                      <th>Satuan</th>
                      <th>Harga</th>
                      <th>No Seri</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>

            <!-- ADD ITEM MODAL -->
            <div class="modal fade" id="modalAddItem" tabindex="-1">
              <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                  <form id="formAddItem">

                    <div class="modal-header">
                      <h5 class="modal-title">Add Vendor Item</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                      <!-- 🔥 hidden vendor -->
                      <input type="hidden" name="vendor_id" id="item_vendor_id">

                      <div class="row">

                        <div class="col-md-6 mb-3">
                          <label class="form-label">Sparepart *</label>
                          <input type="text" name="sparepart" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label class="form-label">Type *</label>
                          <select name="type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="Sayur">Sayur</option>
                            <option value="Buah">Buah</option>
                            <option value="Elektrik">Elektrik</option>
                            <option value="Umum">Umum</option>
                          </select>
                        </div>

                      </div>

                      <div class="row">

                        <div class="col-md-6 mb-3">
                          <label class="form-label">Harga *</label>
                          <input type="number" name="harga" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label class="form-label">No Seri</label>
                          <input type="text" name="no_seri" class="form-control">
                        </div>

                      </div>

                      <div class="row">

                        <div class="col-md-6 mb-3">
                          <label class="form-label">Satuan</label>
                          <select name="satuan" class="form-select" required>
                            <option value="">Select Satuan</option>
                            <option value="kg">Kg</option>
                            <option value="bal">Bal</option>
                            <option value="pack">Pack</option>
                            <option value="pcs">Pcs</option>
                            <option value="can">Can</option>
                            <option value="galon">Galon</option>
                          </select>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label class="form-label">Status</label>
                          <select name="status" class="form-select">
                            <option value="Aktif">Aktif</option>
                            <option value="Non Aktif">Non Aktif</option>
                          </select>
                        </div>

                      </div>

                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">Save Item</button>
                    </div>

                  </form>

                </div>
              </div>
            </div>

            <!-- EDIT ITEM MODAL -->
            <div class="modal fade" id="modalEditItem" tabindex="-1">
              <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                  <form id="formEditItem">

                    <input type="hidden" name="id" id="edit_item_id">

                    <div class="modal-header">
                      <h5 class="modal-title">Edit Vendor Item</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                      <div class="row">

                        <div class="col-md-6 mb-3">
                          <label class="form-label">Sparepart *</label>
                          <input type="text" name="sparepart" id="edit_sparepart" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label class="form-label">Type *</label>
                          <select name="type" id="edit_type" class="form-select" required>
                            <option value="Sayur">Sayur</option>
                            <option value="Buah">Buah</option>
                            <option value="Elektrik">Elektrik</option>
                          </select>
                        </div>

                      </div>

                      <div class="row">

                        <div class="col-md-6 mb-3">
                          <label class="form-label">Harga *</label>
                          <input type="number" name="harga" id="edit_harga" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                          <label class="form-label">No Seri</label>
                          <input type="text" name="no_seri" id="edit_no_seri" class="form-control">
                        </div>

                      </div>

                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Status</label>
                          <select name="status" id="edit_status" class="form-select">
                            <option value="Aktif">Aktif</option>
                            <option value="Non Aktif">Non Aktif</option>
                          </select>
                        </div>
                      </div>

                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                  </form>

                </div>
              </div>
            </div>
          </div>

          <?= $this->endSection() ?>

          <?= $this->section('scripts') ?>

          <script>
          $(function () {

            let vendorId = $('#vendor_id').val();

            $('.dtVendorItems').DataTable({
              processing: true,
              ajax: {
                url: `/api/partners/${vendorId}/items`,
                headers: {
                  Authorization: 'Bearer ' + window.jwtToken
                },
                dataSrc: 'data'
              },
              columns: [
                { data: null },
                { data: null },
                { data: 'sparepart' },
                { data: 'type' },
                { data: 'satuan' },
                { data: 'harga' },
                { data: 'no_seri' },
                { data: 'status' },
                { data: null }
              ],
              columnDefs: [
                {
                  targets: 0,
                  className: 'control',
                  orderable: false,
                  searchable: false,
                  render: () => ''
                },
                {
                  targets: 1,
                  render: function (data, type, full, meta) {
                    return meta.row + 1;
                  }
                },
                {
                  targets: 3,
                  render: function (data) {
                    return data;
                  }
                },
                {
                  targets: 5,
                  render: function (data) {
                    return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                  }
                },
                {
                  targets: 7,
                  render: function (data) {
                    return data === 'Aktif'
                      ? '<span class="badge bg-label-success">Aktif</span>'
                      : '<span class="badge bg-label-danger">Non Aktif</span>';
                  }
                },
                {
                  targets: -1,
                  render: function (data, type, full) {
                    return `
                      <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-icon btn-primary btn-edit" data-id="${full.id}">
                          <i class="ti ti-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-icon btn-danger btn-delete" data-id="${full.id}">
                          <i class="ti ti-trash"></i>
                        </button>
                      </div>
                    `;
                  }
                }
              ],
              order: [[1, 'asc']]
            });

            $('#btnAddItem').on('click', function () {

              let vendorId = $('#vendor_id').val();

              $('#item_vendor_id').val(vendorId);

              $('#modalAddItem').modal('show');

            });

            $('#formAddItem').on('submit', function (e) {
              e.preventDefault();

              let formData = $(this).serializeArray();
              let data = {};

              formData.forEach(i => data[i.name] = i.value);

              $.ajax({
                url: '/api/partners/items',
                type: 'POST',
                headers: {
                  Authorization: 'Bearer ' + window.jwtToken
                },
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function (res) {

                  if (res.status) {

                    Swal.fire('Success', 'Item saved', 'success');

                    $('#modalAddItem').modal('hide');
                    $('#formAddItem')[0].reset();

                    $('.dtVendorItems').DataTable().ajax.reload();

                  } else {
                    Swal.fire('Error', res.message, 'error');
                  }

                }
              });

            });

            $('.dtVendorItems tbody').on('click', '.btn-edit', function () {

              let id = $(this).data('id');

              $.ajax({
                url: `/api/partners/items/${id}`,
                type: 'GET',
                headers: {
                  Authorization: 'Bearer ' + window.jwtToken
                },
                success: function (res) {

                  if (res.status) {

                    let d = res.data;

                    $('#edit_item_id').val(d.id);
                    $('#edit_sparepart').val(d.sparepart);
                    $('#edit_type').val(d.type);
                    $('#edit_harga').val(d.harga);
                    $('#edit_no_seri').val(d.no_seri);
                    $('#edit_status').val(d.status);

                    $('#modalEditItem').modal('show');

                  } else {
                    Swal.fire('Error', res.message, 'error');
                  }

                }
              });

            });

            $('#formEditItem').on('submit', function (e) {
              e.preventDefault();

              let id = $('#edit_item_id').val();

              let formData = $(this).serializeArray();
              let data = {};

              formData.forEach(i => data[i.name] = i.value);

              $.ajax({
                url: `/api/partners/items/${id}`,
                type: 'PUT',
                headers: {
                  Authorization: 'Bearer ' + window.jwtToken
                },
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function (res) {

                  if (res.status) {

                    Swal.fire('Success', 'Item updated', 'success');

                    $('#modalEditItem').modal('hide');

                    $('.dtVendorItems').DataTable().ajax.reload();

                  } else {
                    Swal.fire('Error', res.message, 'error');
                  }

                }
              });

            });

            $('.dtVendorItems tbody').on('click', '.btn-delete', function () {

              let id = $(this).data('id');

              Swal.fire({
                title: 'Delete item?',
                text: "This action cannot be undone",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete'
              }).then((result) => {

                if (result.isConfirmed) {

                  $.ajax({
                    url: `/api/partners/items/${id}`,
                    type: 'DELETE',
                    headers: {
                      Authorization: 'Bearer ' + window.jwtToken
                    },
                    success: function (res) {

                      if (res.status) {

                        Swal.fire('Deleted!', 'Item removed', 'success');

                        $('.dtVendorItems').DataTable().ajax.reload();

                      } else {
                        Swal.fire('Error', res.message, 'error');
                      }

                    }
                  });

                }

              });

            });

          });
          </script>

          <?= $this->endSection() ?>
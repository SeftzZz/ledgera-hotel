<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="dtBranch table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Company</th>
                        <th>Branch Code</th>
                        <th>Branch Name</th>
                        <th>Action</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- ADD MODAL -->
    <div class="modal fade" id="modalAddBranch" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formAddBranch">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Branch</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company *</label>
                                <select name="company_id" class="form-select" required>
                                    <option value="">Select Company</option>
                                    <?php foreach ($companies ?? [] as $company): ?>
                                        <option value="<?= $company['id'] ?>">
                                            <?= esc($company['company_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Branch Code *</label>
                                <input type="text" name="branch_code" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Branch Name *</label>
                                <input type="text" name="branch_name" class="form-control" required>
                            </div>
                        </div>

                        <h6 class="mb-3 mt-3">Target & Revenue Setting</h6>

                        <div class="target-repeater">

                          <div data-repeater-list="targets">

                            <div data-repeater-item>
                              <div class="border rounded p-3 mb-3">

                                <!-- ROW 1 -->
                                <div class="row">
                                  <div class="col-md-6 mb-3">
                                    <label class="form-label">Target Revenue *</label>
                                    <div class="input-group">
                                      <span class="input-group-text range-start">0 -</span>
                                      <input type="number" name="target" class="form-control target" required>
                                    </div>
                                  </div>

                                  <div class="col-md-3 mb-3">
                                    <label class="form-label">Room Revenue</label>
                                    <div class="input-group">
                                      <input type="number" name="room_revenue" class="form-control room">
                                      <span class="input-group-text">%</span>
                                    </div>
                                    <div class="form-text preview-room">Rp 0</div>
                                  </div>

                                  <div class="col-md-3 mb-3">
                                    <label class="form-label">FB Revenue</label>
                                    <div class="input-group">
                                      <input type="number" name="fb_revenue" class="form-control fb">
                                      <span class="input-group-text">%</span>
                                    </div>
                                    <div class="form-text preview-fb">Rp 0</div>
                                  </div>
                                </div>

                                <!-- ROW 2 -->
                                <div class="row">
                                  <div class="col-md-5 mb-3">
                                    <label class="form-label">Tax & Service</label>
                                    <div class="input-group">
                                      <input type="number" name="tax_service" class="form-control tax">
                                      <span class="input-group-text">%</span>
                                    </div>
                                    <div class="form-text preview-tax">Rp 0</div>
                                  </div>

                                  <div class="col-md-5 mb-3">
                                    <label class="form-label">Total Margin</label>
                                    <div class="input-group">
                                      <input type="number" name="total_margin" class="form-control margin">
                                      <span class="input-group-text">%</span>
                                    </div>
                                    <div class="form-text preview-margin">Rp 0</div>
                                  </div>

                                  <!-- DELETE -->
                                  <div class="col-md-2">
                                    <label class="form-label"></label>
                                    <div class="input-group">
                                        <button type="button" data-repeater-delete class="btn btn-danger w-100">
                                          <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                  </div>
                                </div>

                              </div>
                            </div>

                          </div>

                          <!-- ADD BUTTON -->
                          <button type="button" data-repeater-create class="btn btn-primary">
                            <i class="ti ti-plus"></i> Tambah Target
                          </button>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="modalEditBranch" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formEditBranch">

                    <input type="hidden" name="id" id="edit_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Branch</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company *</label>
                                <select name="company_id" id="edit_company_id" class="form-select" required>
                                    <option value="">Select Company</option>
                                    <?php foreach ($companies ?? [] as $company): ?>
                                        <option value="<?= $company['id'] ?>">
                                            <?= esc($company['company_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Branch Code *</label>
                                <input type="text" name="branch_code" id="edit_branch_code" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Branch Name *</label>
                                <input type="text" name="branch_name" id="edit_branch_name" class="form-control" required>
                            </div>
                        </div>

                        <h6 class="mb-3 mt-3">Target & Revenue Setting</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Target Revenue *</label>
                                <div class="input-group">
                                    <span class="input-group-text">0 -</span>
                                    <input type="number" name="target" id="edit_target" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Room Revenue</label>
                                <div class="input-group">
                                    <input type="number" name="room_revenue" id="edit_room_revenue" class="form-control">
                                    <span class="input-group-text">%</span>
                                </div>
                                <div id="edit_preview_room" class="form-text">Rp 0</div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">FB Revenue</label>
                                <div class="input-group">
                                    <input type="number" name="fb_revenue" id="edit_fb_revenue" class="form-control">
                                    <span class="input-group-text">%</span>
                                </div>
                                <div id="edit_preview_fb" class="form-text">Rp 0</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tax & Service</label>
                                <div class="input-group">
                                    <input type="number" name="tax_service" id="edit_tax_service" class="form-control">
                                    <span class="input-group-text">%</span>
                                </div>
                                <div id="edit_preview_tax" class="form-text">Rp 0</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Margin</label>
                                <div class="input-group">
                                    <input type="number" name="total_margin" id="edit_total_margin" class="form-control">
                                    <span class="input-group-text">%</span>
                                </div>
                                <div id="edit_preview_margin" class="form-text">Rp 0</div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<link rel="stylesheet" href="<?= base_url('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') ?>" />
<link rel="stylesheet" href="<?= base_url('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') ?>" />
<link rel="stylesheet" href="<?= base_url('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') ?>" />
<script src="<?= base_url('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') ?>"></script>

<script>
'use strict';
const token = window.jwtToken;
const userId = window.userId;
const branchId = window.branchId;

$(function () {

    let dt_table = $('.dtBranch'), dt_branch;

    if (dt_table.length) {

        dt_branch = dt_table.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?= base_url('branch/datatable') ?>",
                type: "POST",
                data: d => {
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                }
            },
            columns: [
                { data: null },
                { data: 'no' },
                { data: 'company_name' },
                { data: 'branch_code' },
                { data: 'branch_name' },
                { data: 'action' },
                { data: null }
            ],
            columnDefs: [
                {
                    className: 'control',
                    orderable: false,
                    searchable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: () => ''
                },
                {
                    targets: 1,
                    orderable: false,
                    searchable: false
                },
                {
                    targets: -1,
                    title: 'Detail',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, full, meta) {

                        let targets = full.targets ? full.targets.split(',') : [];

                        let targetList = '';

                        targets.forEach(item => {

                            let [id, value] = item.split(':');

                            targetList += `
                                <a href="/branch/${full.branch_id}/ratio/${id}" class="dropdown-item">
                                    <strong>${formatRupiah(parseInt(value))}</strong>
                                </a>
                            `;
                        });

                        return (
                            '<div class="d-flex justify-content-sm-center align-items-sm-center">' +

                                '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">' +
                                    '<i class="ti ti-dots-vertical"></i>' +
                                '</button>' +

                                '<div class="dropdown-menu dropdown-menu-end m-0">' +
                                    targetList +
                                '</div>' +

                            '</div>'
                        );
                    }
                }
            ],
            order: [[3, 'asc']],
            dom:
                '<"card-header flex-column flex-md-row"' +
                    '<"head-label text-center">' +
                    '<"dt-action-buttons text-end pt-3 pt-md-0"B>' +
                '>' +
                '<"row"' +
                    '<"col-sm-12 col-md-6"l>' +
                    '<"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>' +
                '>' +
                't' +
                '<"row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                '>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 100],
            buttons: [
                {
                    extend: 'collection',
                    className: 'btn btn-label-primary dropdown-toggle me-2',
                    text: '<i class="ti ti-file-export me-sm-1"></i> Export',
                    buttons: [
                        { extend: 'print', className: 'dropdown-item' },
                        { extend: 'csv', className: 'dropdown-item' },
                        { extend: 'pdf', className: 'dropdown-item' }
                    ]
                },
                {
                    text: '<i class="ti ti-plus me-sm-1"></i> Add Branch',
                    className: 'create-new btn btn-primary',
                    action: function () {
                        $('#modalAddBranch').modal('show');
                    }
                }
            ]
        });

        $('div.head-label').html('<h5 class="card-title mb-0">Branch List</h5>');
    }


    // SUBMIT ADD BRANCH
    $('#formAddBranch').on('submit', function (e) {
        e.preventDefault();

        // 🔥 pakai serialize (AMAN untuk repeater)
        let formData = $(this).serialize();

        Swal.fire({
            title: 'Add new branch?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(result => {

            if (result.isConfirmed) {

                $.ajax({
                    url: "<?= base_url('api/branches') ?>",
                    type: "POST",
                    data: formData,
                    dataType: 'json',

                    success: function (res) {

                        if (res.status) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Saved',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // 🔥 reset form
                            $('#formAddBranch')[0].reset();

                            // reset repeater ke 1 item
                            let $repeater = $('.target-repeater');

                            $repeater.find('[data-repeater-item]').not(':first').remove();

                            // reset value item pertama
                            $repeater.find('[data-repeater-item]').first().find('input').val('');

                            // reset preview
                            $repeater.find('.preview-room').text('Rp 0');
                            $repeater.find('.preview-fb').text('Rp 0');
                            $repeater.find('.preview-tax').text('Rp 0');
                            $repeater.find('.preview-margin').text('Rp 0');

                            // reset range
                            updateTargetRanges();

                            $('#modalAddBranch').modal('hide');
                            $('.dtBranch').DataTable().ajax.reload(null, false);

                        } else {
                            Swal.fire('Failed', res.message, 'error');
                        }
                    },

                    error: function () {
                        Swal.fire('Error', 'Server error', 'error');
                    }
                });
            }
        });
    });

    $('.dtBranch tbody').on('click', '.edit-record', function () {

        let id = $(this).data('id');

        $.get(`<?= base_url('api/branches') ?>/${id}`, function (res) {

            if (res.status) {

                let d = res.data;

                $('#edit_id').val(d.id);
                $('#edit_company_id').val(d.company_id).trigger('change');
                $('#edit_branch_code').val(d.branch_code);
                $('#edit_branch_name').val(d.branch_name);

                $('#edit_target').val(d.target);
                $('#edit_room_revenue').val(d.room_revenue);
                $('#edit_fb_revenue').val(d.fb_revenue);
                $('#edit_tax_service').val(d.tax_service);
                $('#edit_total_margin').val(d.total_margin);

                calculateTarget('#formEditBranch ');

                $('#modalEditBranch').modal('show');

            } else {
                Swal.fire('Error', res.message, 'error');
            }
        });
    });

    $('#formEditBranch').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        Swal.fire({
            title: 'Update branch?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, update'
        }).then(result => {

            if (result.isConfirmed) {

                $.ajax({
                    url: "<?= base_url('api/branches/update') ?>",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (res) {

                        if (res.status) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Updated',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#modalEditBranch').modal('hide');
                            $('.dtBranch').DataTable().ajax.reload(null, false);

                        } else {
                            Swal.fire('Failed', res.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Server error', 'error');
                    }
                });
            }
        });
    });

    function formatRupiah(angka) {
        return 'Rp ' + (angka || 0).toLocaleString('id-ID');
    }

    function calculateTarget(prefix = '') {

        let isEdit = prefix.includes('formEditBranch');

        let target = parseFloat($(prefix + '[name="target"]').val()) || 0;
        let room   = parseFloat($(prefix + '[name="room_revenue"]').val()) || 0;
        let fb     = parseFloat($(prefix + '[name="fb_revenue"]').val()) || 0;
        let tax    = parseFloat($(prefix + '[name="tax_service"]').val()) || 0;
        let margin = parseFloat($(prefix + '[name="total_margin"]').val()) || 0;

        let roomVal   = target * room / 100;
        let fbVal     = target * fb / 100;
        let taxVal    = target * tax / 100;
        let marginVal = target * margin / 100;

        if (isEdit) {
            $('#edit_preview_room').text(formatRupiah(roomVal));
            $('#edit_preview_fb').text(formatRupiah(fbVal));
            $('#edit_preview_tax').text(formatRupiah(taxVal));
            $('#edit_preview_margin').text(formatRupiah(marginVal));
        } else {
            $('#preview_room').text(formatRupiah(roomVal));
            $('#preview_fb').text(formatRupiah(fbVal));
            $('#preview_tax').text(formatRupiah(taxVal));
            $('#preview_margin').text(formatRupiah(marginVal));
        }
    }

    // ADD
    $(document).on('input', 
        '#formAddBranch input', 
        () => calculateTarget('')
    );

    // EDIT
    $(document).on('input', 
        '#formEditBranch input', 
        () => calculateTarget('#formEditBranch ')
    );

    $('.target-repeater').repeater({
      initEmpty: false,

      show: function () {
        $(this).slideDown();

        // 🔥 update range setelah add
        setTimeout(() => {
          updateTargetRanges();
        }, 100);
      },

      hide: function (deleteElement) {
        if (confirm('Hapus target ini?')) {
          $(this).slideUp(deleteElement);

          // 🔥 update ulang setelah delete
          setTimeout(() => {
            updateTargetRanges();
          }, 300);
        }
      }
    });

    function updateTargetRanges() {

      let items = $('[data-repeater-item]');

      items.each(function(index) {

        let prev = items.eq(index - 1).find('.target').val() || 0;

        if (index === 0) {
          $(this).find('.range-start').text('0 -');
        } else {
          $(this).find('.range-start').text(prev + ' -');
        }

      });
    }

    $(document).on('input', '.target', function () {
      updateTargetRanges();
    });

    $(document).ready(function () {
      updateTargetRanges();
    });
});
</script>

<?= $this->endSection() ?>

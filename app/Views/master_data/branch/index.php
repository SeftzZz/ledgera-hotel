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
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
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
                { data: 'action' }
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
                    orderable: false,
                    searchable: false
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

        let formData = new FormData(this);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

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
                    processData: false,
                    contentType: false,
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

                            $('#modalAddBranch').modal('hide');
                            $('.dtBranch').DataTable().ajax.reload(null, false);
                            $('#formAddBranch')[0].reset();

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

});
</script>

<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="dtCompany table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- ADD MODAL -->
    <div class="modal fade" id="modalAddCompany" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formAddCompany">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Company</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company Code *</label>
                                <input type="text" name="company_code" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company Name *</label>
                                <input type="text" name="company_name" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="company_addr" class="form-control"></textarea>
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

    <!-- LOAN MODAL -->
    <div class="modal fade" id="modalLoan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <form id="formLoan">
                    <input type="hidden" name="company_id" id="loan_company_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Pengajuan Kredit / Installment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Jumlah Kredit *</label>
                            <input type="number" name="amount" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tenor (bulan) *</label>
                            <input type="number" name="tenor" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mulai Bulan *</label>
                            <input type="month" name="start_date" class="form-control" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Submit</button>
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

$(function () {

    let dt_table = $('.dtCompany'), dt_company;

    if (dt_table.length) {

        dt_company = dt_table.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?= base_url('company/datatable') ?>",
                type: "POST",
                data: d => {
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                }
            },
            columns: [
                { data: null },
                { data: 'no' },
                { data: 'company_code' },
                { data: 'company_name' },
                { data: 'company_addr' },
                { data: 'created_at' },
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
            order: [[2, 'asc']],
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
                    className: 'btn btn-label-primary dropdown-toggle me-2 waves-effect waves-light',
                    text: '<i class="ti ti-file-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                    buttons: [
                        {
                            extend: 'print',
                            className: 'dropdown-item',
                            exportOptions: { columns: [1,2,3,4,5] }
                        },
                        {
                            extend: 'csv',
                            className: 'dropdown-item',
                            exportOptions: { columns: [1,2,3,4,5] }
                        },
                        {
                            extend: 'pdf',
                            className: 'dropdown-item',
                            exportOptions: { columns: [1,2,3,4,5] }
                        }
                    ]
                },
                {
                    text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-sm-inline-block">Add Company</span>',
                    className: 'create-new btn btn-primary waves-effect waves-light',
                    action: function () {
                        $('#modalAddCompany').modal('show');
                    }
                }
            ],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            let data = row.data();
                            return 'Details of ' + data.company_name;
                        }
                    }),
                    type: 'column',
                    renderer: function (api, rowIdx, columns) {
                        let data = $.map(columns, function (col) {
                            return col.title !== ''
                                ? '<tr>' +
                                      '<td>' + col.title + ':</td>' +
                                      '<td>' + col.data + '</td>' +
                                  '</tr>'
                                : '';
                        }).join('');

                        return data
                            ? $('<table class="table"><tbody /></table>').append(data)
                            : false;
                    }
                }
            }
        });

        $('div.head-label').html('<h5 class="card-title mb-0">Company List</h5>');
    }

    // Submit Add Company
    $('#formAddCompany').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        Swal.fire({
            title: 'Add new company?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(result => {

            if (result.isConfirmed) {

                $.ajax({
                    url: "<?= base_url('company/store') ?>",
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

                            $('#modalAddCompany').modal('hide');
                            $('.dtCompany').DataTable().ajax.reload(null, false);

                            $('#formAddCompany')[0].reset();

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

    $(document).on('click', '.btn-loan', function () {
        let companyId = $(this).data('id');

        // set ke hidden input
        $('#loan_company_id').val(companyId);

        // tampilkan modal
        $('#modalLoan').modal('show');
    });

    $('#formLoan').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        Swal.fire({
            title: 'Submit loan?',
            text: 'This will generate journal & installment schedule',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed'
        }).then(result => {

            if (result.isConfirmed) {

                $.ajax({
                    url: "<?= base_url('company/loan') ?>",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (res) {

                        if (res.status) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#modalLoan').modal('hide');
                            $('#formLoan')[0].reset();

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

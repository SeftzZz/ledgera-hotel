<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php 
    $sessionCompanyId = session()->get('company_id');
    $isSuperAdmin = $sessionCompanyId == 0;
?>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="dtTax table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Rate (%)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- ADD MODAL -->
    <div class="modal fade" id="modalAddTax" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formAddTax">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Add New Tax Code
                        </h5>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="row">

                            <!-- TAX CODE -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    Tax Code *
                                </label>

                                <input type="text"
                                       name="tax_code"
                                       class="form-control"
                                       required>
                            </div>

                            <!-- TAX NAME -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    Tax Name *
                                </label>

                                <input type="text"
                                       name="tax_name"
                                       class="form-control"
                                       required>
                            </div>

                            <!-- RATE -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    Rate (%) *
                                </label>

                                <input type="number"
                                       step="0.01"
                                       name="tax_rate"
                                       class="form-control"
                                       required>
                            </div>

                        </div>

                        <div class="row">

                            <!-- TAX TYPE -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    Tax Type *
                                </label>

                                <select name="tax_type"
                                        class="form-select"
                                        required>

                                    <option value="ppn">
                                        PPN
                                    </option>

                                    <option value="withholding">
                                        Withholding
                                    </option>

                                    <option value="pb1">
                                        PB1
                                    </option>

                                    <option value="fee">
                                        Fee
                                    </option>

                                </select>
                            </div>

                            <!-- DIRECTION -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    Tax Direction
                                </label>

                                <select name="tax_direction"
                                        class="form-select">

                                    <option value="input">
                                        Input
                                    </option>

                                    <option value="output">
                                        Output
                                    </option>

                                    <option value="both" selected>
                                        Both
                                    </option>

                                </select>
                            </div>

                            <!-- COA -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    COA Account
                                </label>

                                <select name="coa_account_id"
                                        class="form-select">

                                    <option value="">
                                        -- Select COA --
                                    </option>

                                    <?php foreach ($coa as $c): ?>

                                    <option value="<?= $c['id'] ?>">
                                        <?= $c['account_code'] ?> - <?= $c['account_name'] ?>
                                    </option>

                                    <?php endforeach; ?>

                                </select>
                            </div>

                        </div>

                        <div class="row">

                            <!-- INCLUDED -->
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Included Tax
                                </label>

                                <select name="is_included"
                                        class="form-select">

                                    <option value="0" selected>
                                        No
                                    </option>

                                    <option value="1">
                                        Yes
                                    </option>

                                </select>

                            </div>

                            <!-- CREDITABLE -->
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Creditable
                                </label>

                                <select name="is_creditable"
                                        class="form-select">

                                    <option value="1" selected>
                                        Yes
                                    </option>

                                    <option value="0">
                                        No
                                    </option>

                                </select>

                            </div>

                            <!-- STATUS -->
                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Status
                                </label>

                                <select name="is_active"
                                        class="form-select">

                                    <option value="1" selected>
                                        Active
                                    </option>

                                    <option value="0">
                                        Inactive
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-label-secondary"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit"
                                class="btn btn-primary">
                            Save
                        </button>

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

    let dt_table = $('.dtTax'), dt_tax;

    if (dt_table.length) {

        dt_tax = dt_table.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?= base_url('tax/datatable') ?>",
                type: "POST",
                data: d => {
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                }
            },
            columns: [
                { data: null },
                { data: 'no' },
                { data: 'tax_code' },
                { data: 'tax_name' },
                { data: 'tax_rate' },
                { data: 'status' },
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
            lengthMenu: [10,25,50,100],
            buttons: [
                {
                    extend: 'collection',
                    className: 'btn btn-label-primary dropdown-toggle me-2 waves-effect waves-light',
                    text: '<i class="ti ti-file-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                    buttons: [
                        { extend: 'print', className: 'dropdown-item', exportOptions: { columns: [1,2,3,4,5] } },
                        { extend: 'csv',   className: 'dropdown-item', exportOptions: { columns: [1,2,3,4,5] } },
                        { extend: 'pdf',   className: 'dropdown-item', exportOptions: { columns: [1,2,3,4,5] } }
                    ]
                },
                {
                    text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-sm-inline-block">Add Tax Code</span>',
                    className: 'create-new btn btn-primary waves-effect waves-light',
                    action: function () {
                        $('#modalAddTax').modal('show');
                    }
                }
            ],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            let data = row.data();
                            return 'Details of ' + data.tax_name;
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

        $('div.head-label').html('<h5 class="card-title mb-0">Tax Code List</h5>');
    }

    // Submit Add Tax
    $('#formAddTax').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        Swal.fire({
            title: 'Add new tax code?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(result => {

            if (result.isConfirmed) {

                $.ajax({
                    url: "<?= base_url('tax/store') ?>",
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

                            $('#modalAddTax').modal('hide');
                            $('.dtTax').DataTable().ajax.reload(null, false);
                            $('#formAddTax')[0].reset();

                        } else {
                            Swal.fire('Failed', res.message, 'error');
                        }
                    }
                });
            }
        });
    });

});
</script>

<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="dtTransaction table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Reference No</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- ADD MODAL -->
    <div class="modal fade" id="modalAddTransaction" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formAddTransaction">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Transaction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Transaction Date *</label>
                                <input type="date" name="trx_date" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reference No *</label>
                                <input type="text" name="reference_no" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Transaction Type *</label>
                                <select name="trx_type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <?php foreach($trxTypes as $type): ?>
                                        <option value="<?= esc($type['trx_type']) ?>">
                                            <?= ucfirst(esc($type['trx_type'])) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Amount *</label>
                                <input type="number" name="amount" class="form-control" required>
                            </div>
                        </div>

                        <!-- Hidden Company & Branch (optional kalau sudah session) -->
                        <input type="hidden" name="company_id" value="<?= session('company_id') ?>">
                        <input type="hidden" name="branch_id" value="<?= session('branch_id') ?>">

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Transaction</button>
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

    let dt_table = $('.dtTransaction'), dt_transaction;

    if (dt_table.length) {

        dt_transaction = dt_table.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?= base_url('transaction/datatable') ?>",
                type: "POST",
                data: d => {
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                }
            },
            columns: [
                { data: null },
                { data: 'no' },
                { data: 'reference_no' },
                { data: 'date' },
                { data: 'type' },
                { data: 'amount' },
                { data: 'action' }
            ],
            columnDefs: [
                {
                    className: 'control',
                    orderable: false,
                    searchable: false,
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
            order: [[2, 'desc']],
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
            buttons: [
                {
                    text: '<i class="ti ti-plus me-sm-1"></i> <span>Create Transaction</span>',
                    className: 'btn btn-primary',
                    action: function () {
                        $('#modalAddTransaction').modal('show');
                    }
                }
            ]
        });

        $('div.head-label').html('<h5 class="card-title mb-0">Transaction List</h5>');
    }

    // SUBMIT TRANSACTION
    $('#formAddTransaction').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        Swal.fire({
            title: 'Create transaction?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(result => {

            if (result.isConfirmed) {

                $.ajax({
                    url: "<?= base_url('transaction/store') ?>",
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

                            $('#modalAddTransaction').modal('hide');
                            dt_transaction.ajax.reload(null, false);
                            $('#formAddTransaction')[0].reset();

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

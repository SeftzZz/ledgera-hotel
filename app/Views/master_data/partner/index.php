<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="dtPartner table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- ADD MODAL -->
    <div class="modal fade" id="modalAddPartner" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formAddPartner">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Business Partner</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Code *</label>
                                <input type="text" name="partner_code" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" name="partner_name" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Type *</label>
                                <select name="partner_type" class="form-select" required>
                                    <option selected value="Vendor">Vendor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
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

$(function () {

    let dt_table = $('.dtPartner'), dt_partner;

    if (dt_table.length) {

        dt_partner = dt_table.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?= base_url('partner/datatable') ?>",
                type: "POST",
                data: d => {
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                }
            },
            columns: [
              { data: null },
              { data: null },
              { data: 'kode' },
              { data: 'name' },
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
                  render: function (data, type, full, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                  }
                },
                {
                  targets: 4,
                  render: function (data) {

                    return data === 'Aktif'
                      ? '<span class="badge bg-label-success">Aktif</span>'
                      : '<span class="badge bg-label-danger">Non Aktif</span>';
                  }
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
                        { extend: 'print', className: 'dropdown-item', exportOptions: { columns: [1,2,3,4] } },
                        { extend: 'csv',   className: 'dropdown-item', exportOptions: { columns: [1,2,3,4] } },
                        { extend: 'pdf',   className: 'dropdown-item', exportOptions: { columns: [1,2,3,4] } }
                    ]
                },
                {
                    text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-sm-inline-block">Add Partner</span>',
                    className: 'create-new btn btn-primary waves-effect waves-light',
                    action: function () {
                        $('#modalAddPartner').modal('show');
                    }
                }
            ],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            let data = row.data();
                            return 'Details of ' + data.partner_name;
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

        $('div.head-label').html('<h5 class="card-title mb-0">Business Partner List</h5>');
    }

    // Submit Add Partner
    $('#formAddPartner').on('submit', function(e){
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        Swal.fire({
            title: 'Add new partner?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(result => {

            if (result.isConfirmed) {

                $.ajax({
                    url: "<?= base_url('partner/store') ?>",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(res){

                        if(res.status){

                            Swal.fire({
                                icon: 'success',
                                title: 'Saved',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#modalAddPartner').modal('hide');
                            dt_partner.ajax.reload(null,false);
                            $('#formAddPartner')[0].reset();
                        }
                    }
                });
            }
        });

    });

});
</script>

<?= $this->endSection() ?>

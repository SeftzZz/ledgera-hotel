<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="dtApproval table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Journal No</th>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<link rel="stylesheet" href="<?= base_url('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') ?>" />
<link rel="stylesheet" href="<?= base_url('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') ?>" />
<script src="<?= base_url('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') ?>"></script>

<script>
'use strict';

$(function () {

    let dt_table = $('.dtApproval'), dt_approval;

    if (dt_table.length) {

        dt_approval = dt_table.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?= base_url('approval/datatable') ?>",
                type: "POST",
                data: d => {
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                }
            },
            columns: [
                { data: null },
                { data: 'no' },
                { data: 'journal_no' },
                { data: 'journal_date' },
                { data: 'description' },
                { data: 'total_amount' },
                { data: 'status' },
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
            order: [[3, 'desc']],
            displayLength: 10,
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

        $('div.head-label').html('<h5 class="card-title mb-0">Journal Approval</h5>');
    }

    // ==============================
    // APPROVE
    // ==============================
    $(document).on('click', '.btn-approve', function(){

        let id = $(this).data('id');

        Swal.fire({
            title: 'Approve this journal?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, approve',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(result => {

            if(result.isConfirmed){

                $.ajax({
                    url: "<?= base_url('approval/approve') ?>/" + id,
                    type: "POST",
                    data: {
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(res){

                        if(res.status){

                            Swal.fire({
                                icon: 'success',
                                title: 'Approved',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            dt_approval.ajax.reload(null,false);

                        } else {
                            Swal.fire('Failed', res.message, 'error');
                        }
                    }
                });

            }
        });

    });

    // ==============================
    // REJECT
    // ==============================
    $(document).on('click', '.btn-reject', function(){

        let id = $(this).data('id');

        Swal.fire({
            title: 'Reject this journal?',
            input: 'textarea',
            inputLabel: 'Reason',
            inputPlaceholder: 'Enter rejection reason...',
            showCancelButton: true,
            confirmButtonText: 'Reject',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(result => {

            if(result.isConfirmed){

                $.ajax({
                    url: "<?= base_url('approval/reject') ?>/" + id,
                    type: "POST",
                    data: {
                        reason: result.value,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(res){

                        if(res.status){

                            Swal.fire({
                                icon: 'success',
                                title: 'Rejected',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            dt_approval.ajax.reload(null,false);

                        } else {
                            Swal.fire('Failed', res.message, 'error');
                        }
                    }
                });

            }
        });

    });

    // ==============================
    // HISTORY
    // ==============================
    $(document).on('click', '.btn-history', function(){

        let id = $(this).data('id');

        $.ajax({
            url: "<?= base_url('approval/history') ?>/" + id,
            type: "POST",
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(res){

                if(!res.status){
                    Swal.fire('Error','Failed to load history','error');
                    return;
                }

                let timelineHtml = '';

                if(res.data.length === 0){
                    timelineHtml = '<p class="text-muted">No approval history.</p>';
                } else {

                    res.data.forEach(function(item){

                        let badge = '';
                        if(item.status === 'approved'){
                            badge = '<span class="badge bg-success">Approved</span>';
                        } else if(item.status === 'rejected'){
                            badge = '<span class="badge bg-danger">Rejected</span>';
                        } else {
                            badge = '<span class="badge bg-warning">Pending</span>';
                        }

                        timelineHtml += `
                            <div style="margin-bottom:15px;padding:10px;border-left:3px solid #7367f0">
                                <strong>Step ${item.step_order ?? '-'}</strong><br>
                                ${badge}<br>
                                <small>
                                    By: ${item.name ?? '-'} <br>
                                    At: ${item.approved_at ?? '-'}
                                </small>
                                <br>
                                <em>${item.note ?? ''}</em>
                            </div>
                        `;
                    });
                }

                Swal.fire({
                    title: 'Approval History',
                    html: timelineHtml,
                    width: 600,
                    confirmButtonText: 'Close'
                });

            }
        });

    });
});
</script>

<?= $this->endSection() ?>
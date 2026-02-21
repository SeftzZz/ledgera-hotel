<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Closing Period</h5>
        </div>

        <div class="card-datatable table-responsive pt-0">
            <table class="dtClosing table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Period</th>
                        <th>Status</th>
                        <th>Closed At</th>
                        <th>Action</th>
                    </tr>
                </thead>
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

$(function(){

    let table = $('.dtClosing').DataTable({
        ajax: {
            url: "<?= base_url('closing/datatable') ?>",
            type: "POST",
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            }
        },
        columns: [
            { data: 'no' },
            { data: 'period' },
            { data: 'status' },
            { data: 'closed_at' },
            { data: 'action' }
        ]
    });

    // CLOSE
    $(document).on('click', '.btn-close', function(){

        let id = $(this).data('id');

        Swal.fire({
            title: 'Close this period?',
            text: 'After closing, journal cannot be modified.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, close'
        }).then(result => {

            if(result.isConfirmed){

                $.post("<?= base_url('closing/close') ?>/"+id, {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                }, function(res){

                    if(res.status){
                        Swal.fire('Success', res.message, 'success');
                        table.ajax.reload();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }

                }, 'json');
            }

        });

    });

    // REOPEN
    $(document).on('click', '.btn-open', function(){

        let id = $(this).data('id');

        Swal.fire({
            title: 'Reopen this period?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, reopen'
        }).then(result => {

            if(result.isConfirmed){

                $.post("<?= base_url('closing/open') ?>/"+id, {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                }, function(res){

                    Swal.fire('Success', res.message, 'success');
                    table.ajax.reload();

                }, 'json');
            }

        });

    });

});
</script>

<?= $this->endSection() ?>
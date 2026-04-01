<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="dtJournal table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Journal No</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- JOURNAL VIEW MODAL -->
    <div class="modal fade" id="modalViewJournal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Journal Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <strong>Journal No:</strong> 
                        <span id="viewJournalNo"></span>
                    </div>

                    <div class="mb-3">
                        <strong>Date:</strong> 
                        <span id="viewJournalDate"></span>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                            </tr>
                        </thead>
                        <tbody id="journalDetailBody"></tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<link rel="stylesheet" href="<?= base_url('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') ?>" />
<script src="<?= base_url('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') ?>"></script>

<script>
'use strict';

$(function () {

    let dt = $('.dtJournal').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "<?= base_url('journal/datatable') ?>",
            type: "POST",
            data: d => {
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
            }
        },
        columns: [
            { data: null },
            { data: 'no' },
            { data: 'journal_no' },
            { data: 'date' },
            { data: 'description' },
            { data: 'status' },
            { data: 'action' }
        ],
        columnDefs: [
            { targets: 0, orderable:false, searchable:false, render:()=>'' },
            { targets: -1, orderable:false, searchable:false }
        ],
        order: [[2,'desc']]
    });

    $(document).on('click', '.btn-post', function(){

        let id = $(this).data('id');

        if(!confirm('Post this journal?')) return;

        $.post("<?= base_url('journal/post') ?>/"+id, {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(res){

            if(res.status){
                alert(res.message);
                dt.ajax.reload();
            } else {
                alert(res.message);
            }

        }, 'json');

    });

    $(document).on('click', '.btn-view', function(){

        let id = $(this).data('id');

        $.get("<?= base_url('journal/detail') ?>/"+id, function(res){

            if(res.status){

                $('#viewJournalNo').text(res.header.journal_no);
                $('#viewJournalDate').text(res.header.journal_date);

                let html = '';
                let totalDebit = 0;
                let totalCredit = 0;

                // Urutkan: akun utama dulu, lalu akun pajak
                let sorted = res.details.sort((a, b) => {
                    let aIsTax = a.account_name.toLowerCase().includes('ppn') || 
                                 a.account_name.toLowerCase().includes('pph') || 
                                 a.account_name.toLowerCase().includes('pb1');
                    let bIsTax = b.account_name.toLowerCase().includes('ppn') || 
                                 b.account_name.toLowerCase().includes('pph') ||
                                 b.account_name.toLowerCase().includes('pb1');

                    return aIsTax - bIsTax; // tax ke bawah
                });

                sorted.forEach(d => {

                    let debit = parseFloat(d.debit);
                    let credit = parseFloat(d.credit);

                    totalDebit += debit;
                    totalCredit += credit;

                    let isTax = d.account_name.toLowerCase().includes('ppn') ||
                                d.account_name.toLowerCase().includes('pph') ||
                                d.account_name.toLowerCase().includes('pb1');

                    let style = isTax 
                        ? 'padding-left:25px; font-style:italic; color:#b8860b;' 
                        : '';

                    html += `
                        <tr>
                            <td style="${style}">${d.account_name}</td>
                            <td class="text-end">${debit.toLocaleString()}</td>
                            <td class="text-end">${credit.toLocaleString()}</td>
                        </tr>
                    `;
                });

                // Garis total
                html += `
                    <tr style="border-top:2px solid #000;">
                        <td><strong>Total</strong></td>
                        <td class="text-end"><strong>${totalDebit.toLocaleString()}</strong></td>
                        <td class="text-end"><strong>${totalCredit.toLocaleString()}</strong></td>
                    </tr>
                `;

                $('#journalDetailBody').html(html);
                $('#modalViewJournal').modal('show');

            } else {
                alert(res.message);
            }

        }, 'json');

    });
});
</script>

<?= $this->endSection() ?>

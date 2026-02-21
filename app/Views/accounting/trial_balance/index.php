<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h4>Trial Balance - <?= $year ?></h4>
        </div>

        <div class="card-body">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="120">Code</th>
                        <th>Account</th>
                        <th width="150">Type</th>
                        <th width="200" class="text-end">Debit</th>
                        <th width="200" class="text-end">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?= $row['code'] ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= ucfirst($row['type']) ?></td>
                        <td class="text-end">
                            <?= number_format($row['debit'],2) ?>
                        </td>
                        <td class="text-end">
                            <?= number_format($row['credit'],2) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end">TOTAL</td>
                        <td class="text-end"><?= number_format($grandDebit,2) ?></td>
                        <td class="text-end"><?= number_format($grandCredit,2) ?></td>
                    </tr>
                </tfoot>
            </table>

            <?php if ($grandDebit != $grandCredit): ?>
                <div class="alert alert-danger mt-3">
                    Trial Balance NOT BALANCED!
                </div>
            <?php else: ?>
                <div class="alert alert-success mt-3">
                    Trial Balance Balanced ✔
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h4>Profit & Loss - <?= $year ?></h4>
        </div>

        <div class="card-body">

            <!-- REVENUE -->
            <h5 class="fw-bold">Revenue</h5>
            <table class="table table-sm">
                <?php foreach($revenue as $r): ?>
                    <tr>
                        <td><?= $r['account_name'] ?></td>
                        <td class="text-end">
                            <?= number_format($r['balance'],2) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="fw-bold border-top">
                    <td>Total Revenue</td>
                    <td class="text-end">
                        <?= number_format($totalRevenue,2) ?>
                    </td>
                </tr>
            </table>

            <!-- COGS -->
            <h5 class="fw-bold mt-4">Cost of Goods Sold</h5>
            <table class="table table-sm">
                <?php foreach($cogs as $c): ?>
                    <tr>
                        <td><?= $c['account_name'] ?></td>
                        <td class="text-end">
                            <?= number_format($c['balance'],2) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="fw-bold border-top">
                    <td>Total COGS</td>
                    <td class="text-end">
                        <?= number_format($totalCogs,2) ?>
                    </td>
                </tr>
            </table>

            <h5 class="fw-bold mt-3">
                Gross Profit: <?= number_format($grossProfit,2) ?>
            </h5>

            <!-- EXPENSE -->
            <h5 class="fw-bold mt-4">Operating Expenses</h5>
            <table class="table table-sm">
                <?php foreach($expense as $e): ?>
                    <tr>
                        <td><?= $e['account_name'] ?></td>
                        <td class="text-end">
                            <?= number_format($e['balance'],2) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="fw-bold border-top">
                    <td>Total Expenses</td>
                    <td class="text-end">
                        <?= number_format($totalExpense,2) ?>
                    </td>
                </tr>
            </table>

            <h4 class="fw-bold mt-4">
                Net Profit: <?= number_format($netProfit,2) ?>
            </h4>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
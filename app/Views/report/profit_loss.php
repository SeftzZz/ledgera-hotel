<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-xxl container-p-y">

    <div class="card p-4">

        <h5>Profit & Loss - <?= $month ?> / <?= $year ?></h5>
        <hr>

        <?php
        $revenue = 0;
        $cogs = 0;
        $expense = 0;

        foreach($rows as $r){
            if($r['account_type'] == 'revenue') $revenue += $r['balance'];
            if($r['account_type'] == 'cogs') $cogs += $r['balance'];
            if($r['account_type'] == 'expense') $expense += $r['balance'];
        }

        $grossProfit = $revenue - $cogs;
        $netProfit   = $grossProfit - $expense;
        ?>

        <table class="table table-bordered">
            <tbody>

                <tr class="table-light">
                    <th colspan="2">Revenue</th>
                </tr>
                <?php foreach($rows as $r): ?>
                    <?php if($r['account_type'] == 'revenue'): ?>
                        <tr>
                            <td><?= esc($r['account_name']) ?></td>
                            <td class="text-end"><?= number_format($r['balance'],2) ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                <tr class="fw-bold">
                    <td>Total Revenue</td>
                    <td class="text-end"><?= number_format($revenue,2) ?></td>
                </tr>

                <tr class="table-light">
                    <th colspan="2">Cost of Goods Sold</th>
                </tr>
                <?php foreach($rows as $r): ?>
                    <?php if($r['account_type'] == 'cogs'): ?>
                        <tr>
                            <td><?= esc($r['account_name']) ?></td>
                            <td class="text-end"><?= number_format($r['balance'],2) ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                <tr class="fw-bold">
                    <td>Total COGS</td>
                    <td class="text-end"><?= number_format($cogs,2) ?></td>
                </tr>

                <tr class="fw-bold table-warning">
                    <td>Gross Profit</td>
                    <td class="text-end"><?= number_format($grossProfit,2) ?></td>
                </tr>

                <tr class="table-light">
                    <th colspan="2">Operating Expense</th>
                </tr>
                <?php foreach($rows as $r): ?>
                    <?php if($r['account_type'] == 'expense'): ?>
                        <tr>
                            <td><?= esc($r['account_name']) ?></td>
                            <td class="text-end"><?= number_format($r['balance'],2) ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                <tr class="fw-bold">
                    <td>Total Expense</td>
                    <td class="text-end"><?= number_format($expense,2) ?></td>
                </tr>

                <tr class="fw-bold table-success">
                    <td>Net Profit</td>
                    <td class="text-end"><?= number_format($netProfit,2) ?></td>
                </tr>

            </tbody>
        </table>

    </div>

</div>

<?= $this->endSection() ?>
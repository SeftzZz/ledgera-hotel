<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h4>Balance Sheet - <?= $year ?></h4>
        </div>

        <div class="card-body">

            <div class="row">

                <!-- LEFT COLUMN - ASSET -->
                <div class="col-md-6">
                    <h5 class="fw-bold mb-3">Assets</h5>

                    <table class="table table-sm">
                        <?php foreach ($assets as $a): ?>
                            <tr>
                                <td><?= $a['account_name'] ?></td>
                                <td class="text-end">
                                    <?= number_format($a['balance'],2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr class="fw-bold border-top">
                            <td>Total Assets</td>
                            <td class="text-end">
                                <?= number_format($totalAsset,2) ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- RIGHT COLUMN - L + E -->
                <div class="col-md-6">
                    <h5 class="fw-bold mb-3">Liabilities</h5>

                    <table class="table table-sm mb-4">
                        <?php foreach ($liabilities as $l): ?>
                            <tr>
                                <td><?= $l['account_name'] ?></td>
                                <td class="text-end">
                                    <?= number_format($l['balance'],2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr class="fw-bold border-top">
                            <td>Total Liabilities</td>
                            <td class="text-end">
                                <?= number_format($totalLiability,2) ?>
                            </td>
                        </tr>
                    </table>

                    <h5 class="fw-bold mb-3">Equity</h5>

                    <table class="table table-sm">
                        <?php foreach ($equity as $e): ?>
                            <tr>
                                <td><?= $e['account_name'] ?></td>
                                <td class="text-end">
                                    <?= number_format($e['balance'],2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr class="fw-bold border-top">
                            <td>Total Equity</td>
                            <td class="text-end">
                                <?= number_format($totalEquity,2) ?>
                            </td>
                        </tr>

                        <tr class="fw-bold bg-light">
                            <td>Total L + E</td>
                            <td class="text-end">
                                <?= number_format($totalLiability + $totalEquity,2) ?>
                            </td>
                        </tr>
                    </table>
                </div>

            </div>

            <?php if ($totalAsset != ($totalLiability + $totalEquity)): ?>
                <div class="alert alert-danger mt-4">
                    Balance Sheet NOT BALANCED!
                </div>
            <?php else: ?>
                <div class="alert alert-success mt-4">
                    Balance Sheet Balanced ✔
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
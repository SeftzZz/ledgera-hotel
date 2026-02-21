<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
<div class="container-xxl flex-grow-1 container-p-y">
<div class="row">

<!-- ========================= -->
<!-- ACCOUNTING OVERVIEW -->
<!-- ========================= -->
<div class="col-lg-6 mb-4">
    <div class="card bg-primary text-white h-100">
        <div class="card-body">
            <h5 class="mb-1 text-white">Accounting Overview</h5>
            <small><?= date('F', mktime(0,0,0,$month,1)) ?> <?= $year ?></small>

            <div class="row mt-4">
                <div class="col-4">
                    <h6 class="text-white">Revenue</h6>
                    <h4 class="text-white"><?= number_format($revenue,2) ?></h4>
                </div>
                <div class="col-4">
                    <h6 class="text-white">Expense</h6>
                    <h4 class="text-white"><?= number_format($expense,2) ?></h4>
                </div>
                <div class="col-4">
                    <h6 class="text-white">Net Profit</h6>
                    <h4 class="text-white"><?= number_format($profit,2) ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================= -->
<!-- PROFIT OVERVIEW -->
<!-- ========================= -->
<div class="col-lg-3 col-sm-6 mb-4">
    <div class="card">
        <div class="card-header">
            <small class="text-muted">Profit Overview</small>
            <h4 class="card-title mb-0"><?= number_format($profit,2) ?></h4>
        </div>
        <div class="card-body">
            <div class="progress" style="height: 8px">
                <div class="progress-bar bg-success"
                     style="width: <?= $revenue > 0 ? round(($profit/$revenue)*100,2) : 0 ?>%">
                </div>
            </div>
            <small class="text-muted mt-2 d-block">
                Margin <?= $revenue > 0 ? round(($profit/$revenue)*100,2) : 0 ?>%
            </small>
        </div>
    </div>
</div>

<!-- ========================= -->
<!-- PENDING APPROVAL -->
<!-- ========================= -->
<div class="col-lg-3 col-sm-6 mb-4">
    <div class="card">
        <div class="card-body">
            <div class="card-icon">
                <span class="badge bg-label-warning rounded-pill p-2">
                    <i class="ti ti-clock ti-sm"></i>
                </span>
            </div>
            <h4 class="mt-2"><?= $pending ?></h4>
            <small>Pending Approvals</small>
        </div>
    </div>
</div>

<!-- ========================= -->
<!-- MONTHLY FINANCE CHART -->
<!-- ========================= -->
<div class="col-lg-8 mb-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Monthly Finance</h5>
            <small class="text-muted">Revenue vs Expense</small>
        </div>
        <div class="card-body">
            <canvas id="financeChart" height="120"></canvas>
        </div>
    </div>
</div>

<!-- ========================= -->
<!-- QUICK SUMMARY -->
<!-- ========================= -->
<div class="col-lg-4 mb-4">
    <div class="card h-100">
        <div class="card-header">
            <h5 class="mb-0">Quick Summary</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Total Revenue</strong>
                <div><?= number_format($revenue,2) ?></div>
            </div>

            <div class="mb-3">
                <strong>Total Expense</strong>
                <div><?= number_format($expense,2) ?></div>
            </div>

            <div>
                <strong>Net Profit</strong>
                <div class="<?= $profit >= 0 ? 'text-success' : 'text-danger' ?>">
                    <?= number_format($profit,2) ?>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('financeChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Revenue', 'Expense', 'Net Profit'],
        datasets: [{
            label: 'Amount',
            data: [
                <?= $revenue ?>,
                <?= $expense ?>,
                <?= $profit ?>
            ],
            backgroundColor: [
                '#28c76f',
                '#ea5455',
                '#7367f0'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    }
});
</script>

<?= $this->endSection() ?>
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
                    <h5 class="mb-1 text-white">Accounting (Actual)</h5>
                    <small><?= date('F', mktime(0,0,0,$month,1)) ?> <?= $year ?></small>

                    <div class="row mt-4">
                        <div class="col-6">
                            <h6 class="text-white">Revenue</h6>
                            <h4 class="text-white" id="revenue-value"><?= number_format($revenue,2) ?></h4>
                            <!-- COLLECTION RATE -->
                            <div class="mt-3">
                                <small>Target Rp</small>
                                <div class="progress" style="height:10px;">
                                    <div class="progress-bar bg-success"
                                         style="width: <?= round($collectionRate,2) ?>%">
                                    </div>
                                </div>
                                <small><?= round($collectionRate,2) ?>%</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="text-white">Expense</h6>
                            <h4 class="text-white" id="expense-value"><?= number_format($expense,2) ?></h4>
                            <!-- COLLECTION RATE -->
                            <div class="mt-3">
                                <small>Target Rp</small>
                                <div class="progress" style="height:10px;">
                                    <div class="progress-bar bg-success"
                                         style="width: <?= round($collectionRate,2) ?>%">
                                    </div>
                                </div>
                                <small><?= round($collectionRate,2) ?>%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ========================= -->
        <!-- ESTIMATED OVERVIEW -->
        <!-- ========================= -->
        <div class="col-lg-6 mb-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h5 class="mb-1 text-white">Estimated Overview</h5>
                    <small><?= date('F', mktime(0,0,0,$month,1)) ?> <?= $year ?></small>

                    <div class="row mt-4">
                        <div class="col-4">
                            <h6 class="text-white">Total Order</h6>
                            <h4 class="text-white" id="estimated-value">
                                Rp <?= number_format($estimated,0,',','.') ?>
                            </h4>
                        </div>

                        <div class="col-4">
                            <h6 class="text-white">Cash In</h6>
                            <h4 class="text-white" id="cash-value">
                                Rp <?= number_format($actualCash,0,',','.') ?>
                            </h4>
                        </div>

                        <div class="col-4">
                            <h6 class="text-white">Outstanding</h6>
                            <h4 class="text-white" id="outstanding-value">
                                Rp <?= number_format($outstanding,0,',','.') ?>
                            </h4>
                        </div>
                    </div>

                    <!-- COLLECTION RATE -->
                    <div class="mt-3">
                        <small>Collection Rate</small>
                        <div class="progress" style="height:10px;">
                            <div class="progress-bar bg-success"
                                 style="width: <?= round($collectionRate,2) ?>%">
                            </div>
                        </div>
                        <small><?= round($collectionRate,2) ?>%</small>
                    </div>

                </div>
            </div>
        </div>

        <!-- ========================= -->
        <!-- MONTHLY FINANCE CHART -->
        <!-- ========================= -->
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Finance</h5>
                    <small class="text-muted">Revenue vs Expense</small>
                </div>
                <div class="card-body">
                    <div id="shipmentStatisticsChart"></div>
                </div>
            </div>
        </div>

        <!-- ========================= -->
        <!-- PROFIT OVERVIEW (MONTH) -->
        <!-- ========================= -->
        <div class="col-lg-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <small class="text-muted">End Of Month Overview</small>
                    <h4 class="card-title mb-0" id="profit-month">
                        Rp <?= number_format($profit,0,',','.') ?>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="progress" style="height: 8px">
                        <div class="progress-bar bg-success" id="profit-margin-bar"
                             style="width: <?= $revenue > 0 ? round(($profit/$revenue)*100,2) : 0 ?>%">
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block" id="profit-margin-text">
                        Margin <?= $revenue > 0 ? round(($profit/$revenue)*100,2) : 0 ?>%
                    </small>
                </div>
            </div>
        </div>

        <!-- ========================= -->
        <!-- PROFIT END OF DAY -->
        <!-- ========================= -->
        <div class="col-lg-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <small class="text-muted">Today Profit</small>
                    <h4 class="card-title mb-0" id="profit-today">
                        Rp <?= number_format($todayProfit,0,',','.') ?>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="progress" style="height: 8px">
                        <div class="progress-bar bg-info"
                             style="width: <?= $todayRevenue > 0 ? round(($todayProfit/$todayRevenue)*100,2) : 0 ?>%">
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        Margin <?= $todayRevenue > 0 ? round(($todayProfit/$todayRevenue)*100,2) : 0 ?>%
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
                    <h4 class="mt-2" id="pending-count"><?= $pending ?></h4>
                    <small>Pending Approvals</small>
                </div>
            </div>
        </div>

        <!-- ========================= -->
        <!-- POSTED APPROVAL -->
        <!-- ========================= -->
        <div class="col-lg-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-icon">
                        <span class="badge bg-label-success rounded-pill p-2">
                            <i class="ti ti-check ti-sm"></i>
                        </span>
                    </div>
                    <h4 class="mt-2" id="posted-count"><?= $posted ?></h4>
                    <small>Posted Journals</small>
                </div>
            </div>
        </div>

        <!-- ========================= -->
        <!-- DEPARTMENT BUDGET -->
        <!-- ========================= -->
        <?php foreach ($departmentSummary as $dept): ?>
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-body">

                    <h5 class="mb-1 "><?= $dept['name'] ?> <badge class="badge bg-label-primary"><?= $dept['branch_name'] ?></badge></h5>
                    <small><?= date('F', mktime(0,0,0,$month,1)) ?> <?= $year ?></small>

                    <div class="row mt-4">

                        <div class="col-4">
                            <h6 class="">Target</h6>
                            <h4 class="">
                                Rp <?= number_format($dept['target'],0,',','.') ?>
                            </h4>
                        </div>

                        <div class="col-4">
                            <h6 class="">Estimated Revenue</h6>
                            <h4 class="">
                                Rp <?= number_format($dept['estimated'],0,',','.') ?>
                            </h4>
                        </div>

                        <div class="col-4">
                            <h6 class="">Variance</h6>
                            <h4 class="">
                                Rp <?= number_format($dept['target'] - $dept['estimated'],0,',','.') ?>
                            </h4>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <!-- SPEND RATIO -->
                            <div class="mt-3">
                                <small>Spend Ratio (<?= $dept['spend_ratio'] ?>%)</small>
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-success"
                                         style="width: <?= $dept['spend_ratio'] ?>%">
                                    </div>
                                </div>
                                <small>
                                    Rp <?= number_format(($dept['target'] * $dept['spend_ratio'] / 100),0,',','.') ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <!-- WORKER RATIO -->
                            <div class="mt-3">
                                <small>Worker Ratio (<?= $dept['worker_ratio'] ?>%)</small>
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-warning"
                                         style="width: <?= $dept['worker_ratio'] ?>%">
                                    </div>
                                </div>
                                <small>
                                    Rp <?= number_format(($dept['target'] * $dept['worker_ratio'] / 100),0,',','.') ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <!-- DW RATIO -->
                            <div class="mt-3">
                                <small>DW Ratio (<?= $dept['dw_ratio'] ?>%)</small>
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-warning"
                                         style="width: <?= $dept['dw_ratio'] ?>%">
                                    </div>
                                </div>
                                <small>
                                    Rp <?= number_format(($dept['target'] * $dept['dw_ratio'] / 100),0,',','.') ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <!-- SPEND LIMIT BY ESTIMATED + ACTUAL -->
                            <div class="mt-3">
                                <small>
                                    
                                </small>

                                <?php
                                    // ======================
                                    // LIMIT (BASED ON TARGET)
                                    // ======================
                                    $limit = ($dept['target'] * $dept['spend_ratio'] / 100);

                                    // ======================
                                    // ESTIMATED (EXISTING)
                                    // ======================
                                    $estimatedLimit = ($dept['estimated'] * $dept['spend_ratio'] / 100);

                                    // persen terhadap LIMIT (bukan target)
                                    $estimatedPercent = $limit > 0 
                                        ? ($estimatedLimit / $limit) * 100 
                                        : 0;

                                    // ======================
                                    // ACTUAL (NEW)
                                    // ======================
                                    $actual = $dept['expense'] ?? 0;

                                    // persen terhadap LIMIT (INI YANG BENAR)
                                    $actualPercent = $limit > 0
                                        ? ($actual / $limit) * 100
                                        : 0;
                                ?>

                                <!-- ======================
                                     ESTIMATED %
                                ====================== -->
                                <small>
                                    Spend Limit <?= number_format($estimatedPercent,2) ?>%
                                </small>

                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-warning"
                                         style="width: <?= min($estimatedPercent,100) ?>%">
                                    </div>
                                </div>

                                <div class="fs-6 pt-1">
                                    Rp <?= number_format($estimatedLimit,0,',','.') ?>
                                </div>

                                <!-- ======================
                                     ACTUAL %
                                ====================== -->
                                <div class="fw-bold fs-6 pt-2">
                                    Actual Spend <?= number_format($actualPercent,2) ?>%
                                </div>

                                <div class="progress" style="height:6px;">
                                    <div id="spend-percent-value" class="progress-bar <?= $actual > $limit ? 'bg-danger' : 'bg-success' ?>"
                                         style="width: <?= min($actualPercent,100) ?>%">
                                    </div>
                                </div>

                                <div class="fw-bold fs-6 pt-1" id="spend-value">
                                    Rp <?= number_format($actual,0,',','.') ?>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4">
                            <!-- WORKER LIMIT BY ESTIMATED -->
                            <div class="mt-3">
                                <small>
                                    Worker Limit
                                    <?= number_format(
                                        $dept['target'] > 0 
                                            ? (($dept['estimated'] * $dept['worker_ratio'] / 100) / $dept['target']) * 100 
                                            : 0
                                    ,2) ?>%
                                </small>

                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-warning"
                                         style="width: <?= $dept['target'] > 0 
                                            ? (($dept['estimated'] * $dept['worker_ratio'] / 100) / $dept['target']) * 100 
                                            : 0 ?>%">
                                    </div>
                                </div>

                                <div class="fs-6 pt-1">
                                    Rp <?= number_format(($dept['estimated'] * $dept['worker_ratio'] / 100),0,',','.') ?>
                                </div>

                                <!-- ======================
                                     ACTUAL WORKFORCE
                                ====================== -->
                                <div class="fw-bold fs-6 pt-2" id="workforce-percent-value">
                                    Actual Workforce <?= number_format($dept['actual_worker_percent'],2) ?>%
                                </div>

                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar <?= $dept['workforce'] > $dept['limit_worker'] ? 'bg-danger' : 'bg-success' ?>"
                                         style="width: <?= min($dept['actual_worker_percent'],100) ?>%">
                                    </div>
                                </div>

                                <div class="fw-bold fs-6 pt-1" id="workforce-value">
                                    Rp <?= number_format($dept['workforce'],0,',','.') ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <!-- WORKER LIMIT BY ESTIMATED -->
                            <div class="mt-3">
                                <small>
                                    DW Limit
                                    <?= number_format(
                                        $dept['target'] > 0 
                                            ? (($dept['estimated'] * $dept['dw_ratio'] / 100) / $dept['target']) * 100 
                                            : 0
                                    ,2) ?>%
                                </small>

                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-warning"
                                         style="width: <?= $dept['target'] > 0 
                                            ? (($dept['estimated'] * $dept['dw_ratio'] / 100) / $dept['target']) * 100 
                                            : 0 ?>%">
                                    </div>
                                </div>

                                <div class="fs-6 pt-1">
                                    Rp <?= number_format(($dept['estimated'] * $dept['dw_ratio'] / 100),0,',','.') ?>
                                </div>

                                <!-- ======================
                                     ACTUAL DW
                                ====================== -->
                                <div class="fw-bold fs-6 pt-2" id="daily_worker-percent-value">
                                    Actual DW <?= number_format($dept['actual_dw_percent'],2) ?>%
                                </div>

                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar <?= $dept['daily_worker'] > $dept['limit_dw'] ? 'bg-danger' : 'bg-success' ?>"
                                         style="width: <?= min($dept['actual_dw_percent'],100) ?>%">
                                    </div>
                                </div>

                                <div class="fw-bold fs-6 pt-1" id="workforce-value">
                                    Rp <?= number_format($dept['daily_worker'],0,',','.') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="col-lg-12 mb-4">
            <!-- Order List Table -->
            <div class="card h-100">
              <div class="card-datatable table-responsive">
                <table class="datatables-expense table border-top">
                  <thead>
                    <tr>
                      <th></th>
                      <th></th>
                      <th>Date</th>
                      <th>Department</th>
                      <th>Account</th>
                      <th>Item</th>
                      <th>Qty</th>
                      <th>Amount</th>
                      <th>Requester</th>
                      <th>PO</th>
                      <th>Journal</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
        </div>

        <!--/ Delivery Performance -->
        <!-- Reasons for delivery exceptions -->
        <?php foreach ($branches as $i => $branch): ?>
        <div class="col-lg-12 col-sm-6 mb-4">
          <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
              <div class="card-title mb-0">
                <h5 class="m-0 me-2"><?= $branch['branch_name'] ?></h5>
              </div>
            </div>
            <div class="card-body">
                <div style="display:flex; flex-wrap:wrap; gap:20px;">
                    <?php foreach ($branch['items'] as $j => $item): ?>
                        <div style="width:32%;">
                            <div style="justify-self: center;">
                                <label>Target: Rp <?= number_format($item['target']) ?></label>
                            </div>
                            <div id="deliveryExceptionsChart-<?= $i ?>-<?= $j ?>"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <!--/ Reasons for delivery exceptions -->

        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  window.dashboardData = {

    // ======================
    // ACCOUNTING
    // ======================
    revenue: <?= json_encode($revenue) ?>,
    expense: <?= json_encode($expense) ?>,
    cogs: <?= json_encode($cogs) ?>,
    profit: <?= json_encode($profit) ?>,

    // ======================
    // TODAY
    // ======================
    todayRevenue: <?= json_encode($todayRevenue) ?>,
    todayProfit: <?= json_encode($todayProfit) ?>,

    // ======================
    // ORDER
    // ======================
    estimated: <?= json_encode($estimated) ?>,
    actualCash: <?= json_encode($actualCash) ?>,
    outstanding: <?= json_encode($outstanding) ?>,

    // ======================
    // KPI
    // ======================
    collectionRate: <?= json_encode($collectionRate) ?>,

    // ======================
    // APPROVAL
    // ======================
    pending: <?= json_encode($pending) ?>,
    posted: <?= json_encode($posted) ?>,

    // ======================
    // BRANCH
    // ======================
    branches: <?= json_encode($branches) ?>,

    // ======================
    // DEPARTMENT
    // ======================
    departmentSummary: <?= json_encode($departmentSummary) ?>,

    // ======================
    // HISTORY
    // ======================
    historyLabels: <?= json_encode($historyLabels) ?>,
    historyRevenue: <?= json_encode($historyRevenue) ?>,
    historyCash: <?= json_encode($historyCash) ?>,
    historyOutstanding: <?= json_encode($historyOutstanding) ?>,

    // ======================
    // META
    // ======================
    title: <?= json_encode($title) ?>,
    month: <?= json_encode($month) ?>,
    year: <?= json_encode($year) ?>

  };

  console.log(window.dashboardData);
</script>
<script src="<?= base_url('assets/js/app-logistics-dashboard.js' ) ?>"></script>
<script src="<?= base_url('assets/js/app-expense-dashboard.js' ) ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {

  function initWS() {

    const token = window.jwtToken;

    if (!token) {
      console.warn('⏳ WAITING TOKEN...');
      return false;
    }

    // console.log('🔥 INIT WS START');
    // console.log('TOKEN:', token);

    const protocol = window.location.protocol === 'https:' ? 'wss' : 'ws';
    const wsUrl = `${protocol}://${window.location.hostname}?token=${token}`;

    console.log('CONNECT TO:', wsUrl);

    const ws = new WebSocket(wsUrl);

    ws.onopen = () => {
      console.log('✅ WS Connected');
    };

    ws.onmessage = (event) => {
      try {
        const msg = JSON.parse(event.data);

        console.log('📡 WS EVENT:', msg);

        handleRealtimeUpdate(msg);

      } catch (err) {
        console.error('WS parse error', err);
      }
    };

    ws.onerror = (err) => {
      console.error('❌ WS Error FULL:', err);
    };

    ws.onclose = () => {
      console.warn('⚠️ WS Disconnected');
    };

    return true;
  }

  // retry sampai token ada
  const interval = setInterval(() => {
    if (initWS()) {
      clearInterval(interval);
    }
  }, 500);

})();
</script>
<script>
function handleRealtimeUpdate(msg) {

  switch (msg.type) {

    case 'transaction_created':
    case 'journal_posted':
    case 'order_paid':
      refreshDashboard();
      break;

    default:
      console.log('Unhandled event:', msg.type);
  }

}
</script>
<script>
async function refreshDashboard() {
  try {

    const res = await fetch('/dashboard/data', {
      headers: {
        Authorization: 'Bearer ' + localStorage.getItem('jwtToken')
      }
    });

    const data = await res.json();

    updateUI(data);

  } catch (err) {
    console.error('Refresh error:', err);
  }
}
</script>
<script>
function updateUI(data) {

  // ======================
  // ACCOUNTING
  // ======================
  document.getElementById('revenue-value').innerText =
    formatRupiah(data.revenue);

  document.getElementById('expense-value').innerText =
    formatRupiah(data.expense);

  // ======================
  // ESTIMATED
  // ======================
  document.getElementById('estimated-value').innerText =
    formatRupiah(data.estimated);

  document.getElementById('cash-value').innerText =
    formatRupiah(data.actualCash);

  document.getElementById('outstanding-value').innerText =
    formatRupiah(data.outstanding);

  // ======================
  // PROFIT
  // ======================
  document.getElementById('profit-month').innerText =
    formatRupiah(data.profit);
  
  const margin = data.revenue > 0
  ? ((data.profit / data.revenue) * 100)
  : 0;

  document.getElementById('profit-margin-bar').style.width = margin + '%';
  document.getElementById('profit-margin-text').innerText =
    'Margin ' + margin.toFixed(2) + '%';

  // ======================
  // TODAY PROFIT
  // ======================
  document.getElementById('profit-today').innerText =
    formatRupiah(data.todayProfit);

  // ======================
  // PENDING
  // ======================
  document.getElementById('pending-count').innerText =
    data.pending;

  // ======================
  // POSTED
  // ======================
  document.getElementById('posted-count').innerText =
    data.posted;

  const dept = data.departmentSummary?.[0] || {};

  document.getElementById('spend-value').innerText = formatRupiah(dept.expense || 0);

  document.getElementById('spend-percent-value').innerText = 'Actual Spend ' + (dept.actual_spend_percent || 0).toFixed(2) + '%';

  document.getElementById('workforce-value').innerText = formatRupiah(dept.workforce || 0);

  document.getElementById('workforce-percent-value').innerText = 'Actual Workforce ' + (dept.actual_worker_percent || 0).toFixed(2) + '%';

  document.getElementById('daily_worker-value').innerText = formatRupiah(dept.daily_worker || 0);

  document.getElementById('daily_worker-percent-value').innerText = 'Actual DW ' + (dept.actual_dw_percent || 0).toFixed(2) + '%';

  // ======================
  // UPDATE CHART
  // ======================
  updateShipmentChart(data);

}
</script>
<script>
function updateShipmentChart(data) {

  if (!window.shipmentChart) return;

  window.shipmentChart.updateOptions({
    xaxis: {
      categories: data.historyLabels
    }
  });

  window.shipmentChart.updateSeries([
    {
      name: 'Revenue',
      data: data.historyRevenue
    },
    {
      name: 'Cash In',
      data: data.historyCash
    },
    {
      name: 'Outstanding',
      data: data.historyOutstanding
    }
  ]);
}
</script>
<script>
function formatRupiah(num) {
  return 'Rp ' + Number(num).toLocaleString('id-ID');
}
</script>
<?= $this->endSection() ?>
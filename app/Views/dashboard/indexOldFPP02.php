                        <?= $this->extend('layouts/main') ?>
                        <?= $this->section('content') ?>
                            <div class="content-wrapper">
                                <div class="container-xxl flex-grow-1 container-p-y">
                                    <div class="row">
                                        <?php if (session()->get('user_role') === 'admin'): ?>
                                            <!-- ========================= -->
                                            <!-- ACCOUNTING OVERVIEW -->
                                            <!-- ========================= -->
                                            <div class="col-lg-6 mb-4">
                                                <div class="card bg-primary text-white h-100">
                                                    <div class="card-body">
                                                        <h5 class="mb-1 text-white">Accounting (Actual)</h5>
                                                        <small><?= date('F', mktime(0,0,0,$month,1)) ?> <?= $year ?></small>
                                                        <div class="row mt-4">
                                                            <div class="col-4">
                                                                <h6 class="text-white">Revenue</h6>
                                                                <h4 class="text-white"><?= number_format($revenue,2) ?></h4>
                                                                <!-- COLLECTION RATE -->
                                                                <div class="mt-3">
                                                                    <small>Target Rp</small>
                                                                    <div class="progress" style="height:6px;">
                                                                        <div class="progress-bar bg-success"
                                                                             style="width: <?= round($collectionRate,2) ?>%">
                                                                        </div>
                                                                    </div>
                                                                    <small><?= round($collectionRate,2) ?>%</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <h6 class="text-white">Expense</h6>
                                                                <h4 class="text-white"><?= number_format($expense,2) ?></h4>
                                                                <!-- COLLECTION RATE -->
                                                                <div class="mt-3">
                                                                    <small>Target Rp</small>
                                                                    <div class="progress" style="height:6px;">
                                                                        <div class="progress-bar bg-success"
                                                                             style="width: <?= round($collectionRate,2) ?>%">
                                                                        </div>
                                                                    </div>
                                                                    <small><?= round($collectionRate,2) ?>%</small>
                                                                </div>
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
                                                                <h4 class="text-white">
                                                                    Rp <?= number_format($estimated,0,',','.') ?>
                                                                </h4>
                                                            </div>

                                                            <div class="col-4">
                                                                <h6 class="text-white">Cash In</h6>
                                                                <h4 class="text-white">
                                                                    Rp <?= number_format($actualCash,0,',','.') ?>
                                                                </h4>
                                                            </div>

                                                            <div class="col-4">
                                                                <h6 class="text-white">Outstanding</h6>
                                                                <h4 class="text-white">
                                                                    Rp <?= number_format($outstanding,0,',','.') ?>
                                                                </h4>
                                                            </div>
                                                        </div>

                                                        <!-- COLLECTION RATE -->
                                                        <div class="mt-3">
                                                            <small>Collection Rate</small>
                                                            <div class="progress" style="height:6px;">
                                                                <div class="progress-bar bg-success"
                                                                     style="width: <?= round($collectionRate,2) ?>%">
                                                                </div>
                                                            </div>
                                                            <small><?= round($collectionRate,2) ?>%</small>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <!-- ========================= -->
                                            <!-- DEPT BUDGET -->
                                            <!-- ========================= -->
                                            <div class="col-lg-6 mb-4">
                                                <div class="card bg-primary text-white">
                                                    <div class="card-body">
                                                        <h5 class="mb-1 text-white">Department Bugdet</h5>
                                                        <small><?= date('F', mktime(0,0,0,$month,1)) ?> <?= $year ?></small>
                                                        <div class="row mt-4">
                                                            <div class="col-12">
                                                                <h6 class="text-white">Kitchen / Culinary</h6>
                                                                <h4 class="text-white"><?= number_format(80500000,2) ?></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- ========================= -->
                                            <!-- DEPT BUDGET USE -->
                                            <!-- ========================= -->
                                            <div class="col-lg-6 mb-4">
                                                <div class="card bg-info text-white">
                                                    <div class="card-body">
                                                        <h5 class="mb-1 text-white">Department Budget Use</h5>
                                                        <small><?= date('F', mktime(0,0,0,$month,1)) ?> <?= $year ?></small>
                                                        <div class="row mt-4">
                                                            <div class="col-12">
                                                                <h6 class="text-white">Kitchen / Culinary</h6>
                                                                <h4 class="text-white">
                                                                    Rp <?= number_format(21000000,0,',','.') ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- ========================= -->
                                        <!-- PROFIT OVERVIEW (MONTH) -->
                                        <!-- ========================= -->
                                        <div class="col-lg-3 col-sm-6 mb-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <small class="text-muted">End Of Month Overview</small>
                                                    <h4 class="card-title mb-0">
                                                        Rp <?= number_format($profit,0,',','.') ?>
                                                    </h4>
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
                                        <!-- PROFIT END OF DAY -->
                                        <!-- ========================= -->
                                        <div class="col-lg-6 col-sm-6 mb-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <small class="text-muted">Today Profit</small>
                                                    <h4 class="card-title mb-0">
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
                                                    <h4 class="mt-2"><?= $pending ?></h4>
                                                    <small>Pending Approvals</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!--/ Delivery Performance -->
                                        <!-- Reasons for delivery exceptions -->
                                        <?php foreach ($branchLabels as $i => $branch): ?>
                                            <div class="col-lg-4 col-sm-6 mb-4">
                                              <div class="card h-100">
                                                <div class="card-header d-flex align-items-center justify-content-between">
                                                  <div class="card-title mb-0">
                                                    <h5 class="m-0 me-2"><?= $branch ?></h5>
                                                  </div>
                                                </div>
                                                <div class="card-body">
                                                    <div id="deliveryExceptionsChart-<?= $i ?>" class="pt-md-4"></div>     
                                                </div>
                                              </div>
                                            </div>
                                        <?php endforeach; ?>
                                        <!--/ Reasons for delivery exceptions -->

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
                            <script>
                                window.dashboardData = {
                                    branchLabels: <?= json_encode($branchLabels) ?>,
                                    branchRevenue: <?= json_encode($branchRevenue) ?>,
                                    branchExpense: <?= json_encode($branchExpense) ?>,
                                    branchTargets: <?= json_encode($branchTargets) ?>,
                                    branchSW: <?= json_encode(array_values($branchSW)) ?>
                                };
                            </script>
                            <script src="<?= base_url('assets/js/app-logistics-dashboard.js' ) ?>"></script>
                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                            <script>
                                const ctx = document.getElementById('financeChart');

                                new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: ['Revenue (Accrual)', 'Cash In', 'Outstanding'],
                                        datasets: [{
                                            label: 'Amount',
                                            data: [
                                                <?= $revenue ?>,
                                                <?= $actualCash ?>,
                                                <?= $outstanding ?>
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
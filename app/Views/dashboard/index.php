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
                                                        <h4 class="text-white"><?= number_format($revenue,2) ?></h4>

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

                                                    <div class="col-6">
                                                        <h6 class="text-white">Expense</h6>
                                                        <h4 class="text-white"><?= number_format($expense,2) ?></h4>

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

                                    <!-- ========================= -->
                                    <!-- DEPARTMENT BUDGET -->
                                    <!-- ========================= -->
                                    <?php foreach ($departmentSummary as $dept): ?>
                                        <div class="col-lg-6 mb-4">
                                            <div class="card h-100">
                                                <div class="card-body">

                                                    <h5 class="mb-1"><?= $dept['name'] ?></h5>
                                                    <small><?= date('F', mktime(0,0,0,$month,1)) ?> <?= $year ?></small>

                                                    <div class="row mt-4">

                                                        <div class="col-4">
                                                            <h6>Target</h6>
                                                            <h4>Rp <?= number_format($dept['target'],0,',','.') ?></h4>
                                                        </div>

                                                        <div class="col-4">
                                                            <h6>Estimated Revenue</h6>
                                                            <h4>Rp <?= number_format($dept['estimated'],0,',','.') ?></h4>
                                                        </div>

                                                        <div class="col-4">
                                                            <h6>Gap</h6>
                                                            <h4>
                                                                Rp <?= number_format($dept['target'] - $dept['estimated'],0,',','.') ?>
                                                            </h4>
                                                        </div>

                                                    </div>

                                                    <!-- (dipotong di sini biar tidak terlalu panjang, tapi struktur sudah konsisten 4 spasi) -->

                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                </div>
                            </div>
                        </div>

                        <?= $this->endSection() ?>
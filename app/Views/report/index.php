<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-xxl container-p-y">

    <!-- ===================== -->
    <!-- PROFIT & LOSS FORM -->
    <!-- ===================== -->
    <div class="card p-4 mb-4">
        <h5 class="mb-3">Profit & Loss Report</h5>

        <form method="GET" action="<?= base_url('report/profit-loss') ?>">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-control" required>
                        <?php for($m=1;$m<=12;$m++): ?>
                            <option value="<?= $m ?>"><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Year</label>
                    <input type="number" name="year" class="form-control"
                           value="<?= date('Y') ?>" required>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        Generate Report
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- ===================== -->
    <!-- EXPORT JOURNAL FORM -->
    <!-- ===================== -->
    <div class="card p-4">
        <h5 class="mb-3">Export Journal</h5>

        <form method="GET" action="<?= base_url('export/journals') ?>">
            <div class="row">

                <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-control" required>
                        <?php for($m=1;$m<=12;$m++): ?>
                            <option value="<?= $m ?>"><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Year</label>
                    <input type="number" name="year"
                           class="form-control"
                           value="<?= date('Y') ?>" required>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit"
                            class="btn btn-success w-100">
                        <i class="ti ti-file-export me-1"></i>
                        Export to Excel
                    </button>
                </div>

            </div>
        </form>

    </div>

</div>

<?= $this->endSection() ?>
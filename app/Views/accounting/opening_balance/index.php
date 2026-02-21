<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h4>Opening Balance - <?= $year ?></h4>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
        </div>

        <form method="post" action="<?= base_url('opening-balance/save') ?>">

            <div class="card-body">
                    <input type="hidden" name="year" value="<?= $year ?>">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="120">Code</th>
                                <th>Account Name</th>
                                <th width="150">Type</th>
                                <th width="200">Opening Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accounts as $acc): ?>
                                <tr>
                                    <td><?= $acc['account_code'] ?></td>
                                    <td><?= $acc['account_name'] ?></td>
                                    <td><?= ucfirst($acc['account_type']) ?></td>
                                    <td>
                                        <input type="text" 
                                               name="accounts[<?= $acc['id'] ?>]" 
                                               class="form-control text-end"
                                               value="<?= $openingData[$acc['id']] ?? 0 ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
            </div>

            <div class="card-footer">
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        Save Opening Balance
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<?= $this->endSection() ?>
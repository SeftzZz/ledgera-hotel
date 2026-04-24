<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th>Code</th>
            <th>Account</th>
            <th>Type</th>
            <th>Opening</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Ending</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $d): ?>
        <tr>
            <td><?= $d['code'] ?></td>
            <td><?= $d['name'] ?></td>
            <td><?= $d['type'] ?></td>
            <td><?= number_format($d['opening'],2) ?></td>
            <td><?= number_format($d['debit'],2) ?></td>
            <td><?= number_format($d['credit'],2) ?></td>
            <td><?= number_format($d['ending'],2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
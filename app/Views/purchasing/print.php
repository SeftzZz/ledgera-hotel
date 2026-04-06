<!DOCTYPE html>
<html>
<head>
  <title>Print Purchasing</title>

  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 14px;
      margin: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    table th, table td {
      border: 1px solid #000;
      padding: 6px;
    }

    .no-border td {
      border: none !important;
    }

    .text-end {
      text-align: right;
    }

    .mb-3 { margin-bottom: 15px; }
    .mb-4 { margin-bottom: 20px; }

    @media print {
      body {
        margin: 0;
      }
    }
  </style>
</head>

<body>

<h3>Form Purchasing (PO)</h3>

<!-- ================= HEADER ================= -->
<div class="mb-4">
  <table class="no-border">
    <tr>
      <td width="120">Nama</td>
      <td><?= $po->nama_po ?></td>
    </tr>
    <tr>
      <td>Jabatan</td>
      <td><?= $po->jabatan_po ?></td>
    </tr>
    <tr>
      <td>Divisi</td>
      <td><?= $po->divisi_po ?></td>
    </tr>
    <tr>
      <td>Tanggal</td>
      <td><?= $po->tanggal_po ?></td>
    </tr>
  </table>
</div>

<!-- ================= VENDOR ================= -->
<div class="mb-4">
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Vendor</th>
        <th>Bon</th>
        <th>No PO</th>
      </tr>
    </thead>
    <tbody>
      <?php $no=1; foreach ($vendors as $v): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= $v['vendor_name'] ?></td>
        <td><?= $v['is_bon'] ? '✔' : '-' ?></td>
        <td><?= $v['no_po'] ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- ================= TOTAL ================= -->
<div class="text-end mb-3">
  <strong>Total: Rp <?= number_format($total) ?></strong>
</div>

<!-- ================= ITEMS ================= -->
<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Barang</th>
      <th>Qty</th>
      <th>Vendor</th>
      <th>Harga</th>
    </tr>
  </thead>
  <tbody>
    <?php $no=1; foreach ($items as $i): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= $i['sparepart'] ?></td>
      <td><?= $i['qty'] ?></td>
      <td><?= $i['vendor_name'] ?></td>
      <td>Rp <?= number_format($i['harga']) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<script>
  window.onload = function () {
    window.print();
  };
</script>

</body>
</html>
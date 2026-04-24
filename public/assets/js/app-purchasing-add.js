/**
 * App Purchasing - Clean Version
 */

'use strict';

(function () {
  const invoiceItemQtyList = document.querySelectorAll('.invoice-item-qty'),
        invoiceDateList = document.querySelectorAll('.date-picker');

  // Qty
  if (invoiceItemQtyList) {
    invoiceItemQtyList.forEach(function (el) {
      new Cleave(el, {
        delimiter: '',
        numeral: true
      });
    });
  }

  // Datepicker
  if (invoiceDateList) {
    invoiceDateList.forEach(function (el) {
      el.flatpickr({
        monthSelectorType: 'static'
      });
    });
  }
})();


// ==========================
// MAIN
// ==========================
$(function () {

  // =========================
  // LOAD STATS (PURCHASING)
  // =========================
  async function loadPurchasingStats() {

    try {

      const res = await fetch('/api/pengajuan/stats', {
        headers: {
          Authorization: 'Bearer ' + window.jwtToken
        }
      });

      const json = await res.json();

      // 🔥 tetap pakai ID template lama (tidak diubah)
      document.getElementById('purchasing_pending').innerText = json.data.pengajuan;
      document.getElementById('purchasing_proses').innerText = json.data.proses;
      document.getElementById('purchasing_selesai').innerText = json.data.selesai;
      document.getElementById('purchasing_total').innerText = json.data.total;
      document.getElementById('purchasing_today').innerText = json.data.today;

    } catch (err) {
      console.error(err);
    }

  }

  loadPurchasingStats();


  // =========================
  // DATATABLE PURCHASING
  // =========================
  const table = $('.datatables-purchasing');

  if (table.length) {

    table.DataTable({
      ajax: {
        url: '/api/purchasing', // sekarang isi dari form_pengajuan
        headers: {
          Authorization: 'Bearer ' + window.jwtToken
        }
      },
      columns: [
        // No Pengajuan
        { 
          data: 'id',
          render: function (data) {
            return 'PG-' + data;
          }
        },

        // Tanggal
        { data: 'tanggal' },

        // Pemohon
        { data: 'nama' },

        // Total Item
        { data: 'total_item' },

        // Total Harga
        {
          data: 'total_harga',
          render: function (data) {
            return 'Rp ' + Number(data || 0).toLocaleString();
          }
        },

        // Status
        {
          data: 'status',
          render: function (data) {

            let badge = 'secondary';

            if (data === 'Pengajuan') badge = 'warning';
            if (data === 'Proses') badge = 'info';
            if (data === 'Selesai') badge = 'success';

            return `<span class="badge bg-label-${badge}">${data}</span>`;
          }
        },

        // Actions
        {
          data: null,
          render: function (data) {

            let printBtn = '';

            // 🔥 hanya tampil jika status PROSES
            if (data.status === 'Selesai') {
              printBtn = `
                <a href="/purchasing/print/${data.id}" 
                   target="_blank"
                   class="dropdown-item">
                  Print
                </a>
              `;
            }

            return `
              <div class="d-flex justify-content-sm-center align-items-sm-center">
                <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="ti ti-dots-vertical"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end m-0">
                  
                  <a href="javascript:void(0);" 
                     class="dropdown-item btn-detail-pengajuan" 
                     data-id="${data.id}">
                    View
                  </a>

                  ${printBtn}

                </div>
              </div>
            `;
          }
        }
      ]
    });

  }


  // =========================
  // DETAIL PO
  // =========================
  $(document).on('click', '.btn-detail-pengajuan', async function () {

    const id = $(this).data('id');

    const res = await fetch(`/api/pengajuan/${id}`, {
      headers: {
        Authorization: 'Bearer ' + window.jwtToken
      }
    });

    const json = await res.json();
    const data = json.data;
    const isLocked = data.header.status === 'Proses' || data.header.status === 'Selesai';

    $('#po_pengajuan_id').val(id);

    // =========================
    // HEADER (PO USER, BUKAN PENGAJUAN)
    // =========================
    $('#po_nama').val('');
    $('#po_divisi').val('');
    $('#po_jabatan').val('');
    $('#po_tanggal').val(new Date().toISOString().split('T')[0]);
    $('#po_dp').val('');

    // =========================
    // GROUP VENDOR AWAL
    // =========================
    let vendors = {};

    data.items.forEach(item => {
      vendors[item.vendor_id] = {
        name: item.vendor_name,
        kode: item.vendor_kode
      };
    });

    const bulan = new Date().toISOString().slice(5,7);
    const tahun = new Date().toISOString().slice(2,4);

    let vendorHtml = '';
    let no = 1;

    Object.keys(vendors).forEach(vendorId => {

      const v = vendors[vendorId];
      const noPo = `${v.kode}/${bulan}${tahun}-0001`;

      vendorHtml += `
        <tr data-vendor-id="${vendorId}">
          <td>${no++}</td>
          <td>${v.name}</td>
          <td><input type="checkbox" class="is-bon"></td>
          <td><input type="text" class="form-control po-number" value="${noPo}"></td>
        </tr>
      `;
    });

    $('#vendorTable tbody').html(vendorHtml);

    // =========================
    // ITEMS
    // =========================
    let html = '';
    let total = 0;
    let i = 1;

    data.items.forEach(item => {

      let subtotal = item.qty * item.harga;
      total += subtotal;

      html += `
        <tr 
          data-detail-id="${item.id}"
          data-vendor-id="${item.vendor_id}"
          data-item-id="${item.vendor_item_id}"
          data-harga="${item.harga}"
          data-sparepart="${item.sparepart}"
          data-purpose="${item.purpose}"
          data-vendor-kode="${item.vendor_kode}"
        >
          <td>${i}</td>
          <td>${item.sparepart}</td>
          <td>
            <input 
              type="number" 
              class="form-control qty-input" 
              value="${item.qty}" 
              min="0"
              data-harga="${item.harga}"
            >
          </td>
          <td>${item.satuan}</td>
          <td>${item.purpose}</td>
          <td>${item.vendor_name}</td>
          <td>
            <button 
              type="button"
              class="btn btn-sm btn-primary btn-pilih-vendor ${isLocked ? 'disabled' : ''}"
              data-detail-id="${item.id}"
              data-sparepart="${item.sparepart}"
              ${isLocked ? 'disabled' : ''}
            >
              ${isLocked ? 'Locked' : 'Pilih Vendor'}
            </button>
          </td>
          <td>Rp ${Number(item.harga).toLocaleString()}</td>
          <td class="subtotal">
            Rp ${Number(item.harga * item.qty).toLocaleString()}
          </td>
        </tr>
      `;

      i++;
    });

    $('#po_items_table tbody').html(html);
    $('#po_grand_total').text('Rp ' + total.toLocaleString());
    window.totalPayment = total;

    $('#modalDetailPO').modal('show');

    const overLimit = checkLimit();

    if (isLocked || overLimit) {
      $('#btnSavePO').closest('.modal-footer').hide();
    } else {
      $('#btnSavePO').closest('.modal-footer').show();
    }

    $('#limitWarning').remove();

    if (overLimit) {
      $('#estimatedLimit').after(`
        <div id="limitWarning" class="alert alert-danger mt-2">
          Estimated spend melebihi limit!
        </div>
      `);
    }

  });

  $(document).on('input', '.qty-input', function () {

    let row = $(this).closest('tr');

    let qty = parseFloat($(this).val()) || 0;
    let harga = parseFloat($(this).data('harga')) || 0;

    let subtotal = qty * harga;

    // update subtotal
    row.find('.subtotal').text('Rp ' + subtotal.toLocaleString());

    // =========================
    // UPDATE TOTAL
    // =========================
    let total = 0;

    $('#po_items_table tbody tr').each(function () {
      let q = parseFloat($(this).find('.qty-input').val()) || 0;
      let h = parseFloat($(this).find('.qty-input').data('harga')) || 0;
      total += (q * h);
    });

    $('#po_grand_total').text('Rp ' + total.toLocaleString());
    window.totalPayment = total;

    // =========================
    // UPDATE LIMIT UI
    // =========================
    const overLimit = checkLimit();

    // tombol save
    if (overLimit) {
      $('#btnSavePO').closest('.modal-footer').hide();
    } else {
      $('#btnSavePO').closest('.modal-footer').show();
    }

    // warning
    $('#limitWarning').remove();

    if (overLimit) {
      $('#estimatedLimit').after(`
        <div id="limitWarning" class="alert alert-danger mt-2">
          Estimated spend melebihi limit!
        </div>
      `);
    }

  });

  // =========================
  // PILIH VENDOR (OPEN MODAL)
  // =========================
  $(document).on('click', '.btn-pilih-vendor', async function () {
    if ($(this).prop('disabled')) return;
    const sparepart = $(this).data('sparepart');
    const detailId = $(this).data('detail-id');
    
    const res = await fetch('/api/partners/items', {
      headers: {
        Authorization: 'Bearer ' + window.jwtToken
      }
    });

    const json = await res.json();

    const filtered = json.data.filter(x => x.sparepart === sparepart);

    const minHarga = Math.min(...filtered.map(x => Number(x.harga)));

    let html = '';

    filtered.forEach(v => {
      html += `
        <tr>
          <td>${v.vendor_name}</td>
          <td class="${v.harga == minHarga ? 'text-success fw-bold' : ''}">
            Rp ${Number(v.harga).toLocaleString()}
          </td>
          <td>
            <button 
              class="btn btn-success btn-select-vendor"
              data-detail-id="${detailId}"
              data-vendor="${v.vendor_name}"
              data-vendor-id="${v.vendor_id}"
              data-item-id="${v.id}"
              data-harga="${v.harga}"
              data-vendor-kode="${v.vendor_kode}"
            >
              Pilih
            </button>
          </td>
        </tr>
      `;
    });

    $('#vendorOptions').html(html);
    $('#modalVendor').modal('show');

  });


  // =========================
  // SELECT VENDOR (UPDATE ROW)
  // =========================
  $(document).on('click', '.btn-select-vendor', function () {

    const detailId = $(this).data('detail-id');
    const vendor = $(this).data('vendor');
    const vendorId = $(this).data('vendor-id');
    const itemId = $(this).data('item-id');
    const harga = $(this).data('harga');
    const vendorKode = $(this).data('vendor-kode');

    console.log('SELECTED VENDOR KODE:', vendorKode);

    const row = $('#po_items_table tbody tr[data-detail-id="'+detailId+'"]');

    // update tampilan
    row.find('td:nth-child(4)').text(vendor);
    row.find('td:nth-child(6)').text('Rp ' + Number(harga).toLocaleString());

    // update data
    row.attr('data-vendor-id', vendorId);
    row.attr('data-item-id', itemId);
    row.attr('data-harga', harga);
    row.attr('data-vendor-kode', vendorKode); // 🔥 FIX UTAMA

    // =========================
    // RECALCULATE TOTAL
    // =========================
    let total = 0;

    $('#po_items_table tbody tr').each(function () {
      const qty = parseInt($(this).find('td:nth-child(3)').text()) || 0;
      const harga = parseInt($(this).attr('data-harga')) || 0;
      total += qty * harga;
    });

    $('#po_grand_total').text('Rp ' + total.toLocaleString());
    window.totalPayment = total;

    rebuildVendorTable();

    $('#modalVendor').modal('hide');

  });


  // =========================
  // REBUILD VENDOR TABLE
  // =========================
  function rebuildVendorTable() {

    let vendors = {};

    $('#po_items_table tbody tr').each(function () {

      const row = $(this);

      const vendorId = row.attr('data-vendor-id');
      const vendorName = row.find('td:nth-child(4)').text();
      const vendorKode = row.attr('data-vendor-kode');

      if (!vendorId) return;

      vendors[vendorId] = {
        name: vendorName,
        kode: vendorKode // 🔥 selalu ambil dari row terbaru
      };

      console.log('ROW FINAL KODE:', vendorKode);

    });

    const bulan = new Date().toISOString().slice(5,7);
    const tahun = new Date().toISOString().slice(2,4);

    let html = '';
    let no = 1;

    Object.keys(vendors).forEach(vendorId => {

      const v = vendors[vendorId];
      const noPo = `${v.kode}/${bulan}${tahun}-0001`;

      html += `
        <tr data-vendor-id="${vendorId}">
          <td>${no++}</td>
          <td>${v.name}</td>
          <td><input type="checkbox" class="is-bon"></td>
          <td><input type="text" class="form-control po-number" value="${noPo}"></td>
        </tr>
      `;
    });

    $('#vendorTable tbody').html(html);
  }

  $('#btnSavePO').on('click', async function () {

    let items = [];

    $('#po_items_table tbody tr').each(function () {

      const row = $(this);

      const vendorRow = $('#vendorTable tr[data-vendor-id="'+row.data('vendor-id')+'"]');

      let noPo = null;
      let isBon = 0;

      if (vendorRow.length) {
        noPo = vendorRow.find('.po-number').val();
        isBon = vendorRow.find('.is-bon').is(':checked') ? 1 : 0;
      }

      const qty = parseFloat(row.find('.qty-input').val()) || 0;
      const harga = parseFloat(row.data('harga')) || 0;

      // ❗ skip kalau qty 0
      if (qty <= 0) return;

      items.push({
        detail_id: row.data('detail-id'),
        vendor_item_id: row.data('item-id'),
        qty: qty,
        harga: harga,
        subtotal: qty * harga, // 🔥 optional tapi bagus
        no_po: noPo,
        is_bon: isBon
      });

    });

    // =========================
    // VALIDASI
    // =========================
    if (items.length === 0) {
      Swal.fire('Error', 'Item kosong atau qty 0', 'error');
      return;
    }

    let payload = {
      pengajuan_id: $('#po_pengajuan_id').val(),
      nama: $('#po_nama').val(),
      divisi: $('#po_divisi').val(),
      jabatan: $('#po_jabatan').val(),
      tanggal: $('#po_tanggal').val(),
      deposit: $('#po_dp').val(),
      items: items,
      total: window.totalPayment,
      branch_name: window.branchName
    };

    console.log('PAYLOAD:', payload);

    try {

      const res = await fetch('/api/purchasing/save', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: 'Bearer ' + window.jwtToken
        },
        body: JSON.stringify(payload)
      });

      const json = await res.json();

      if (!json.status) {
        throw new Error(json.message || 'Gagal simpan PO');
      }

      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Purchase Order berhasil disimpan',
        timer: 1500,
        showConfirmButton: false
      }).then(() => {
        location.reload();
      });

    } catch (err) {

      console.error(err);
      Swal.fire('Error', err.message || 'Terjadi kesalahan', 'error');

    }

  });
});
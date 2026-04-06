/**
 * App Invoice - Add (CLEAN - NO ORDER)
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

  // Datepicker (kalau masih dipakai di UI)
  if (invoiceDateList) {
    invoiceDateList.forEach(function (el) {
      el.flatpickr({
        monthSelectorType: 'static'
      });
    });
  }
})();


// ==========================
// REPEATER
// ==========================
$(function () {

  var sourceItem = $('.source-item');

  // =========================
  // INIT REPEATER
  // =========================
  if (sourceItem.length) {
    sourceItem.on('submit', function (e) {
      e.preventDefault();
    });

    sourceItem.repeater({
      initEmpty: false,

      show: function () {
        $(this).slideDown();

        // tooltip
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (el) {
          return new bootstrap.Tooltip(el);
        });

        // 🔥 load hanya row baru
        loadVendorItems(this);
      },

      hide: function (deleteElement) {
        if (confirm('Hapus item ini?')) {
          $(this).slideUp(deleteElement);
        }
      }
    });
  }

  // =========================
  // CACHE (BIAR GA FETCH TERUS)
  // =========================
  let vendorItemsCache = [];

  // =========================
  // LOAD VENDOR ITEMS
  // =========================
  async function loadVendorItems(context = document) {

    try {

      // 🔥 cache
      if (vendorItemsCache.length === 0) {
        const res = await fetch('/api/partners/items', {
          headers: {
            Authorization: 'Bearer ' + window.jwtToken
          }
        });

        const json = await res.json();
        vendorItemsCache = json.data;
      }

      const selects = context.querySelectorAll
        ? context.querySelectorAll('.vendor-item')
        : $(context).find('.vendor-item');

      $(selects).each(function () {

        const select = this;

        // 🔥 JANGAN overwrite kalau sudah terisi
        if (select.options.length > 1) return;

        let html = '<option value="">Pilih Item</option>';

        vendorItemsCache.forEach(item => {
          html += `
            <option 
              value="${item.id}" 
              data-price="${item.harga}"
              data-vendor="${item.vendor_name}"
              data-sparepart="${item.sparepart}"
            >
              ${item.sparepart} (${item.vendor_name})
            </option>
          `;
        });

        select.innerHTML = html;

      });

    } catch (err) {
      console.error(err);
    }
  }

  // =========================
  // INIT AWAL (LOAD 1st ROW)
  // =========================
  $(document).ready(function () {
    loadVendorItems(document);
  });

  // =========================
  // MODAL OPEN
  // =========================
  $('#btnOpenPengajuan').on('click', function () {
    $('#modalPengajuan').modal('show');

    // 🔥 pastikan dropdown ada
    loadVendorItems(document);
  });


  // =========================
  // RESET MODAL
  // =========================
  $('#modalPengajuan').on('hidden.bs.modal', function () {

    $('#formPengajuan')[0].reset();

    const list = $('[data-repeater-list="items"]');

    list.find('[data-repeater-item]').not(':first').remove();

    const first = list.find('[data-repeater-item]').first();

    first.find('input[name="qty"]').val(1);
    first.find('select').val('');

  });


  // =========================
  // SUBMIT
  // =========================
  $('#btnSubmit').on('click', function () {

    let items = [];
    let invalid = false;

    $('[data-repeater-item]').each(function () {

      let row = $(this);

      let vendor = row.find('.vendor-item').val();

      let qty = row.find('[name="qty"]').val();
      qty = qty ? parseInt(qty) : 0;

      if (isNaN(qty) || qty <= 0) qty = 1;

      console.log('ROW:', { vendor, qty });

      // reset error style
      row.find('.vendor-item').removeClass('is-invalid');
      row.find('[name="qty"]').removeClass('is-invalid');

      // skip row kosong
      if (!vendor && qty === 0) return;

      // validasi
      if (!vendor) {
        row.find('.vendor-item').addClass('is-invalid');
        invalid = true;
        return;
      }

      items.push({
        vendor_item_id: Number(vendor),
        qty: qty
      });

    });

    // =========================
    // VALIDASI GLOBAL
    // =========================
    if (invalid) {
      Swal.fire({
        icon: 'error',
        title: 'Input tidak valid',
        text: 'Periksa kembali item dan qty'
      });
      return;
    }

    if (items.length === 0) {
      Swal.fire({
        icon: 'error',
        title: 'Item kosong',
        text: 'Silakan pilih minimal 1 item'
      });
      return;
    }

    if (!$('#nama').val()) {
      Swal.fire('Error', 'Nama wajib diisi', 'error');
      return;
    }

    // =========================
    // PAYLOAD
    // =========================
    let payload = {
      branch_id: window.branchId,
      nama: $('#nama').val(),
      divisi: $('#divisi').val(),
      jabatan: $('#jabatan').val(),
      tanggal: $('#tanggal').val(),
      items: items
    };

    // =========================
    // SUBMIT
    // =========================
    $('#btnSubmit').prop('disabled', true);

    $.ajax({
      url: '/api/pengajuan',
      type: 'POST',
      headers: {
        Authorization: 'Bearer ' + window.jwtToken
      },
      contentType: 'application/json',
      data: JSON.stringify(payload),

      success: function (res) {

        if (res.status) {
          Swal.fire('Success', 'Pengajuan berhasil disimpan', 'success');
          location.reload();
        } else {
          Swal.fire('Error', res.message || 'Gagal', 'error');
          $('#btnSubmit').prop('disabled', false);
        }

      },

      error: function (err) {
        console.error(err);
        Swal.fire('Error', 'Terjadi kesalahan server', 'error');
        $('#btnSubmit').prop('disabled', false);
      }

    });

  });

  async function loadPengajuanStats() {

    try {

      const res = await fetch('/api/pengajuan/stats', {
        headers: {
          Authorization: 'Bearer ' + window.jwtToken
        }
      });

      const json = await res.json();

      document.getElementById('pengajuan_pending').innerText = json.data.pengajuan;
      document.getElementById('pengajuan_proses').innerText = json.data.proses;
      document.getElementById('pengajuan_selesai').innerText = json.data.selesai;
      document.getElementById('pengajuan_total').innerText = json.data.total;
      document.getElementById('pengajuan_today').innerText = json.data.today;

    } catch (err) {
      console.error(err);
    }

  }

  loadPengajuanStats();

});
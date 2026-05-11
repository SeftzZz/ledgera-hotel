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
              data-satuan="${item.satuan || ''}"
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

  $(document).on('change', '.vendor-item', function () {

    const selected = $(this).find(':selected');

    const satuan = selected.data('satuan') || '';

    const row = $(this).closest('[data-repeater-item]');

    row.find('.satuan-label').text(satuan);

  });

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
  $('#btnSubmit').on('click', async function () {

    let items = [];
    let invalid = false;

    $('[data-repeater-item]').each(function () {

      let row = $(this);

      let vendor = row.find('.vendor-item').val();
      let purpose = row.find('.purpose').val(); // 🔥 FIX

      let qtyRaw = row.find('input[name$="[qty]"], input[name="qty"]').val();

      qtyRaw = (qtyRaw ?? '').toString().trim().replace(',', '.');

      let qty = parseFloat(qtyRaw);
      if (isNaN(qty)) qty = 0;

      // reset error
      row.find('.vendor-item').removeClass('is-invalid');
      row.find('.qty').removeClass('is-invalid');
      row.find('.purpose').removeClass('is-invalid');

      // skip row kosong
      if (!vendor && qty === 0 && !purpose) return;

      // =========================
      // VALIDASI PER ROW
      // =========================
      if (!vendor) {
        row.find('.vendor-item').addClass('is-invalid');
        invalid = true;
        return;
      }

      if (qty <= 0) {
        row.find('.qty').addClass('is-invalid');
        invalid = true;
        return;
      }

      if (!purpose) {
        row.find('.purpose').addClass('is-invalid');
        invalid = true;
        return;
      }

      items.push({
        vendor_item_id: Number(vendor),
        qty: qty,
        purpose: purpose
      });

    });

    // =========================
    // VALIDASI GLOBAL
    // =========================
    if (invalid) {
      Swal.fire('Error', 'Periksa kembali item, qty, dan tujuan', 'error');
      return;
    }

    if (items.length === 0) {
      Swal.fire('Error', 'Minimal 1 item', 'error');
      return;
    }

    if (!$('#nama').val()) {
      Swal.fire('Error', 'Nama wajib diisi', 'error');
      return;
    }

    $('#btnSubmit').prop('disabled', true);

    try {

      const payload = {
        company_id: window.companyId,
        branch_id: window.branchId,
        nama: $('#nama').val(),
        divisi: $('#divisi').val(),
        jabatan: $('#jabatan').val(),
        tanggal: $('#tanggal').val(),
        items: items
      }

      console.log(payload);
      
      // =========================
      // 1. SIMPAN PENGAJUAN
      // =========================
      const pengajuanRes = await fetch('/api/pengajuan', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: 'Bearer ' + window.jwtToken
        },
        body: JSON.stringify({
          company_id: window.companyId,
          branch_id: window.branchId,
          nama: $('#nama').val(),
          divisi: $('#divisi').val(),
          jabatan: $('#jabatan').val(),
          tanggal: $('#tanggal').val(),
          items: items
        })
      });

      const pengajuanJson = await pengajuanRes.json();

      if (!pengajuanJson.status) {
        throw new Error(pengajuanJson.message || 'Gagal simpan pengajuan');
      }

      // 🔥 AMBIL PENGAJUAN ID
      const pengajuanId = pengajuanJson?.id;

      if (!pengajuanId) {
        throw new Error('pengajuan_id tidak ditemukan');
      }

      // =========================
      // SUCCESS
      // =========================
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Pengajuan berhasil dibuat',
        timer: 1500,
        showConfirmButton: false
      }).then(() => {
        location.reload();
      });

    } catch (err) {

      console.error(err);

      Swal.fire('Error', err.message || 'Terjadi kesalahan', 'error');

      $('#btnSubmit').prop('disabled', false);
    }

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
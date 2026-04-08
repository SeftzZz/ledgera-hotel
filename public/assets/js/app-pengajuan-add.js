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

      let qtyRaw = row.find('input[name$="[qty]"], input[name="qty"]').val();

      qtyRaw = (qtyRaw ?? '').toString().trim().replace(',', '.');

      let qty = parseFloat(qtyRaw);
      if (isNaN(qty)) qty = 0;

      // reset error
      row.find('.vendor-item').removeClass('is-invalid');
      row.find('.qty').removeClass('is-invalid');

      if (!vendor && qty === 0) return;

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

      items.push({
        vendor_item_id: Number(vendor),
        qty: qty
      });

    });

    // =========================
    // VALIDASI GLOBAL
    // =========================
    if (invalid) {
      Swal.fire('Error', 'Periksa kembali item dan qty', 'error');
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

      // // =========================
      // // 2. CREATE CART
      // // =========================
      // const cartRes = await fetch('/api/cart/create', {
      //   method: 'POST',
      //   headers: {
      //     'Content-Type': 'application/json',
      //     Authorization: 'Bearer ' + window.jwtToken
      //   },
      //   body: JSON.stringify({
      //     branch_id: window.branchId,
      //     name: $('#nama').val(),
      //     email: window.userEmail,
      //     category: window.categoryName
      //   })
      // });

      // const cartJson = await cartRes.json();
      // const cartId = cartJson.data.cart_id;

      // // =========================
      // // 3. ADD ITEMS KE CART
      // // =========================
      // for (let item of items) {

      //   // 🔥 ambil harga dari cache
      //   const vendorItem = vendorItemsCache.find(v => v.id == item.vendor_item_id);

      //   const price = vendorItem ? vendorItem.harga : 0;

      //   await fetch('/api/cart/add', {
      //     method: 'POST',
      //     headers: {
      //       'Content-Type': 'application/json',
      //       Authorization: 'Bearer ' + window.jwtToken
      //     },
      //     body: JSON.stringify({
      //       cart_id: cartId,
      //       item_id: item.vendor_item_id,
      //       quantity: item.qty,
      //       price: Number(price),
      //       date: $('#tanggal').val()
      //     })
      //   });

      // }

      // // =========================
      // // 4. CHECKOUT
      // // =========================
      // const orderRes = await fetch('/api/orders/checkout', {
      //   method: 'POST',
      //   headers: {
      //     'Content-Type': 'application/json',
      //     Authorization: 'Bearer ' + window.jwtToken
      //   },
      //   body: JSON.stringify({
      //     cart_id: cartId,
      //     order_number: 'PG-' + pengajuanId,
      //     payment_method: 'cash',
      //     deposit: 0,
      //     pengajuan_id: pengajuanId
      //   })
      // });

      // const orderJson = await orderRes.json();

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
        // location.reload();
      });

      console.log('ORDER:', orderJson);

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
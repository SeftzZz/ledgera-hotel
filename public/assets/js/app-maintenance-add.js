/**
 * App Invoice - Add (MODIFIED → MAINTENANCE LOGIC)
 */

'use strict';

(function () {
  const invoiceItemPriceList = document.querySelectorAll('.invoice-item-price'),
    invoiceItemQtyList = document.querySelectorAll('.invoice-item-qty'),
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

  // Price
  if (invoiceItemPriceList) {
    invoiceItemPriceList.forEach(function (el) {
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


// =========================
// REPEATER
// =========================
$(function () {

  const sourceItem = $('.source-item');

  if (sourceItem.length) {
    sourceItem.on('submit', function (e) {
      e.preventDefault();
    });

    sourceItem.repeater({
      show: function () {
        $(this).slideDown();

        // init cleave ulang
        new Cleave($(this).find('.invoice-item-qty')[0], {
          numeral: true,
          delimiter: ''
        });

        new Cleave($(this).find('.invoice-item-price')[0], {
          numeral: true,
          delimiter: ''
        });
      },
      hide: function (e) {
        $(this).slideUp();
      }
    });
  }


  // =========================
  // AUTO ISI HARGA + SPAREPART
  // =========================
  $(document).on('change', '.item-details', function () {

    const selected = $(this).find(':selected');
    const wrapper = $(this).closest('[data-repeater-item]');

    const text = selected.text(); // contoh: Daun Bawang (10000)

    const match = text.match(/\((\d+)\)/);
    const harga = match ? match[1] : 0;

    wrapper.find('.invoice-item-price').val(harga);

    const sparepart = text.split('(')[0].trim();
    wrapper.find('input[name="sparepart"]').val(sparepart);

  });


  // =========================
  // VALIDASI
  // =========================
  function validateForm() {

    const nama = $('input[name="nama"]').val();
    const divisi = $('input[name="divisi"]').val();
    const jabatan = $('input[name="jabatan"]').val();
    const tanggal = $('input[name="tanggal"]').val();

    if (!nama || !divisi || !jabatan || !tanggal) {
      alert('Header wajib diisi');
      return false;
    }

    const rows = $('[data-repeater-item]');

    if (rows.length === 0) {
      alert('Item kosong');
      return false;
    }

    let valid = true;

    rows.each(function () {
      const itemId = $(this).find('.item-details').val();
      const qty = $(this).find('.invoice-item-qty').val();
      const price = $(this).find('.invoice-item-price').val();

      if (!itemId || !qty || qty <= 0 || !price) {
        valid = false;
      }
    });

    if (!valid) {
      alert('Item tidak lengkap');
      return false;
    }

    return true;
  }


  // =========================
  // SUBMIT → MAINTENANCE API
  // =========================
  document.getElementById('btnSubmit').addEventListener('click', async function () {

    if (!validateForm()) return;

    try {

      const rows = document.querySelectorAll('[data-repeater-item]');

      const payload = {
        nama: document.querySelector('[name="nama"]').value,
        divisi: document.querySelector('[name="divisi"]').value,
        jabatan: document.querySelector('[name="jabatan"]').value,
        tanggal: document.querySelector('[name="tanggal"]').value,
        items: []
      };

      // =========================
      // LOOP ITEMS
      // =========================
      for (let row of rows) {

        const itemId = row.querySelector('.item-details').value;
        const qty = row.querySelector('.invoice-item-qty').value;
        const price = row.querySelector('.invoice-item-price').value;
        const kondisi = row.querySelector('[name="kondisi"]').value;
        const sparepart = row.querySelector('[name="sparepart"]').value;

        if (!itemId || !qty || qty <= 0) continue;

        payload.items.push({
          vendor_item_id: Number(itemId),
          sparepart: sparepart,
          qty: Number(qty),
          harga: Number(price),
          kondisi: kondisi
        });

      }

      if (payload.items.length === 0) {
        alert('Item kosong');
        return;
      }

      // =========================
      // FETCH API (MAINTENANCE)
      // =========================
      const res = await fetch('/maintenance/save', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: 'Bearer ' + window.jwtToken
        },
        body: JSON.stringify(payload)
      });

      const json = await res.json();

      if (!json.status) {
        throw new Error(json.message || 'Gagal');
      }

      alert('Maintenance berhasil disimpan');

      location.reload();

    } catch (err) {
      console.error(err);
      alert('Terjadi error');
    }

  });

});
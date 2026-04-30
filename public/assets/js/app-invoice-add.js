/**
 * App Invoice - Add
 */

'use strict';

(function () {
  const invoiceItemPriceList = document.querySelectorAll('.invoice-item-price'),
    invoiceItemQtyList = document.querySelectorAll('.invoice-item-qty'),
    invoiceDateList = document.querySelectorAll('.date-picker');

  // Qty
  if (invoiceItemQtyList) {
    invoiceItemQtyList.forEach(function (invoiceItemQty) {
      new Cleave(invoiceItemQty, {
        delimiter: '',
        numeral: true
      });
    });
  }

  // Datepicker
  if (invoiceDateList) {
    invoiceDateList.forEach(function (invoiceDateEl) {
      invoiceDateEl.flatpickr({
        monthSelectorType: 'static'
      });
    });
  }
})();

// repeater (jquery)
$(function () {
  var applyChangesBtn = $('.btn-apply-changes'),
    discount,
    tax1,
    tax2,
    discountInput,
    tax1Input,
    tax2Input,
    sourceItem = $('.source-item'),
    adminDetails = {
      'App Design': 'Designed UI kit & app pages.',
      'App Customization': 'Customization & Bug Fixes.',
      'ABC Template': 'Bootstrap 4 admin template.',
      'App Development': 'Native App Development.'
    };

  // Prevent dropdown from closing on tax change
  $(document).on('click', '.tax-select', function (e) {
    e.stopPropagation();
  });

  // On tax change update it's value value
  function updateValue(listener, el) {
    listener.closest('.repeater-wrapper').find(el).text(listener.val());
  }

  // Apply item changes btn
  if (applyChangesBtn.length) {
    $(document).on('click', '.btn-apply-changes', function (e) {
      var $this = $(this);
      tax1Input = $this.closest('.dropdown-menu').find('#taxInput1');
      tax2Input = $this.closest('.dropdown-menu').find('#taxInput2');
      discountInput = $this.closest('.dropdown-menu').find('#discountInput');
      tax1 = $this.closest('.repeater-wrapper').find('.tax-1');
      tax2 = $this.closest('.repeater-wrapper').find('.tax-2');
      discount = $('.discount');

      if (tax1Input.val() !== null) {
        updateValue(tax1Input, tax1);
      }

      if (tax2Input.val() !== null) {
        updateValue(tax2Input, tax2);
      }

      if (discountInput.val().length) {
        $this
          .closest('.repeater-wrapper')
          .find(discount)
          .text(discountInput.val() + '%');
      }
    });
  }

  // Repeater init
  if (sourceItem.length) {
    sourceItem.on('submit', function (e) {
      e.preventDefault();
    });
    sourceItem.repeater({
      show: function () {
        $(this).slideDown();
        // Initialize tooltip on load of each item
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });
      },
      hide: function (e) {
        $(this).slideUp();
      }
    });
  }

  // Item details select onchange
  $(document).on('change', '.item-details', function () {
    var $this = $(this);

    if (!$this.next('.extra-fields').length) {

      $this.after(`
        <div class="extra-fields mt-2">
          
          <select 
            name="customer[]" 
            class="form-select select2 mb-3" 
            data-placeholder="Select customer" 
            style="width:100%">
            <option value=""></option>
            ${window.customerOptions || ''}
          </select>

          <input type="text" name="customer_phone[]" class="form-control mb-3 mt-3" placeholder="No Telepon">
          <input type="email" name="customer_email[]" class="form-control" placeholder="Email">

          <div class="row">
            <div class="col-md-6 mb-3 mt-3">
              <input type="text" name="check_in[]" class="form-control date-picker" placeholder="Check In">
            </div>
            <div class="col-md-6 mb-3 mt-3">
              <input type="text" name="check_out[]" class="form-control date-picker" placeholder="Check Out">
            </div>
          </div>

          <textarea name="note[]" class="form-control" placeholder="Note"></textarea>
        </div>
      `);

      // init select2 (WAJIB karena dynamic)
      $this.next('.extra-fields').find('.select2').select2({
        placeholder: "Select customer",
        width: '100%',
        tags: true,                //ini kunci
        allowClear: true,
        createTag: function (params) {
          var term = $.trim(params.term);

          if (term === '') {
            return null;
          }

          return {
            id: term,
            text: term,
            newTag: true // optional flag
          };
        }
      });

      // init datepicker untuk field baru
      $this.next('.extra-fields').find('.date-picker').flatpickr({
        monthSelectorType: 'static'
      });
    }
  });

  $(document).on('change', '.select2', function () {
    const selected = $(this).find(':selected');
    const wrapper = $(this).closest('.extra-fields');

    const phone = selected.data('phone') || '';
    const email = selected.data('email') || '';
    const name  = selected.text(); // ambil nama (termasuk input manual)

    wrapper.find('input[name="customer_phone[]"]').val(phone);
    wrapper.find('input[name="customer_email[]"]').val(email);

    // simpan name ke hidden (biar gampang kirim)
    if (!wrapper.find('.customer-name-hidden').length) {
      wrapper.append(`<input type="hidden" class="customer-name-hidden" name="customer_name[]" value="${name}">`);
    } else {
      wrapper.find('.customer-name-hidden').val(name);
    }

  });

  async function loadItemsDropdown() {
    try {
      const res = await fetch('/api/items', {
        headers: {
          Authorization: 'Bearer ' + window.jwtToken
        }
      });

      const json = await res.json();

      const selectList = document.querySelectorAll('.item-details');

      selectList.forEach(select => {
        select.innerHTML = '<option disabled selected>Select Item</option>';

        json.data.forEach(item => {
          select.innerHTML += `
            <option value="${item.id}">
              ${item.name} (${item.category_name})
            </option>
          `;
        });
      });

    } catch (err) {
      console.error(err);
    }
  }

  loadItemsDropdown();

  $(document).on('click', '[data-repeater-create]', function () {
    setTimeout(() => {
      loadItemsDropdown();
    }, 300);
  });

  document.getElementById('btnSubmit').addEventListener('click', async function () {
    try {
      const rows = document.querySelectorAll('[data-repeater-item]');

      if (rows.length === 0) {
        alert('Item kosong');
        return;
      }

      // =========================
      // AMBIL CUSTOMER (ROW PERTAMA SAJA)
      // =========================
      const firstRow = rows[0];

      const name = firstRow.querySelector('.customer-name-hidden')?.value || '';
      const phone = firstRow.querySelector('input[name="customer_phone[]"]')?.value || '';
      const email = firstRow.querySelector('input[name="customer_email[]"]')?.value || '';
      const checkIn  = firstRow.querySelector('input[name="check_in[]"]')?.value || '';
      const checkOut = firstRow.querySelector('input[name="check_out[]"]')?.value || '';
      const note     = firstRow.querySelector('textarea[name="note[]"]')?.value || '';

      if (!email) {
        alert('Email wajib diisi');
        return;
      }

      // =========================
      // 1. CREATE CART (sekarang kirim customer)
      // =========================
      const cartRes = await fetch('/api/cart/create', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: 'Bearer ' + window.jwtToken
        },
        body: JSON.stringify({
          branch_id: window.branchId,
          name: name,
          phone: phone,
          email: email,
          category: window.categoryName
        })
      });

      const cartJson = await cartRes.json();
      const cartId = cartJson.data.cart_id;

      // =========================
      // 2. ADD ITEMS
      // =========================
      for (let row of rows) {

        const itemId = row.querySelector('.item-details').value;
        const qty    = row.querySelector('.invoice-item-qty').value;
        const price  = row.querySelector('.invoice-item-price').value;
        const date  = row.querySelector('.invoice-item-date').value;

        if (!date) {
          alert('Date wajib diisi');
          return;
        }

        if (!itemId || !qty || qty <= 0) continue;

        await fetch('/api/cart/add', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: 'Bearer ' + window.jwtToken
          },
          body: JSON.stringify({
            cart_id: cartId,
            item_id: itemId,
            quantity: Number(qty),
            price: Number(price),
            date: date
          })
        });
      }

      // =========================
      // 3. CHECKOUT (TANPA PRICE)
      // =========================
      const orderRes = await fetch('/api/orders/checkout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: 'Bearer ' + window.jwtToken
        },
        body: JSON.stringify({
          cart_id: cartId,
          order_number: document.getElementById('invoiceId').value,
          payment_method: 'cash',
          deposit: document.getElementById('deposit').value,
          check_in: checkIn,
          check_out: checkOut,
          note: note
        })
      });

      if (!checkIn || !checkOut) {
        alert('Check In & Check Out wajib diisi');
        return;
      }

      if (new Date(checkOut) <= new Date(checkIn)) {
        alert('Check Out harus lebih besar dari Check In');
        return;
      }

      const orderJson = await orderRes.json();

      alert('Order berhasil: ' + orderJson.data.order_number);

      location.reload();
    } catch (err) {
      console.error(err);
      alert('Terjadi error');
    }

  });
});

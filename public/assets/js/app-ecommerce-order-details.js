/**
 * app-ecommerce-order-details Script
 */

'use strict';

const orderId = window.location.pathname.split('/').pop();

$(function () {

  var table;
  var dt_details_table = $('.datatables-order-details');

  if (dt_details_table.length) {

    table = dt_details_table.DataTable({

      dom: 't',

      columns: [
        { data: 'name' },
        { data: 'price' },
        { data: 'quantity' },
        { data: null }
      ],

      columnDefs: [

        {
          // PRODUCT NAME
          targets: 0,
          render: function (data, type, full) {
            let name = full.name;

            let states = ['success','danger','warning','info','dark','primary','secondary'];
            let state = states[Math.floor(Math.random() * states.length)];

            let initials = name.match(/\b\w/g) || [];
            initials = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();

            let avatar = `
              <span class="avatar-initial rounded-2 bg-label-${state}">
                ${initials}
              </span>
            `;

            // ambil dari ORDER (bukan item)
            function formatDate(dateStr){
              if(!dateStr) return '-';

              const date = new Date(dateStr);

              return date.toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
              });
            }
            
            let checkIn  = formatDate(window.orderData?.check_in || '-');
            let checkOut = formatDate(window.orderData?.check_out || '-');
            let note     = window.orderData?.note || '-';

            return `
              <div class="d-flex align-items-start">

                <div class="avatar-wrapper me-2">
                  <div class="avatar rounded-2 bg-label-secondary">
                    ${avatar}
                  </div>
                </div>

                <div class="d-flex flex-column">

                  <span class="fw-semibold">${name}</span>

                  <small class="text-muted">
                    <b>Check In:</b> ${checkIn}
                  </small>

                  <small class="text-muted">
                    <b>Check Out:</b> ${checkOut}
                  </small>

                  ${note ? `
                    <small class="text-muted">
                      <b>Note:</b> ${note}
                    </small>
                  ` : ''}

                </div>

              </div>
            `;
          }
        },

        {
          // PRICE
          targets: 1,
          render: function(data){
            return formatRupiah(data);
          }
        },

        {
          // QTY
          targets: 2,
          render: function(data){
            return `<span class="text-body">${data}</span>`;
          }
        },

        {
          // TOTAL
          targets: 3,
          render: function(data,type,row){

            let total = parseFloat(row.price) * parseFloat(row.quantity);

            return `<h6 class="mb-0">${formatRupiah(total)}</h6>`;
          }
        }

      ]

    });


    /*
    ===============================
    LOAD ORDER DATA (1x API)
    ===============================
    */

    $.ajax({

      url: `/api/orders/detail/${orderId}`,

      headers: {
        Authorization: 'Bearer ' + window.jwtToken
      },

      success: function(res){

        const order = res.data.order;
        const items = res.data.items;
        window.orderData = order;

        // HEADER
        $('#order_number').text(order.order_number);
        $('#order_status').text(order.status);
        $('#order_date').text(order.created_at);

        $('#order_subtotal').text(formatRupiah(order.subtotal));
        $('#order_deposit').text(formatRupiah(order.deposit));
        $('#order_discount').text(formatRupiah(order.discount));
        let total = 0;

        items.forEach(item => {
          total += Number(item.price) * Number(item.quantity);
        });

        // kalau ada deposit
        const finalTotal = total - Number(order.deposit || 0);

        $('#order_total').text(formatRupiah(finalTotal));

        // TABLE
        table.clear();
        table.rows.add(items);
        table.draw();

        // CUSTOMER
        $('#customer_id').text('Customer: #' + order.user_id);
        $('#customer_name').text(order.customer_name);
        $('#customer_email').text(order.customer_email);
        $('#customer_phone').text(order.customer_phone);

      }

    });

  }

});


function formatRupiah(number){

  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR'
  }).format(number);

}

//sweet alert
(function () {
  const deleteOrder = document.querySelector('.delete-order');
  // Suspend User javascript
  if (deleteOrder) {
    deleteOrder.onclick = function () {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert order!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete order!',
        customClass: {
          confirmButton: 'btn btn-primary me-2 waves-effect waves-light',
          cancelButton: 'btn btn-label-secondary waves-effect waves-light'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'Order has been removed.',
            customClass: {
              confirmButton: 'btn btn-success waves-effect waves-light'
            }
          });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
          Swal.fire({
            title: 'Cancelled',
            text: 'Cancelled Delete :)',
            icon: 'error',
            customClass: {
              confirmButton: 'btn btn-success waves-effect waves-light'
            }
          });
        }
      });
    };
  }

  $('#editIncomeForm').on('submit', async function () {

    try {

      let deposit = $('#modalEditDeposit').val();

      // =========================
      // FORMAT CLEAN (hapus titik/koma)
      // =========================
      deposit = deposit.replace(/\./g, '').replace(/,/g, '');

      const btn = $(this).find('button[type="submit"]');
      btn.prop('disabled', true).text('Processing...');

      const status = $('#modalEditStatus').val();

      const res = await fetch('/api/orders/pay', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: 'Bearer ' + window.jwtToken
        },
        body: JSON.stringify({
          order_id: orderId,
          deposit: Number(deposit || 0),
          status: status,
          trxType: 'sales',
          branch_name: window.branchName
        })
      });

      const json = await res.json();

      if (!json.status) {
        throw new Error(json.message);
      }

      alert('Payment updated ✅');

      location.reload();

    } catch (err) {

      console.error(err);
      alert('Gagal update');

    }

  });

  $('#modalEditStatus').on('change', function () {
    if ($(this).val() === 'paid') {
      $('#modalEditDeposit').val('').prop('disabled', true);
    } else {
      $('#modalEditDeposit').prop('disabled', false);
    }
  });

  //for custom year
  function getCurrentYear() {
    var currentYear = new Date().getFullYear();
    return currentYear;
  }

  var year = getCurrentYear();
  document.getElementById('order_date').innerHTML = year;
})();

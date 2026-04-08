/**
 * app-ecommerce-pengajuan-list Script
 */

'use strict';

// Datatable (jquery)

$(function () {
  let borderColor, bodyBg, headingColor;

  if (isDarkStyle) {
    borderColor = config.colors_dark.borderColor;
    bodyBg = config.colors_dark.bodyBg;
    headingColor = config.colors_dark.headingColor;
  } else {
    borderColor = config.colors.borderColor;
    bodyBg = config.colors.bodyBg;
    headingColor = config.colors.headingColor;
  }

  // Variable declaration for table

  var dt_order_table = $('.datatables-pengajuan'),
    statusObj = {
      1: { title: 'Still Pending', class: 'bg-label-warning' },
      2: { title: 'Already Paid', class: 'bg-label-success' },
      3: { title: 'Deposit Payment', class: 'bg-label-info' },
      4: { title: 'Error', class: 'bg-label-danger' },
      5: { title: 'Cancelled', class: 'bg-label-secondary' },
    },
    paymentObj = {
      1: { title: 'Paid', class: 'text-success' },
      2: { title: 'Pending', class: 'text-warning' },
      3: { title: 'Processing', class: 'text-info' },
      4: { title: 'Failed', class: 'text-danger' },
      5: { title: 'Cancelled', class: 'text-secondary' }
    };

  // E-commerce Products datatable

  if (dt_order_table.length) {

    var dt_products = dt_order_table.DataTable({

      ajax: {
        url: '/api/pengajuan',
        headers: {
          Authorization: 'Bearer ' + window.jwtToken
        },
        dataSrc: 'data'
      },

      columns: [
        { data: null },
        { data: null },
        { data: 'id' },
        { data: 'tanggal' },
        { data: 'nama' },
        { data: 'divisi' },
        { data: 'status' },
        { data: 'jabatan' },
        { data: null }
      ],

      columnDefs: [

        // RESPONSIVE
        {
          className: 'control',
          targets: 0,
          orderable: false,
          searchable: false,
          render: () => ''
        },

        // CHECKBOX
        {
          targets: 1,
          orderable: false,
          searchable: false,
          render: () => '<input type="checkbox" class="form-check-input">'
        },

        // ID PENGAJUAN
        {
          targets: 2,
          render: function (data, type, full) {
            return `
              <a href="javascript:void(0);" 
                 class="btn-detail" 
                 data-id="${full.id}">
                <span>#PG-${full.id}</span>
              </a>
            `;
          }
        },

        // TANGGAL
        {
          targets: 3,
          render: function (data) {

            if (!data) return '-';

            let date = new Date(data.split('-').reverse().join('-'));

            return date.toLocaleDateString('id-ID', {
              day: '2-digit',
              month: 'short',
              year: 'numeric'
            });
          }
        },

        // PEMOHON
        {
          targets: 4,
          render: function (data, type, full) {

            let name = full.nama;

            let initials = name.match(/\b\w/g) || [];
            initials = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();

            return `
              <div class="d-flex align-items-center">
                <div class="avatar me-2">
                  <span class="avatar-initial bg-label-primary rounded-circle">
                    ${initials}
                  </span>
                </div>
                <div>
                  <span class="fw-medium">${name}</span><br>
                  <small class="text-muted">${full.jabatan}</small>
                </div>
              </div>
            `;
          }
        },

        // DIVISI
        {
          targets: 5,
          render: function (data) {
            return `<span class="badge bg-label-info">${data}</span>`;
          }
        },

        // STATUS
        {
          targets: 6,
          render: function (data) {

            let map = {
              'Pengajuan': 'warning',
              'Proses': 'info',
              'Selesai': 'success'
            };

            let color = map[data] || 'secondary';

            return `<span class="badge bg-label-${color}">${data}</span>`;
          }
        },

        // JABATAN
        {
          targets: 7,
          render: function (data) {
            return `<span class="text-muted">${data}</span>`;
          }
        },

        // ACTIONS
        {
          targets: -1,
          orderable: false,
          searchable: false,
          render: function (data, type, full, meta) {
            return `
              <div class="d-flex justify-content-sm-center align-items-sm-center">
                
                <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="ti ti-dots-vertical"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-end m-0">

                  <!-- DETAIL -->
                  <a href="javascript:void(0);" 
                     class="dropdown-item btn-detail" 
                     data-id="${full.id}">
                    <i class="ti ti-eye me-2"></i> Detail
                  </a>

                  <!-- DELETE -->
                  <a href="javascript:void(0);" 
                     class="dropdown-item text-danger delete-record" 
                     data-id="${full.id}">
                    <i class="ti ti-trash me-2"></i> Delete
                  </a>

                </div>

              </div>
            `;
          }
        }

      ],
      order: [3, 'asc'], //set any columns order asc/desc
      dom:
        '<"card-header pb-md-2 d-flex flex-column flex-md-row align-items-start align-items-md-center"<f><"d-flex align-items-md-center justify-content-md-end mt-2 mt-md-0 gap-2"l<"dt-action-buttons"B>>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
      lengthMenu: [10, 40, 60, 80, 100], //for length of menu
      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search Order',
        info: 'Displaying _START_ to _END_ of _TOTAL_ entries'
      },
      // Buttons with Dropdown
      buttons: [
        {
          extend: 'collection',
          className: 'btn btn-label-secondary dropdown-toggle waves-effect waves-light',
          text: '<i class="ti ti-download me-1"></i>Export',
          buttons: [
            {
              extend: 'print',
              text: '<i class="ti ti-printer me-2"></i>Print',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3, 4, 5, 6, 7],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('order-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              },
              customize: function (win) {
                // Customize print view for dark
                $(win.document.body)
                  .css('color', headingColor)
                  .css('border-color', borderColor)
                  .css('background-color', bodyBg);
                $(win.document.body)
                  .find('table')
                  .addClass('compact')
                  .css('color', 'inherit')
                  .css('border-color', 'inherit')
                  .css('background-color', 'inherit');
              }
            },
            {
              extend: 'csv',
              text: '<i class="ti ti-file me-2"></i>Csv',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3, 4, 5, 6, 7],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('order-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: 'excel',
              text: '<i class="ti ti-file-export me-2"></i>Excel',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3, 4, 5, 6, 7],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('order-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: 'pdf',
              text: '<i class="ti ti-file-text me-2"></i>Pdf',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3, 4, 5, 6, 7],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('order-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: 'copy',
              text: '<i class="ti ti-copy me-2"></i>Copy',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3, 4, 5, 6, 7],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('order-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            }
          ]
        },
        {
            text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-sm-inline-block">Add Pengajuan</span>',
            className: 'create-new btn btn-primary waves-effect waves-light',
            action: function () {
                $('#modalPengajuan').modal('show');
            }
        }
      ],
      // For responsive popup
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['customer'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                ? '<tr data-dt-row="' +
                    col.rowIndex +
                    '" data-dt-column="' +
                    col.columnIndex +
                    '">' +
                    '<td>' +
                    col.title +
                    ':' +
                    '</td> ' +
                    '<td>' +
                    col.data +
                    '</td>' +
                    '</tr>'
                : '';
            }).join('');

            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      }
    });
    $('.dataTables_length').addClass('mt-0 mt-md-3 ms-n2');
    $('.dt-action-buttons').addClass('pt-0');
    $('.dataTables_filter').addClass('ms-n3');
  }

  // Delete Record
  $('.datatables-order tbody').on('click', '.delete-record', function () {
    dt_products.row($(this).parents('tr')).remove().draw();
  });

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);

  async function loadOrderSummary() {

    try {

      $.ajax({

        url: '/api/orders/summary',

        headers: {
          Authorization: 'Bearer ' + window.jwtToken
        },

        success: function(res){

          $('#order_pending').text(res.data.pending);
          $('#order_processing').text(res.data.processing);
          $('#order_completed').text(res.data.completed);
          $('#order_refunded').text(res.data.refunded);
          $('#order_failed').text(res.data.failed);

        }

      });

    }
    catch(err){
      console.error('order summary error',err);
    }

  }

  loadOrderSummary();

  $(document).on('click', '.btn-detail', async function () {

    const id = $(this).data('id');

    try {

      const res = await fetch(`/api/pengajuan/${id}`, {
        headers: {
          Authorization: 'Bearer ' + window.jwtToken
        }
      });

      const json = await res.json();

      const data = json.data;

      // ======================
      // HEADER
      // ======================
      $('#detail_nama').val(data.header.nama);
      $('#detail_divisi').val(data.header.divisi);
      $('#detail_jabatan').val(data.header.jabatan);
      $('#detail_tanggal').val(data.header.tanggal);

      // ======================
      // ITEMS
      // ======================
      let html = '';

      data.items.forEach(item => {
        html += `
          <tr>
            <td>${item.sparepart}</td>
            <td>${item.vendor_name || '-'}</td>
            <td>${item.qty}</td>
            <td>${item.satuan}</td>
            <td>Rp ${Number(item.harga).toLocaleString()}</td>
          </tr>
        `;
      });

      $('#detail_items').html(html);

      // ======================
      // SHOW MODAL
      // ======================
      $('#modalDetailPengajuan').modal('show');

    } catch (err) {
      console.error(err);
      Swal.fire('Error', 'Gagal load detail', 'error');
    }

  });
});

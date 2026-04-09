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
        url: '/api/inventory/list',
        headers: {
          Authorization: 'Bearer ' + window.jwtToken
        },
        dataSrc: 'data'
      },

      columns: [
        { data: null }, // responsive
        { data: null }, // checkbox
        { data: 'sparepart' },
        { data: 'total_qty' },
        { data: 'total_used' },
        { data: 'stock_available' },
        { data: 'satuan' },
        { data: 'vendor_name' },
        { data: null } // actions
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

        // NAMA BARANG
        {
          targets: 2,
          render: function (data, type, full) {
            return `
              <div class="fw-medium">${data}</div>
              <small class="text-muted">${full.vendor_name || '-'}</small>
            `;
          }
        },

        // QTY TOTAL
        {
          targets: 3,
          render: function (data, type, full) {
            return `<span class="fw-bold">${parseInt(data).toLocaleString()}</span>`;
          }
        },

        // TERPAKAI
        {
          targets: 4,
          render: function (data) {
            return `<span class="text-danger">${parseInt(data).toLocaleString()}</span>`;
          }
        },

        // SISA STOK
        {
          targets: 5,
          render: function (data) {

            let qty = parseInt(data);

            let color = qty <= 0 ? 'danger' : (qty < 10 ? 'warning' : 'success');

            return `<span class="badge bg-label-${color}">${qty}</span>`;
          }
        },

        // SATUAN
        {
          targets: 6,
          render: function (data) {
            return `<span class="text-muted">${data || '-'}</span>`;
          }
        },

        // VENDOR
        {
          targets: 7,
          render: function (data) {
            return `<span class="badge bg-label-info">${data || '-'}</span>`;
          }
        },

        // ACTIONS
        {
          targets: -1,
          orderable: false,
          searchable: false,
          render: function (data, type, full) {
            return `
              <div class="d-flex justify-content-sm-center">

                <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="ti ti-dots-vertical"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-end">

                  <a href="javascript:void(0);" 
                     class="dropdown-item btn-detail-item" 
                     data-id="${full.vendor_item_id}">
                    <i class="ti ti-eye me-2"></i> Detail
                  </a>

                  <a href="javascript:void(0);" 
                     class="dropdown-item text-warning btn-consume" 
                     data-id="${full.vendor_item_id}">
                    <i class="ti ti-minus me-2"></i> Pakai Barang
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

  async function loadInventorySummary() {

    try {

      $.ajax({

        url: '/api/inventory/stats',

        headers: {
          Authorization: 'Bearer ' + window.jwtToken
        },

        success: function(res){

          $('#inventory_total').text(res.data.total_items);
          $('#inventory_available').text(res.data.available);
          $('#inventory_empty').text(res.data.stok_habis);
          $('#inventory_low').text(res.data.stok_low);
          $('#inventory_today').text(res.data.today);

        }

      });

    }
    catch(err){
      console.error('inventory summary error', err);
    }

  }

  loadInventorySummary();

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

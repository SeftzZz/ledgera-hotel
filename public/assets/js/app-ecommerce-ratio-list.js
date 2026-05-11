/**
 * App eCommerce Category List
 */

'use strict';

const pathParts = window.location.pathname.split('/');

const hotelId  = pathParts[2]; // 3
const targetId = pathParts[4]; // 2

let TARGET = 0;
function formatRupiah(num) {
  return 'Rp ' + (num || 0).toLocaleString('id-ID');
}

// Comment editor
const commentEditor = document.querySelector('.comment-editor');

if (commentEditor) {
  new Quill(commentEditor, {
    modules: {
      toolbar: '.comment-toolbar'
    },
    placeholder: 'Enter category description...',
    theme: 'snow'
  });
}

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

  // Variable declaration for category list table
  var dt_category_list_table = $('.datatables-category-list');

  //select2 for dropdowns in offcanvas

  var select2 = $('.select2');
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        dropdownParent: $this.parent(),
        placeholder: $this.data('placeholder') //for dynamic placeholder
      });
    });
  }

  // Customers List Datatable

  if (dt_category_list_table.length) {
    var dt_category = dt_category_list_table.DataTable({
      ajax: {
        url: '/api/branches/ratio/' + targetId,
        headers: { Authorization: 'Bearer ' + window.jwtToken },
        dataSrc: function (json) {

          let result = [];

          Object.keys(json.data).forEach(function (key) {

            if (!key) return; // 🔥 skip empty

            let row = json.data[key];

            result.push({
              department: key,
              ratio_spend: row.ratio_spend?.[0] ?? null,
              ratio_worker: row.ratio_worker?.[0] ?? null,
              ratio_dw: row.ratio_dw?.[0] ?? null,
            });

          });

          return result;
        }
      },
      footerCallback: function (row, data, start, end, display) {

        let api = this.api();

        let totalSpend = 0;
        let totalWorker = 0;
        let totalDw = 0;
        let value_spend = 0;
        let value_worker = 0;
        let value_dw = 0;
        let rows = api.rows({ page: 'current' }).data();

        rows.each(function (row) {

          totalSpend += parseFloat(row.ratio_spend?.max_value || 0);
          value_spend = TARGET * totalSpend / 100;

          totalWorker += parseFloat(row.ratio_worker?.min_value || 0);
          value_worker = TARGET * totalWorker / 100;

          totalDw += parseFloat(row.ratio_dw?.max_value || 0);
          value_dw = TARGET * totalDw / 100;
        });

        $(api.column(2).footer()).html(`
          <div class="fw-bold text-primary">
            ${totalSpend.toFixed(2)}%<br>
            <small>${formatRupiah(value_spend)}</small>
          </div>
        `);

        $(api.column(3).footer()).html(`
          <div class="fw-bold text-success">
            ${totalWorker.toFixed(2)}%<br>
            <small>${formatRupiah(value_worker)}</small>
          </div>
        `);

        $(api.column(4).footer()).html(`
          <div class="fw-bold text-success">
            ${totalDw.toFixed(2)}%<br>
            <small>${formatRupiah(value_dw)}</small>
          </div>
        `);
      },
      columns: [
        { data: null },
        { data: 'department' },
        { data: 'ratio_spend' },
        { data: 'ratio_worker' },
        { data: 'ratio_dw' },
        { data: null },
        { data: null }
      ],
      columnDefs: [
        {
          // For Responsive
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 1,
          targets: 0,
          render: function (data, type, full, meta) {
            return '';
          }
        },
        {
          // Category Name
          targets: 1,
          render: function (data, type, full) {

            var $name = full.department;
            var $icon = full.icon;
            var $id = full.id;

            if ($icon) {

              var $output =
                '<img src="' + $icon + '" class="rounded-2">';

            } else {

              var stateNum = Math.floor(Math.random() * 6);
              var states = ['success','danger','warning','info','dark','primary','secondary'];
              var $state = states[stateNum];

              var $initials = $name.match(/\b\w/g) || [];
              $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();

              var $output =
                '<span class="avatar-initial rounded-2 bg-label-' + $state + '">' +
                $initials +
                '</span>';

            }

            return (
              '<div class="d-flex align-items-center">' +
                '<div class="avatar-wrapper me-2">' +
                  '<div class="avatar">' +
                    $output +
                  '</div>' +
                '</div>' +
                '<div class="d-flex flex-column">' +
                  '<span class="fw-medium">' + $name + '</span>' +
                '</div>' +
              '</div>'
            );

          }
        },
        {
          targets: 2,
          render: function (data, type, full) {

            let spend = full.ratio_spend;

            if (!spend) return '<span class="text-muted">-</span>';

            let percent = parseFloat(spend.max_value || 0);
            let value = TARGET * percent / 100;

            return `
              <div class="d-flex flex-column">
                <span class="fw-medium">${percent}%</span>
                <small class="text-muted">${formatRupiah(value)}</small>
              </div>
            `;
          }
        },
        {
          targets: 3,
          render: function (data, type, full) {

            let worker = full.ratio_worker;

            if (!worker) return '<span class="text-muted">-</span>';

            let percent = parseFloat(worker.min_value || 0);
            let value = TARGET * percent / 100;

            return `
              <div class="d-flex flex-column">
                <span class="fw-medium">${percent}%</span>
                <small class="text-muted">${formatRupiah(value)}</small>
              </div>
            `;
          }
        },
        {
          targets: 4,
          render: function (data, type, full) {

            let dw = full.ratio_dw;

            if (!dw) return '<span class="text-muted">-</span>';

            let percent = parseFloat(dw.max_value || 0);
            let value = TARGET * percent / 100;

            return `
              <div class="d-flex flex-column">
                <span class="fw-medium">${percent}%</span>
                <small class="text-muted">${formatRupiah(value)}</small>
              </div>
            `;
          }
        },
        {
          targets: 5,
          render: function (data, type, full) {

            let spend = full.ratio_spend;
            let worker = full.ratio_worker;
            let dw = full.ratio_dw;

            let isOver = (spend || worker || dw); // karena kita ambil label OVER

            return isOver
              ? '<span class="badge bg-label-danger">OVER</span>'
              : '<span class="badge bg-label-success">GOOD</span>';
          }
        },
        {
          // Actions
          targets: -1,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: function () {

            return (
              '<div class="d-flex align-items-center">' +
                '<button class="btn btn-sm btn-icon delete-record me-2">' +
                  '<i class="ti ti-trash"></i>' +
                '</button>' +
                '<button class="btn btn-sm btn-icon">' +
                  '<i class="ti ti-edit"></i>' +
                '</button>' +
              '</div>'
            );

          }
        }
      ],
      order: [2, 'desc'], //set any columns order asc/desc
      dom:
        '<"card-header d-flex flex-wrap pb-2"' +
        '<f>' +
        '<"d-flex justify-content-center justify-content-md-end align-items-baseline"<"dt-action-buttons d-flex justify-content-center flex-md-row mb-3 mb-md-0 ps-1 ms-1 align-items-baseline"lB>>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
      pageLength: -1,
      lengthMenu: [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "All"]
      ],
      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search Category'
      },
      // Button for offcanvas
      buttons: [
        {
          text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span class="d-none d-sm-inline-block">Add Items Ratio</span>',
          className: 'add-new btn btn-primary ms-2 waves-effect waves-light',
          attr: {
            'data-bs-toggle': 'offcanvas',
            'data-bs-target': '#offcanvasEcommerceCategoryList'
          }
        }
      ],
      // For responsive popup
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['categories'];
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
                    '<td> ' +
                    col.title +
                    ':' +
                    '</td> ' +
                    '<td class="ps-0">' +
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
    $('.dt-action-buttons').addClass('pt-0');
    $('.dataTables_filter').addClass('me-3 ps-0');
  }

  // Delete Record
  $('.datatables-category-list tbody').on('click', '.delete-record', function () {
    dt_category.row($(this).parents('tr')).remove().draw();
  });

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);
});

// Category Form Script
(function () {

  $('#formRatio').on('submit', function (e) {
    e.preventDefault();

    let formData = $(this).serializeArray();
    let data = {};

    formData.forEach(i => data[i.name] = i.value);

    // 🔥 TAMBAHKAN HOTEL ID
    data.hotel_id = hotelId;
    data.target_id = targetId;

    // =============================
    // MAPPING TYPE → API
    // =============================
    const endpointMap = {
      spend: '/api/branches/ratio-spend',
      worker: '/api/branches/ratio-worker',
      dw: '/api/branches/ratio-dw'
    };

    let url = endpointMap[data.type];

    if (!url) {
      Swal.fire('Error', 'Type tidak valid', 'error');
      return;
    }

    // =============================
    // DEFAULT VALUE (SAMAKAN BACKEND)
    // =============================
    if (!data.label) {
      data.label = 'OVER';
    }

    if (!data.min_value) {
      data.min_value = 0;
    }

    delete data.type;

    // =============================
    // AJAX
    // =============================
    $.ajax({
      url: url,
      type: 'POST',
      headers: {
        Authorization: 'Bearer ' + window.jwtToken
      },
      data: data,
      success: function (res) {

        if (res.status) {

          Swal.fire('Success', 'Ratio saved', 'success');

          // close offcanvas
          const offcanvasEl = document.getElementById('offcanvasEcommerceCategoryList');
          const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
          if (offcanvas) offcanvas.hide();

          // reload datatable
          $('.datatables-category-list').DataTable().ajax.reload();

          // reset form biar clean
          $('#formRatio')[0].reset();

        } else {
          Swal.fire('Error', res.message || 'Gagal simpan', 'error');
        }

      },
      error: function (xhr) {
        console.error(xhr.responseText);
        Swal.fire('Error', 'Server error', 'error');
      }
    });
  });
  /*
  =============================
  SUBMIT FORM
  =============================
  */

  document.addEventListener('DOMContentLoaded', function () {

    function loadTarget(targetId) {

      $.ajax({
        url: `/api/branches/target/${targetId}`,
        type: 'GET',
        headers: {
          Authorization: 'Bearer ' + window.jwtToken
        },
        success: function(res) {

          if (res.status) {

            let d = res.data;

            TARGET = d.target; // 🔥 SIMPAN GLOBAL

            // $('.datatables-category-list').DataTable().ajax.reload();

            $('#order_pending').text(formatRupiah(d.target));
            $('#order_completed').text(formatRupiah(d.room));
            $('#order_processing').text(formatRupiah(d.fb));
            $('#order_refunded').text(formatRupiah(d.tax));
            $('#order_failed').text(formatRupiah(d.margin));

          }

        }
      });
    }

    loadTarget(targetId);

    const form = document.getElementById('eCommerceCategoryListForm');
    if (!form) return;

    const fv = FormValidation.formValidation(form, {

      fields: {

        name: {
          validators: {
            notEmpty: {
              message: 'Please enter category name'
            }
          }
        },

        status: {
          validators: {
            notEmpty: {
              message: 'Please select category status'
            }
          }
        }

      },

      plugins: {

        trigger: new FormValidation.plugins.Trigger(),

        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: 'is-valid',
          rowSelector: '.mb-3'
        }),

        submitButton: new FormValidation.plugins.SubmitButton(),

        autoFocus: new FormValidation.plugins.AutoFocus()

      }

    });

    /*
    ========================
    FORM VALID
    ========================
    */

    fv.on('core.form.valid', function () {

      console.log('submit triggered');

      $.ajax({

        url: '/api/categories/create',
        type: 'POST',

        headers: {
          Authorization: 'Bearer ' + window.jwtToken
        },

        data: $(form).serialize(),

        success: function () {

          alert('Category saved');

          $('.datatables-category-list').DataTable().ajax.reload();

          const offcanvas = bootstrap.Offcanvas.getInstance(
            document.getElementById('offcanvasEcommerceCategoryList')
          );

          if (offcanvas) offcanvas.hide();

          form.reset();

        },

        error: function () {
          alert('Failed to save category');
        }

      });

    });

  });

})();


/*
=================================
UPLOAD CATEGORY ICON
=================================
*/

$('#iconUpload').change(function () {

  var formData = new FormData();
  formData.append('file', this.files[0]);

  $.ajax({

    url: '/api/upload/category',
    type: 'POST',
    data: formData,

    processData: false,
    contentType: false,

    headers: {
      Authorization: 'Bearer ' + window.jwtToken
    },

    success: function (res) {

      $('#iconPath').val(res.data.path);

    },

    error: function () {

      alert('Icon upload failed');

    }

  });
});
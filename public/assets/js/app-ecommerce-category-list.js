/**
 * App eCommerce Category List
 */

'use strict';

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
        url: '/api/categories',
        headers: { Authorization: 'Bearer ' + window.jwtToken },
        dataSrc: 'data'
      },
      columns: [
        { data: '' },
        { data: 'id' },
        { data: 'name' },
        { data: 'items' },
        { data: 'status' },
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

            var $name = full.name;
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
          // Total items
          targets: 2,
          responsivePriority: 3,
          render: function (data, type, full, meta) {
            var $total_items = full['total_items'];
            return '<div>' + $total_items + '</div>';
          }
        },
        {
          // Status
          targets: 3,
          render: function (data, type, full) {

            var status = full.status;

            var badge =
              status == 'active'
              ? '<span class="badge bg-label-success">Active</span>'
              : '<span class="badge bg-label-secondary">Inactive</span>';

            return badge;

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
      lengthMenu: [7, 10, 20, 50, 70, 100], //for length of menu
      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search Department'
      },
      // Button for offcanvas
      buttons: [
        {
          text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span class="d-none d-sm-inline-block">Add Department</span>',
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

  const form = document.getElementById('eCommerceCategoryListForm');

  // stop jika form tidak ada
  if (!form) {
    return;
  }

  const fv = FormValidation.formValidation(form, {

    fields: {

      name: {
        validators: {
          notEmpty: {
            message: 'Please enter category name'
          },
          stringLength: {
            max: 100,
            message: 'Category name must be less than 100 characters'
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
        rowSelector: function () {
          return '.mb-3';
        }
      }),

      submitButton: new FormValidation.plugins.SubmitButton(),

      autoFocus: new FormValidation.plugins.AutoFocus()

    }

  });

  /*
  =============================
  SUBMIT FORM
  =============================
  */

  document.addEventListener('DOMContentLoaded', function () {

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

          alert('Department saved');

          $('.datatables-category-list').DataTable().ajax.reload();

          const offcanvas = bootstrap.Offcanvas.getInstance(
            document.getElementById('offcanvasEcommerceCategoryList')
          );

          if (offcanvas) offcanvas.hide();

          form.reset();

        },

        error: function () {
          alert('Failed to save department');
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
/**
 *  Logistics Dashboard
 */

'use strict';

(function () {
  let labelColor, headingColor;

  if (isDarkStyle) {
    labelColor = config.colors_dark.textMuted;
    headingColor = config.colors_dark.headingColor;
  } else {
    labelColor = config.colors.textMuted;
    headingColor = config.colors.headingColor;
  }

  // Chart Colors
  const chartColors = {
    donut: {
      series1: config.colors.primary,
      series2: config.colors.warning,
      series3: '#28c76f80',
      series4: config.colors_label.success
    },
    line: {
      series1: config.colors.warning,
      series2: config.colors.primary,
      series3: config.colors.danger
    }
  };

  const dt_expense_table = $('.datatables-expense');

  if (dt_expense_table.length) {

    const dt_expense = dt_expense_table.DataTable({

      ajax: {
        url: 'department-expense',
        headers: {
          Authorization: 'Bearer ' + window.jwtToken
        },
        dataSrc: 'data'
      },

      columns: [
        { data: null },
        { data: null },
        { data: 'journal.journal_date' },
        { data: 'department_name' },
        { data: 'account.name' },
        { data: 'item.sparepart' },
        { data: 'item.qty' },
        { data: 'account.amount' },
        { data: 'pengajuan.nama' },
        { data: 'item.no_po' },
        { data: 'journal.journal_no' }
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

        // DATE
        {
          targets: 2,
          render: function (data) {

            if (!data) return '-';

            return new Date(data).toLocaleDateString('id-ID', {
              day: '2-digit',
              month: 'short',
              year: 'numeric'
            });
          }
        },

        // DEPARTMENT
        {
          targets: 3,
          render: function (data) {

            return `
              <span class="badge bg-label-primary">
                ${data}
              </span>
            `;
          }
        },

        // ACCOUNT
        {
          targets: 4,
          render: function (data, type, full) {

            return `
              <div>
                <strong>
                  ${full.account.code}
                </strong>
                <br>
                <small class="text-muted">
                  ${data}
                </small>
              </div>
            `;
          }
        },

        // ITEM
        {
          targets: 5,
          render: function (data) {

            return `
              <span class="fw-medium">
                ${data ?? '-'}
              </span>
            `;
          }
        },

        // QTY
        {
          targets: 6,
          className: 'text-center',
          render: function (data) {

            return `
              <span class="badge bg-label-info">
                ${parseFloat(data || 0)}
              </span>
            `;
          }
        },

        // AMOUNT
        {
          targets: 7,
          className: 'text-end',
          render: function (data) {

            return `
              <span class="fw-bold text-danger">
                Rp ${parseFloat(data || 0).toLocaleString('id-ID')}
              </span>
            `;
          }
        },

        // REQUESTER
        {
          targets: 8,
          render: function (data, type, full) {

            let initials = data.match(/\b\w/g) || [];

            initials = (
              (initials.shift() || '') +
              (initials.pop() || '')
            ).toUpperCase();

            return `
              <div class="d-flex align-items-center">

                <div class="avatar me-2">
                  <span class="avatar-initial rounded-circle bg-label-primary">
                    ${initials}
                  </span>
                </div>

                <div>
                  <span class="fw-medium">
                    ${data}
                  </span>
                </div>

              </div>
            `;
          }
        },

        // PO
        {
          targets: 9,
          render: function (data) {

            return `
              <span class="text-nowrap">
                ${data ?? '-'}
              </span>
            `;
          }
        },

        // JOURNAL
        {
          targets: 10,
          render: function (data) {

            return `
              <span class="text-nowrap">
                ${data ?? '-'}
              </span>
            `;
          }
        }
      ],

      order: [2, 'desc'],

      dom:
        '<"card-header pb-md-2 d-flex flex-column flex-md-row align-items-start align-items-md-center"<f><"d-flex align-items-md-center justify-content-md-end mt-2 mt-md-0 gap-2"l<"dt-action-buttons"B>>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',

      lengthMenu: [10, 25, 50, 100],

      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search Expense',
        info: 'Displaying _START_ to _END_ of _TOTAL_ entries'
      },

      responsive: {
        details: {
          type: 'column'
        }
      }

    });
  }
})();
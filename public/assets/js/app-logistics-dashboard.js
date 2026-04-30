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

  // Shipment statistics Chart
  // --------------------------------------------------------------------
  const shipmentEl = document.querySelector('#shipmentStatisticsChart'),
    shipmentConfig = {
      series: [
        {
          name: 'Revenue',
          type: 'column',
          data: window.dashboardData.historyRevenue
        },
        {
          name: 'Cash In',
          type: 'line',
          data: window.dashboardData.historyCash
        },
        {
          name: 'Outstanding',
          type: 'line',
          data: window.dashboardData.historyOutstanding
        }
      ],
      chart: {
        height: 270,
        type: 'line',
        stacked: false,
        parentHeightOffset: 0,
        toolbar: {
          show: false
        },
        zoom: {
          enabled: false
        }
      },
      markers: {
        size: 4,
        colors: [config.colors.white],
        strokeColors: chartColors.line.series2,
        hover: {
          size: 6
        },
        borderRadius: 4
      },
      stroke: {
        curve: 'smooth',
        width: [0, 3, 3],
        lineCap: 'round'
      },
      legend: {
        show: true,
        position: 'bottom',
        markers: {
          width: 8,
          height: 8,
          offsetX: -3
        },
        height: 40,
        offsetY: 10,
        itemMargin: {
          horizontal: 10,
          vertical: 0
        },
        fontSize: '15px',
        fontFamily: 'Public Sans',
        fontWeight: 400,
        labels: {
          colors: headingColor,
          useSeriesColors: false
        },
        offsetY: 10
      },
      grid: {
        strokeDashArray: 8
      },
      colors: [chartColors.line.series1, chartColors.line.series2, chartColors.line.series3],
      fill: {
        opacity: [1, 1, 1]
      },
      plotOptions: {
        bar: {
          columnWidth: '30%',
          startingShape: 'rounded',
          endingShape: 'rounded',
          borderRadius: 4
        }
      },
      dataLabels: {
        enabled: false
      },
      xaxis: {
        categories: window.dashboardData.historyLabels
      },
      yaxis: {
        labels: {
          formatter: function (val) {
            return 'Rp ' + val.toLocaleString('id-ID');
          }
        }
      },
      responsive: [
        {
          breakpoint: 1400,
          options: {
            chart: {
              height: 270
            },
            xaxis: {
              labels: {
                style: {
                  fontSize: '10px'
                }
              }
            },
            legend: {
              itemMargin: {
                vertical: 0,
                horizontal: 10
              },
              fontSize: '13px',
              offsetY: 12
            }
          }
        },
        {
          breakpoint: 1399,
          options: {
            chart: {
              height: 415
            },
            plotOptions: {
              bar: {
                columnWidth: '50%'
              }
            }
          }
        },
        {
          breakpoint: 982,
          options: {
            plotOptions: {
              bar: {
                columnWidth: '30%'
              }
            }
          }
        },
        {
          breakpoint: 480,
          options: {
            chart: {
              height: 250
            },
            legend: {
              offsetY: 7
            }
          }
        }
      ]
    };
  if (typeof shipmentEl !== undefined && shipmentEl !== null) {
    window.shipmentChart = new ApexCharts(shipmentEl, shipmentConfig);
    window.shipmentChart.render();
  }

  // Reasons for delivery exceptions Chart
  // --------------------------------------------------------------------
  document.addEventListener("DOMContentLoaded", function () {

    if (!window.dashboardData) {
      console.error('dashboardData tidak ditemukan');
      return;
    }

    if (typeof ApexCharts === 'undefined') {
      console.error('ApexCharts belum load');
      return;
    }

    // 🔥 sekarang pakai branches (nested)
    const branches = window.dashboardData.branches || [];

    if (!branches.length) {
      console.warn('Data chart kosong');
      return;
    }

    branches.forEach((branch, i) => {

      if (!branch.items || !branch.items.length) return;

      branch.items.forEach((item, j) => {

        const el = document.querySelector(`#deliveryExceptionsChart-${i}-${j}`);

        if (!el) {
          console.warn(`Element #deliveryExceptionsChart-${i}-${j} tidak ditemukan`);
          return;
        }

        // ======================
        // DATA
        // ======================
        const revenueVal = Number(item.revenue) || 0;
        const expenseVal = Number(item.expense) || 0;
        const targetVal  = Number(item.target) || 0;
        const swVal      = Number(item.sw) || 0;

        // 🔥 KUNCI: normalize ke target
        const revenueScaled = revenueVal;
        const expenseScaled = expenseVal;

        // ❗ ini yang bikin beda per target
        const remaining = Math.max(targetVal - (revenueScaled + expenseScaled), 0);

        const seriesData = [
          revenueScaled,
          expenseScaled,
          remaining // dipakai untuk shaping
        ];

        const deliveryExceptionsChartConfig = {
          chart: {
            height: 420,
            parentHeightOffset: 0,
            type: 'donut'
          },
          labels: ['Revenue', 'Expense', ''],
          series: seriesData,
          colors: [
            chartColors?.donut?.series1 || '#28c76f',
            chartColors?.donut?.series2 || '#ea5455',
            '#e6e6e6',
          ],
          stroke: {
            width: 0
          },
          dataLabels: {
            enabled: false,
            formatter: function (val) {
              return parseInt(val) + '%';
            }
          },
          legend: {
            show: true,
            position: 'bottom',
            offsetY: 10,
            markers: {
              width: 8,
              height: 8,
              offsetX: -3
            },
            itemMargin: {
              horizontal: 15,
              vertical: 5
            },
            fontSize: '13px',
            fontFamily: 'Public Sans',
            fontWeight: 700,
            labels: {
              colors: typeof headingColor !== 'undefined' ? headingColor : '#6e6b7b',
              useSeriesColors: false
            },

            formatter: function (seriesName, opts) {

              if (!seriesName) return ''; // hide remaining

              let percent = 0;

              if (seriesName === 'Revenue') {
                percent = targetVal > 0
                  ? (revenueVal / targetVal) * 100
                  : 0;
              }

              if (seriesName === 'Expense') {
                percent = targetVal > 0
                  ? (expenseVal / targetVal) * 100
                  : 0;
              }

              return `${seriesName} (${percent.toFixed(2)}%)`;
            }
          },
          tooltip: {
            style: {
              fontSize: '13px',
              fontFamily: 'Public Sans',
              color: '#ffffff'
            },
            y: {
              formatter: function(val, opts) {

                const index = opts.seriesIndex;

                if (index === 0) {
                  return 'Rp ' + Number(val).toLocaleString();
                }

                if (index === 1) {
                  return 'Rp ' + Number(val).toLocaleString();
                }

                if (index === 2) {
                  return 'Rp ' + Number(val).toLocaleString();
                }

                return 'Rp ' + Number(val).toLocaleString();
              }
            }
          },
          grid: {
            padding: {
              top: 15
            }
          },
          states: {
            hover: {
              filter: {
                type: 'none'
              }
            }
          },
          plotOptions: {
            pie: {
              donut: {
                size: '77%',
                labels: {
                  show: true,
                  value: {
                    fontSize: '18px',
                    fontFamily: 'Public Sans',
                    color: typeof headingColor !== 'undefined' ? headingColor : '#000',
                    fontWeight: 500,
                    offsetY: -30,
                    formatter: function (val) {
                      return 'Rp ' + parseInt(val).toLocaleString();
                    }
                  },
                  name: {
                    offsetY: 20,
                    fontFamily: 'Public Sans'
                  },
                  total: {
                    show: true,
                    fontSize: '.75rem',
                    label: branch.branch_name,
                    color: typeof labelColor !== 'undefined' ? labelColor : '#999',
                    formatter: function () {
                      return 'Rp ' + revenueVal.toLocaleString(); // 🔥 tetap revenue saja
                    }
                  }
                }
              }
            }
          },
          responsive: [
            {
              breakpoint: 420,
              options: {
                chart: {
                  height: 360
                }
              }
            }
          ]
        };

        try {
          const chart = new ApexCharts(el, deliveryExceptionsChartConfig);
          chart.render();
        } catch (err) {
          console.error(`Render chart ${branch.branch_name} gagal:`, err);
        }

      });

    });

  });
})();
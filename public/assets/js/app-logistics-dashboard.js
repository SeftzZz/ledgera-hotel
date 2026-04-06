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
      series3: '#7367f029'
    }
  };

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

    const labels = window.dashboardData.branchLabels || [];
    const revenues = window.dashboardData.branchRevenue || [];
    const expenses = window.dashboardData.branchExpense || [];
    const targets = window.dashboardData.branchTargets || [];
    const sw = window.dashboardData.branchSW || [];

    if (!labels.length) {
      console.warn('Data chart kosong');
      return;
    }

    labels.forEach((branch, i) => {
      const el = document.querySelector(`#deliveryExceptionsChart-${i}`);

      if (!el) {
        console.warn(`Element #deliveryExceptionsChart-${i} tidak ditemukan`);
        return;
      }

      // data per branch
      const seriesData = [
        revenues[i] || 0,
        expenses[i] || 0
      ];

      const deliveryExceptionsChartConfig = {
        chart: {
          height: 420,
          parentHeightOffset: 0,
          type: 'donut'
        },
        labels: ['Revenue', 'Expense'], //override per branch
        series: seriesData,
        colors: [
          chartColors?.donut?.series1 || '#28c76f',
          chartColors?.donut?.series2 || '#ea5455',
          chartColors?.donut?.series3 || '#ff9f43',
          chartColors?.donut?.series4 || '#00cfe8',
          chartColors?.donut?.series5 || '#7367f0'
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
          
          // Persentase Revenue & Expense
          formatter: function (seriesName, opts) {
            const value = opts.w.globals.series[opts.seriesIndex];
            const target = targets[i] || 0;
            const totalSW = sw[i] || 0;

            // ambil total expense pembagi dari localStorage
            // const totalSW = parseFloat(localStorage.getItem('dashboard_total_sw') || 0);
            
            let percent = 0;

            if (seriesName === 'Revenue') {
              percent = target > 0
                ? (value / target) * 100
                : 0;
            }

            if (seriesName === 'Expense') {
              percent = totalSW > 0
                ? (value / totalSW) * 100
                : 0;
              // percent = totalSW;
            }

            return `${seriesName} (${percent.toFixed(2)}%)`;
          }
        },
        tooltip: {
          theme: false,
          y: {
            formatter: function(val) {
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
                  label: branch, // 🔥 nama branch di tengah
                  color: typeof labelColor !== 'undefined' ? labelColor : '#999',
                  formatter: function () {
                    const total = seriesData.reduce((a,b)=>a+b,0);
                    return 'Rp ' + total.toLocaleString();
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
        console.error(`Render chart ${branch} gagal:`, err);
      }

    });

  });
})();
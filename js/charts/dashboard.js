(function (jQuery) {
    "use strict";

    if (document.querySelectorAll('#myChart').length) {
      let chartElement = document.querySelector("#myChart");
      let correctCount = parseInt(chartElement.getAttribute("data-correct")) || 0;
      let misplacedCount = parseInt(chartElement.getAttribute("data-misplaced")) || 0;
      let total = correctCount + misplacedCount;
  
      let correctPercentage = total > 0 ? (correctCount / total) * 100 : 0;
      let misplacedPercentage = total > 0 ? (misplacedCount / total) * 100 : 0;
  
      const options = {
          series: [correctPercentage, misplacedPercentage],
          chart: {
              height: 230,
              type: 'radialBar',
          },
          colors: ["#3a57e8", "#f44336"], // Blue for Correct, Red for Misplaced
          labels: ["Correct", "Misplaced"],
          dataLabels: {
              enabled: true,
              formatter: function (val) {
                  return val.toFixed(1) + "%";
              }
          },
          tooltip: {
              y: {
                  formatter: function (val) {
                      return val.toFixed(1) + "%";
                  }
              }
          }
      };
  
      if (ApexCharts !== undefined) {
          const chart = new ApexCharts(document.querySelector("#myChart"), options);
          chart.render();
  
          document.addEventListener('ColorChange', (e) => {
              const newOpt = { colors: [e.detail.detail2, e.detail.detail1] };
              chart.updateOptions(newOpt);
          });
  
          // Hover effect for fading colors
          let correctItem = document.querySelector(".d-flex.align-items-start:nth-child(1)"); // Correct
          let misplacedItem = document.querySelector(".d-flex.align-items-start:nth-child(2)"); // Misplaced
  
          correctItem.addEventListener("mouseenter", () => {
              chart.updateOptions({ colors: ["#3a57e8", "rgba(244, 67, 54, 0.3)"] }); // Fade red (Misplaced)
          });
  
          correctItem.addEventListener("mouseleave", () => {
              chart.updateOptions({ colors: ["#3a57e8", "#f44336"] }); // Restore original colors
          });
  
          misplacedItem.addEventListener("mouseenter", () => {
              chart.updateOptions({ colors: ["rgba(58, 87, 232, 0.3)", "#f44336"] }); // Fade blue (Correct)
          });
  
          misplacedItem.addEventListener("mouseleave", () => {
              chart.updateOptions({ colors: ["#3a57e8", "#f44336"] }); // Restore original colors
          });
      }
  }
  
  
  
  if (document.querySelectorAll('#d-activity').length) {
    let chartElement = document.querySelector("#d-activity");

    // Retrieve data from Blade attributes
    let overstockCount = parseInt(chartElement.getAttribute("data-overstock")) || 0;
    let understockCount = parseInt(chartElement.getAttribute("data-understock")) || 0;
    let normalCount = parseInt(chartElement.getAttribute("data-normal")) || 0;

    const options = {
      series: [{
          name: 'Count',
          data: [overstockCount, understockCount, normalCount] // Data aligns with categories
      }],
        chart: {
            type: 'bar',
            height: 230,
            toolbar: { show: true },
        },
        colors: ["#f44336", "#FFA500", "#3a57e8"], // Overstock = Red, Understock = Orange, Normal = Baby Blue
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '60%',
                borderRadius: 5,
                distributed: true,
            },
        },
        legend: { show: false }, // Show legend
        dataLabels: { enabled: false },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: ['Overstock', 'Understock',  'Normal'], // Separate categories for each bar
            labels: {
              minHeight:20,
              maxHeight:20,
              style: {
                colors: "#8A92A6",
              },
            }
  
        },
        yaxis: {
            title: { text: 'Count' },
            labels: {
                style: { colors: "#8A92A6" },
            }
        },
        fill: { opacity: 1 },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " items";
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#d-activity"), options);
    chart.render();
}

    if (document.querySelectorAll('#d-main').length) {
      const options = {
          series: [{
              name: 'total',
              data: [94, 80, 94, 80, 94, 80, 94]
          }],
          chart: {
              fontFamily: '"Inter", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
              height: 245,
              type: 'area',
              toolbar: {
                  show: false
              },
              sparkline: {
                  enabled: false,
              },
          },
          colors: ["#3a57e8"],
          dataLabels: {
              enabled: false
          },
          stroke: {
              curve: 'smooth',
              width: 3,
          },
          yaxis: {
            show: true,
            labels: {
              show: true,
              minWidth: 19,
              maxWidth: 19,
              style: {
                colors: "#8A92A6",
              },
              offsetX: -5,
            },
          },
          legend: {
              show: false,
          },
          xaxis: {
              labels: {
                  minHeight: 22,
                  maxHeight: 22,
                  show: true,
                  style: {
                    colors: "#8A92A6",
                  },
              },
              lines: {
                  show: false
              },
              categories: ["Jan", "Feb", "Mar", "Apr", "Jun", "Jul", "Aug"]
          },
          grid: {
              show: false,
          },
          fill: {
              type: 'gradient',
              gradient: {
                  shade: 'dark',
                  type: "vertical",
                  shadeIntensity: 0,
                  inverseColors: true,
                  opacityFrom: .4,
                  opacityTo: .1,
                  stops: [0, 50, 80],
                  colors: ["#3a57e8"]
              }
          },
          tooltip: {
            enabled: true,
          },
      };
  
      const chart = new ApexCharts(document.querySelector("#d-main"), options);
      chart.render();
  
      document.addEventListener('ColorChange', (e) => {
        const newOpt = {
          colors: [e.detail.detail1],
          fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: "vertical",
                shadeIntensity: 0,
                gradientToColors: [e.detail.detail1],
                inverseColors: true,
                opacityFrom: .4,
                opacityTo: .1,
                stops: [0, 50, 60],
            }
          },
        }
        chart.updateOptions(newOpt)
      })
    }
  
  if ($('.d-slider1').length > 0) {
      const options = {
          centeredSlides: false,
          loop: false,
          slidesPerView: 4,
          autoplay:false,
          spaceBetween: 32,
          breakpoints: {
              320: { slidesPerView: 1 },
              550: { slidesPerView: 2 },
              991: { slidesPerView: 3 },
              1400: { slidesPerView: 3 },
              1500: { slidesPerView: 4 },
              1920: { slidesPerView: 6 },
              2040: { slidesPerView: 7 },
              2440: { slidesPerView: 8 }
          },
          pagination: {
              el: '.swiper-pagination'
          },
          navigation: {
              nextEl: '.swiper-button-next',
              prevEl: '.swiper-button-prev'
          },

          // And if we need scrollbar
          scrollbar: {
              el: '.swiper-scrollbar'
          }
      }
      let swiper = new Swiper('.d-slider1',options);

      document.addEventListener('ChangeMode', (e) => {
        if (e.detail.rtl === 'rtl' || e.detail.rtl === 'ltr') {
          swiper.destroy(true, true)
          setTimeout(() => {
              swiper = new Swiper('.d-slider1',options);
          }, 500);
        }
      })
  }

  })(jQuery)

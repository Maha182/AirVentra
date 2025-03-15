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
        // Get the data passed from Blade
        const misplacedData = JSON.parse(document.querySelector('#d-main').getAttribute('data-misplaced'));

        const options = {
            series: [{
                name: 'Total Misplaced',
                data: misplacedData.totals,
            }],
            chart: {
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
                    formatter: function (val) {
                        return Math.floor(val); // Ensure y-axis labels are integers
                    }
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
                categories: misplacedData.months, // Use the months from the database
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

// Additional Chart for Capacity Utilization
if (document.querySelectorAll('#capacityChart').length) {
    let chartElement = document.querySelector("#capacityChart");
    let usedCapacity = parseInt(chartElement.getAttribute("data-used")) || 0;
    let freeCapacity = parseInt(chartElement.getAttribute("data-free")) || 0;

    const options = {
        series: [usedCapacity, freeCapacity],
        chart: {
            type: 'pie',
            height: 250,
        },
        labels: ['Used Capacity', 'Free Capacity'],
        colors: ["#3a57e8", "#00E396"],
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " slots";
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#capacityChart"), options);
    chart.render();
}


// Top Problematic Locations (Bar Chart)
if (document.querySelectorAll('#problematicChart').length) {
    let chartElement = document.querySelector("#problematicChart");
    let problemData = JSON.parse(chartElement.getAttribute("data-problematic"));
    
    let locations = problemData.map(entry => entry.wrong_location);
    let errors = problemData.map(entry => entry.errors);
    
    const options = {
        series: [{ name: 'Errors', data: errors }],
        chart: {
            type: 'bar',
            height: 350
        },
        xaxis: {
            categories: locations
        },
        colors: ["#007BFF"],
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " errors";
                }
            }
        }
    };
    
    const chart = new ApexCharts(document.querySelector("#problematicChart"), options);
    chart.render();
}

// Zone Error Distribution (Pie Chart)
if (document.querySelectorAll('#zoneErrorChart').length) {
    let chartElement = document.querySelector("#zoneErrorChart");
    let errorData = JSON.parse(chartElement.getAttribute("data-zone-errors"));
    
    let zones = errorData.map(entry => entry.zone_name);
    let errors = errorData.map(entry => entry.errors);
    
    const options = {
        series: errors,
        chart: {
            type: 'pie',
            height: 250
        },
        labels: zones,
        colors: ["#3a57e8", "#00E396","#FFA500","#007BFF"],

        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " errors";
                }
            }
        }
    };
    
    const chart = new ApexCharts(document.querySelector("#zoneErrorChart"), options);
    chart.render();
}

})(jQuery);

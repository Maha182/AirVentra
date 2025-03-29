<x-app-layout :assets="$assets ?? []">
<head>
      <!-- Required meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <!-- Favicon -->
      <link rel="shortcut icon" href="images/favicon.ico" />
      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="css/bootstrap.min.css">
      <!-- Typography CSS -->
      <link rel="stylesheet" href="css/typography.css">
      <!-- Style CSS -->
      <link rel="stylesheet" href="css/style.css">
      <!-- Responsive CSS -->
      <link rel="stylesheet" href="css/responsive.css">
   </head>
    <!-- Warehouse Capacity Utilization Per Zone -->
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4 class="card-title">Warehouse Capacity Utilization Per Zone</h4>
        </div>
        <div class="card-body">
            <div id="capacity" class="capacity" data-zones='@json($zoneCapacity ?? [])'></div>
        </div>
    </div>



    <!-- Inventory Distribution by Location (Bar Chart) -->
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4 class="card-title">Inventory Distribution Chart</h4>
        </div>
        <div class="card-body">
            <div id="inventoryDistributionChart"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="header-title">
                <h4 class="card-title">Warehouse Capacity by Zone</h4>
            </div>
        </div>
        <div class="card-body">
            <div id="locationCapacity" style="height: 500px;"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            /** ========== 1. Warehouse Capacity Utilization Per Zone (Stacked Bar Chart) ========== **/
            let chartElement = document.querySelector("#capacity");
            if (chartElement) {
                let zoneData = JSON.parse(chartElement.getAttribute("data-zones") || "[]");
                if (zoneData.length > 0) {
                    let categories = zoneData.map(zone => zone.zone_name);
                    let usedCapacities = zoneData.map(zone => parseFloat(zone.used_capacity) || 0);
                    let freeCapacities = zoneData.map(zone => parseFloat(zone.free_capacity) || 0);

                    const zoneCapacityOptions = {
                        series: [
                            { name: 'Used Capacity', data: usedCapacities },
                            { name: 'Free Capacity', data: freeCapacities }
                        ],
                        chart: { type: 'bar', height: 350, stacked: true },
                        xaxis: { categories: categories },
                        colors: ["#3b82f6", "#00E396"],
                        tooltip: { y: { formatter: val => val + " slots" } }
                    };
                    new ApexCharts(chartElement, zoneCapacityOptions).render();
                } else {
                    console.warn("No data available for warehouse capacity utilization");
                }
            } else {
                console.error("Element #capacity not found");
            }




        /** ========== 2. Location Capacity Utilization (Pie Chart) ========== **/
        am4core.ready(function() {
    am4core.useTheme(am4themes_animated);
    var chart = am4core.create("locationCapacity", am4charts.PieChart3D);
    chart.hiddenState.properties.opacity = 0;
    chart.legend = new am4charts.Legend();

    // Chart data passed from the controller
    var chartData = @json($chartData);

    // Format chart data to include both used capacity for zones and free capacity for the warehouse
    chart.data = chartData.map(function(item) {
        return {
            zone: item.zone,
            used_capacity: item.used_capacity
        };
    });

    // Series for Used Capacity (for each zone and free capacity)
    var series = chart.series.push(new am4charts.PieSeries3D());
    series.colors.list = [
        am4core.color("#6ce6f4"),  // Baby blue
        am4core.color("#3b82f6"),  // Blue
        am4core.color("#1e3a8a"),  // Navy
        am4core.color("#00E396")   // Green (for free capacity)
    ];
    series.dataFields.value = "used_capacity";  // Bind to the used_capacity (which includes both zones and free capacity)
    series.dataFields.category = "zone";  // Category for each zone and the free capacity
});



            /** ========== 3. Inventory Distribution by Zone (Bar Chart) ========== **/
            const inventoryChartElement = document.querySelector("#inventoryDistributionChart");
                if (inventoryChartElement) {
                    const zoneProductCount = @json($zoneProductCount ?? []);
                    console.log("zoneProductCount Data:", zoneProductCount);  // Debugging output

                    if (zoneProductCount.length > 0) {
                        const inventoryCategories = zoneProductCount.map(zone => zone.zone_name);
                        const productCounts = zoneProductCount.map(zone => zone.product_count);

                        console.log("Categories:", inventoryCategories);  // Debugging output
                        console.log("Product Counts:", productCounts);   // Debugging output

                        const inventoryOptions = {
                            chart: { type: 'bar', height: 350 },
                            series: [{ name: 'Products in Zone', data: productCounts }],
                            colors: ["#3b82f6"],
                            xaxis: { categories: inventoryCategories },
                        };

                        new ApexCharts(inventoryChartElement, inventoryOptions).render();
                    } else {
                        console.warn("No data found for inventory distribution.");
                    }
                } else {
                    console.error("Element #inventoryDistributionChart not found");
                }



   });
</script>
</x-app-layout>

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
      <!-- DataTables CSS (in <head>) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- jQuery (before DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS (before your custom scripts) -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

   </head>

    <div class="col-md-12">
        <div class="row">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Warehouse Capacity by Zone</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div id="locationCapacity" style="width: 100%; height: 400px;"></div>
                </div>
            </div>

        </div>
    </div>

    <div class="container">
        <h1>Product Stock Levels</h1>
        
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Stock Level Legend</h5>
                </div>
                <div class="card-body">
                    <span class="badge bg-success">Normal Stock</span> - Stock is between min and max levels
                    <span class="badge bg-warning ms-2">Understock</span> - Below minimum stock level
                    <span class="badge bg-danger ms-2">Overstock</span> - Above maximum stock level
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>Product Stock Status</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="product-stock-table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Title</th>
                                    <th>Current Stock</th>
                                    <th>Min Stock</th>
                                    <th>Max Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->title }}</td>
                                    <td>{{ $product->total_stock }}</td>
                                    <td>{{ $product->min_stock }}</td>
                                    <td>{{ $product->max_stock ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $product->status_class }}">
                                            {{ ucfirst($product->stock_status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
    <script>
        $(document).ready(function() {



            $('#product-stock-table').DataTable({
                pageLength: 10, // Default rows per page
                lengthMenu: [5, 10, 25, 50, 100],
                order: [[0, 'asc']], // Sort by Product ID initially
                language: {
                    search: "🔍 Search:",
                    lengthMenu: "Show _MENU_ entries",
                    paginate: {
                        next: "➡️",
                        previous: "⬅️"
                    }
                }
            });
        });

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

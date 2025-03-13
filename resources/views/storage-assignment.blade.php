@extends('layouts.home.app')

@section('content')

<meta charset="UTF-8">
<script>
        // Function to initialize the Python services after page load
        window.onload = function () {
            // Start the barcode detection service
            fetch('http://127.0.0.1:5002/start_service', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ service: 'barcode' })
            }).then(response => response.json())
            .then(data => console.log('Barcode service started:', data))
            .catch(error => console.error('Error starting barcode service:', error));

            // Start the storage assignment service
            fetch('http://127.0.0.1:5002/start_service', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ service: 'assignment' })
            }).then(response => response.json())
            .then(data => console.log('Assignment service started:', data))
            .catch(error => console.error('Error starting assignment service:', error));
        };
</script>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
    }
    .control-panel-btn {
        float: right;
        background-color: #00338d;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
    }
    .section-title {
        font-size: 24px;
        font-weight: bold;
        color: navy;
    }
    .error-report-table th, .error-report-table td {
        text-align: center;
    }
    
    .visualization {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: white;
    }

</style>

<div id="features" class="container-fluid bg-light py-5">
    <h4 class="display-4">Storage Assignment</h4>       
</div>

    @if (session('warning'))
        <div class="alert alert-warning text-center">{{ session('warning') }}</div>
    @endif

    @if (session('info'))
        <div class="alert alert-info text-center">{{ session('info') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success text-center" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger text-center" role="alert">
            {{ session('error') }}
        </div>
    @endif

<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <div class="bg-light p-4 border" style="height: 330px; margin-bottom: 30px;">
                <h4 class="section-title">(Live Video Feed with Barcode Detection)</h4>
                <div class="border mt-3" style="height: 220px; background-color: white;">
                    <img src="http://127.0.0.1:5000/video_feed" width="100%" height="100%" alt="Live Video Feed">
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="bg-light p-4 border" style="height: 330px;">
                <h4 class="section-title">Scanned Product</h4>
                <div class="justify-content-between align-items-start gap-3 col-md-6 p-4">
                    <p class="mb-4">Product ID: <strong id="product-id">{{ session('assigned_product.product_id') ?? '' }}</strong></p>
                    <p class="mb-4">Product Name: <strong id="product-name">{{ session('assigned_product.product_name') ?? '' }}</strong></p>
                    <p class="mb-4">Product Quantity: <strong id="product-quantity">{{ session('assigned_product.product_quantity') ?? '' }}</strong></p>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="bg-light p-4 border rounded">
                <!-- Header Section -->
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="section-title">Recommended Location #ID: 
                        <span class="text-primary" id="Location_id">{{ session('assigned_product.assigned_location') ?? ' ' }}</span>
                    </h4>
                    <h4 class="section-title align-items-center">Storage Utilization</h4> <!-- Moved Title to Same Line -->
                </div>

                <div class="d-flex justify-content-between align-items-stretch gap-3">
                    <!-- Left Side: Location Details -->
                    <div class="col-md-6 border p-4">
                        <p class="mb-4">Zone Name: <span id="zone_name">{{ session('assigned_product.zone_name') ?? ' ' }}</span></p>
                        <p class="mb-4">Aisle Number: <span id="aisle">{{ session('assigned_product.aisle') ?? ' ' }}</span></p>
                        <p class="mb-4">Rack Number: <span id="rack">{{ session('assigned_product.rack') ?? ' ' }}</span></p>
                    </div>

                    <!-- Right Side: Chart -->
                    <div class="col-md-6 card">
                        <div class="card-body d-flex justify-content-between align-items-center" style="height: 100%;">
                            <!-- Storage Chart -->
                            <div id="storageChart" data-used="{{ session('assigned_product.current_capacity') ?? 0 }}" data-total="{{ session('assigned_product.capacity') ?? 1 }}"></div>

                            <!-- Capacity Details -->
                            <div class="d-grid gap-4 ms-4">
                                <div class="d-flex align-items-start">
                                    <svg class="mt-2 icon-14" xmlns="http://www.w3.org/2000/svg" width="14"
                                        viewBox="0 0 24 24" fill="#4bc7d2">
                                        <g>
                                            <circle cx="12" cy="12" r="8" fill="#4bc7d2"></circle>
                                        </g>
                                    </svg>
                                    <div class="ms-3">
                                        <span class="text-gray">Used Capacity</span>
                                        <h6 id="used-capacity">0</h6> <!-- Start with 0 -->
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <svg class="mt-2 icon-14" xmlns="http://www.w3.org/2000/svg" width="14"
                                        viewBox="0 0 24 24" fill="#3a57e8">
                                        <g>
                                            <circle cx="12" cy="12" r="8" fill="#3a57e8"></circle>
                                        </g>
                                    </svg>
                                    <div class="ms-3">
                                        <span class="text-gray">Remaining Capacity</span>
                                        <h6 id="remaining-capacity">0</h6> <!-- Start with 0 -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Buttons -->
        <div class="d-flex justify-content-end mt-3 mb-5">
            <form method="POST" action="{{ route('assignProduct') }}">
                @csrf
                <button class="btn btn-primary" type="submit">Assign to Recommended</button>
            </form>
            <button class="btn btn-light border ms-2" data-bs-toggle="modal" data-bs-target="#manualAssignModal">
                Manually Assign
            </button>
        </div>
    </div>
</div>

 
<!-- Manual Storage Assignment Modal -->
<div class="modal fade" id="manualAssignModal" tabindex="-1" aria-labelledby="manualAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #001f3f;">
                <h5 class="modal-title text-white" id="manualAssignModalLabel">Manual Storage Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Error Message Inside Modal -->
                <div id="lookupError" class="alert alert-danger text-center d-none" role="alert">
                    Location ID not found!
                </div>

                <form action="{{ route('assignProduct') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label bg-light p-2 d-block">Location ID</label>
                        <input type="text" class="form-control" id="locationID" name="locationID" required>
                    </div>

                    <!-- Space between button and Zone Name -->
                    <div class="d-grid gap-3 mb-4">
                        <button type="button" class="btn btn-primary" id="Location-id">Look Up Location</button>
                    </div>
                    <div class="mb-3">
                        <label class="form-label bg-light p-2 d-block">Zone Name: <span id="zone_name" class="fw-bold text-dark"></span></label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label bg-light p-2 d-block">Aisle Number: <span id="aisle" class="fw-bold text-dark"></span></label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label bg-light p-2 d-block">Rack Number: <span id="rack" class="fw-bold text-dark"></span></label>
                    </div>

                    <div class="d-grid gap-3 mb-4">

                        <button class="btn btn-primary" type="submit">Assign Location</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let lastScannedBarcode = sessionStorage.getItem('lastScannedBarcode') || '';
        let chart; // Declare chart variable globally

        // Clear sessionStorage and product display fields on refresh
        sessionStorage.removeItem('lastScannedBarcode');
        document.getElementById('product-id').innerText = '';
        document.getElementById('product-name').innerText = '';
        document.getElementById('product-quantity').innerText = '';
        document.getElementById('Location_id').innerText = '';
        document.getElementById('zone_name').innerText = '';
        document.getElementById('aisle').innerText = '';
        document.getElementById('rack').innerText = '';

        // Initialize the chart
        function initializeChart() {
            let chartElement = document.querySelector("#storageChart");
            let usedCapacity = parseInt(chartElement.getAttribute("data-used")) || 0;
            let totalCapacity = parseInt(chartElement.getAttribute("data-total")) || 1;
            let remainingCapacity = totalCapacity - usedCapacity;

            // Calculate percentages
            let usedPercentage = totalCapacity > 0 ? (usedCapacity / totalCapacity) * 100 : 0;
            let remainingPercentage = totalCapacity > 0 ? (remainingCapacity / totalCapacity) * 100 : 0;

            const options = {
                series: [usedPercentage, remainingPercentage],
                chart: {
                    height: 300,
                    type: 'radialBar',
                },
                colors: ["#4bc7d2", "#3a57e8"], 
                labels: ["Used Capacity", "Remaining Capacity"],
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val.toFixed(1) + "%";
                    },
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold',
                        colors: ["#000"],
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val.toFixed(1) + "%";
                        }
                    }
                },
                plotOptions: {
                    radialBar: {
                        hollow: {
                            margin: 15,
                            size: '60%',
                        },
                        track: {
                            background: '#e7e7e7',
                            strokeWidth: '100%',
                        },
                        stroke: {
                            lineCap: 'round',
                            width: 10
                        }
                    }
                }
            };

            if (ApexCharts !== undefined) {
                chart = new ApexCharts(chartElement, options);
                chart.render();

                // Hover effect for fading colors
                let correctItem = document.querySelector(".d-flex.align-items-start:nth-child(1)"); 
                let misplacedItem = document.querySelector(".d-flex.align-items-start:nth-child(2)");

                correctItem.addEventListener("mouseenter", () => {
                    chart.updateOptions({ colors: ["#4bc7d2", "rgba(50, 53, 223, 0.29)"] }); 
                });

                correctItem.addEventListener("mouseleave", () => {
                    chart.updateOptions({ colors: ["#4bc7d2", "#3a57e8"]}); 
                });

                misplacedItem.addEventListener("mouseenter", () => {
                    chart.updateOptions({ colors: ["rgba(58, 232, 223, 0.3)", "#3a57e8"] });
                });

                misplacedItem.addEventListener("mouseleave", () => {
                    chart.updateOptions({ colors: ["#4bc7d2", "#3a57e8"] }); 
                });
            } else {
                console.error("ApexCharts is not loaded.");
            }
        }

        // Function to update the chart
        function updateChart(usedCapacity, totalCapacity) {
            let remainingCapacity = totalCapacity - usedCapacity;
            let usedPercentage = totalCapacity > 0 ? (usedCapacity / totalCapacity) * 100 : 0;
            let remainingPercentage = totalCapacity > 0 ? (remainingCapacity / totalCapacity) * 100 : 0;

            // Update the chart data
            chart.updateSeries([usedPercentage, remainingPercentage]);

            // Update the used and remaining capacity display
            document.getElementById('used-capacity').innerText = usedCapacity;
            document.getElementById('remaining-capacity').innerText = remainingCapacity;
        }

        // Fetch product data and update fields
        function fetchProductData() {
            fetch('/AirVentra/sendLocationData')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Received data:", data);

                    if (data.success && data.assigned_product) {
                        let assignedProduct = data.assigned_product;
                        let newBarcode = assignedProduct.product_id;

                        console.log("New Barcode:", newBarcode);

                        if (newBarcode && newBarcode !== lastScannedBarcode) {
                            console.log("New barcode detected!");

                            // Update sessionStorage immediately
                            sessionStorage.setItem('lastScannedBarcode', newBarcode);
                            lastScannedBarcode = newBarcode; // Update variable

                            // Update the product UI with new data
                            document.getElementById('product-id').innerText = assignedProduct.product_id || '';
                            document.getElementById('product-name').innerText = assignedProduct.product_name || '';
                            document.getElementById('product-quantity').innerText = assignedProduct.product_quantity || '';

                            document.getElementById('Location_id').innerText = assignedProduct.assigned_location || '';
                            document.getElementById('zone_name').innerText = assignedProduct.zone_name || '';
                            document.getElementById('aisle').innerText = assignedProduct.aisle || '';
                            document.getElementById('rack').innerText = assignedProduct.rack || '';

                            // Update the chart with new data
                            updateChart(assignedProduct.current_capacity, assignedProduct.capacity);
                        }
                    } else {
                        console.error("Error in response:", data.error);
                    }
                })
                .catch(error => {
                    console.error("Error fetching product data:", error);
                });
        }

        // Initialize the chart when the page loads
        initializeChart();

        // Fetch product data every 3 seconds (to keep UI updated)
        setInterval(fetchProductData, 3000);
    });
</script>


@endsection
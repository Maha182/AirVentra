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
        font-size: 18px;
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

    /* Container styling */
.custom-location-container {
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 15px; /* Reduced padding for smaller container */
}

/* Label styling */
.custom-label {
    font-weight: bold;
    font-size: 1rem; /* Smaller font size */
    color: #333;
    margin-bottom: 6px; /* Reduced margin for compact layout */
    display: block;
}

/* Styling for select dropdown */
.custom-select {
    width: 100%;
    padding: 8px 12px; /* Reduced padding for smaller size */
    font-size: 0.9rem; /* Smaller font size */
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #fff;
    transition: border-color 0.3s;
}

/* Focus effect for select */
.custom-select:focus {
    border-color: #3a57e8;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.2);
}

/* Additional spacing between select dropdown and the following elements */
.location-details-spacing {
    margin-bottom: 15px; /* Adjust space as needed */
}

/* You can also directly adjust the margin-bottom of the select element if preferred */
.custom-select {
    margin-bottom: 30px; /* Add space after the select element */
}
/* Margin adjustments for spacing between text elements */
.custom-text + .custom-text {
    margin-top: 8px; /* Reduced margin */
}





</style>
<div id="dynamic-success-alert"></div>

<div id="features" class="container-fluid bg-light py-4">
    <h4 class="display-4" style="color: navy">Storage Assignment</h4>       
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
<!-- Place this in your HTML body once -->


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

        <!-- Scanned Batch Section -->
        <div class="col-md-6">
            <div class="bg-light p-4 border" style="height: 330px;">
                <h4 class="section-title">Scanned Batch</h4>
                <div class="justify-content-between align-items-start gap-3 col-md-6 p-4">
                    <p class="mb-4">Batch ID: <strong id="batch-id">{{ session('assigned_product.batch_id') ?? '' }}</strong></p>
                    <p class="mb-4">Product Name: <strong id="product-name">{{ session('assigned_product.product_name') ?? '' }}</strong></p>
                    <p class="mb-4">Quantity: <strong id="batch-quantity">{{ session('assigned_product.batch_quantity') ?? '' }}</strong></p>
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
                        <span class="text-primary" id="Location_id"></span>
                    </h4>
                    <h4 class="section-title align-items-center">Storage Utilization</h4> <!-- Moved Title to Same Line -->
                </div>

                <div class="d-flex justify-content-between align-items-stretch gap-3">
                    <!-- Left Side: Location Details -->
                    <div class="col-md-6 border p-4 custom-location-container">
                        <label for="locations" class="form-label custom-label">Choose a location:</label>
                        <select name="locations" id="locations" class="form-select custom-select" onchange="updateLocationData()">
                            <!-- Options will be dynamically added here -->
                        </select>

                        <!-- Added a custom class for spacing -->
                        <div class="location-details-spacing"></div> <!-- This is where extra space is added -->

                        <p class="mb-4">Zone Name: <strong id="zone_name"></strong></p>
                        <p class="mb-4">Aisle Number: <strong id="aisle"></strong></p>
                        <p class="mb-4">Rack Number: <strong id="rack"></strong></p>
                    </div>
                    <!-- Right Side: Chart -->
                    <div class="col-md-6 card">
                        <div class="card-body d-flex justify-content-between custom-location-container align-items-center" style="height: 100%;">
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
            <form method="POST" action="{{ route('assignProduct') }}" id="assignProductForm">
                @csrf
                <input type="hidden" name="selected_location_id" id="selectedLocationInput">
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
            @if (session('modal_error'))
                <div class="alert alert-danger text-center" role="alert">
                    ❌ {{ session('modal_error') }}
                </div>
            @endif
            @if (session('modal_success'))
                <div class="alert alert-success text-center" role="alert">
                    {{ session('modal_success') }}
                </div>
            @endif
                <!-- Error Message Inside Modal -->
                <div id="lookupError" class="alert alert-danger text-center d-none" role="alert">
                    Location ID not found!
                </div>

                <form action="{{ route('assignProduct') }}" method="POST">
                    @csrf
                    <input type="hidden" name="from_modal" value="1">
                    <div class="mb-3">
                        <label class="form-label bg-light p-2 d-block">Location ID</label>
                        <input type="text" class="form-control" name="selected_location_id"  id="locationID" required> 
                        </div>

                    <div class="d-grid gap-3 mb-4">
                    <button type="button" class="btn btn-primary" id="lookupLocationBtn">Look Up Location</button>
                   </div>

                    <div class="mb-3">
                        <label class="form-label bg-light p-2 d-block">Zone Name: <span id="zone_name_Modal" class="fw-bold text-dark"></span></label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label bg-light p-2 d-block">Aisle Number: <span id="aisle_Modal" class="fw-bold text-dark"></span></label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label bg-light p-2 d-block">Rack Number: <span id="rack_Modal" class="fw-bold text-dark"></span></label>
                    </div>

                    <div class="d-grid gap-3 mb-4">
                        <button class="btn btn-primary" type="submit">Assign Location</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


@if(session('open_modal'))
<script>
    window.addEventListener('load', function () {
        var myModal = new bootstrap.Modal(document.getElementById('manualAssignModal'));
        myModal.show();
    });
</script>
@endif

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(() => {
            document.querySelector('img[alt="Live Video Feed"]').src = "http://127.0.0.1:5000/video_feed";
        }, 4000); 

        let lastScannedBarcode = sessionStorage.getItem('lastScannedBarcode') || '';
        let chart; // Declare chart variable globally
        let assignedProductData;
        
        

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
        let isFetching = false;

        function fetchProductData(retries = 3) {
            if (isFetching) return;
            isFetching = true;

            fetch('/AirVentra/sendLocationData')
                .then(async response => {
                    const data = await response.json(); // always try to read the JSON
                    if (!response.ok) {
                        // Still show alert if message2 or error exists
                        const alertContainer = document.getElementById('dynamic-success-alert');
                        alertContainer.innerHTML = `
                            <div class="alert alert-danger text-center" role="alert">
                                ❌ ${data.message2 || data.error || 'An unexpected error occurred.'}
                            </div>
                        `;
                        setTimeout(() => {
                            alertContainer.innerHTML = '';
                        }, 20000);

                        // Throw to exit .then and skip the success logic
                        throw new Error(data.message2 || data.error || 'HTTP error');
                    }

                    return data;
                })
                .then(data => {
                    console.log("Received data:", data);

                    if (data.message) {
                        const alertContainer = document.getElementById('dynamic-success-alert');
                        alertContainer.innerHTML = `
                            <div class="alert alert-success text-center" role="alert">
                                ${data.message}
                            </div>
                        `;
                        setTimeout(() => {
                            alertContainer.innerHTML = '';
                        }, 20000);
                    }

                    if (data.success && data.assigned_product) {
                        let newBarcode = data.assigned_product.batch_id;

                        if (newBarcode && newBarcode !== lastScannedBarcode) {
                            lastScannedBarcode = newBarcode;
                            sessionStorage.setItem('lastScannedBarcode', newBarcode);

                            assignedProductData = data.assigned_product;
                            updateProductUI(assignedProductData);
                            updateChart(assignedProductData.current_capacity, assignedProductData.capacity);
                            populateLocationDropdown(assignedProductData);
                        }
                    } else {
                        // ✅ Show real error if available
                        const message = data.message2 || data.error || "❌ Unrecognized or invalid barcode. Please try again.";
                        const alertContainer = document.getElementById('dynamic-success-alert');
                        alertContainer.innerHTML = `
                            <div class="alert alert-danger text-center" role="alert">
                                ${message}
                            </div>
                        `;
                        setTimeout(() => {
                            alertContainer.innerHTML = '';
                        }, 20000);
                    }

                })
                .catch(error => {
                    console.error("Error fetching product data:", error);
                    // Already shown alert inside .then if response is not OK, so no need to repeat here
                })
                .finally(() => {
                    isFetching = false;
                });
        }


   
        function updateProductUI(productData) {
            document.getElementById('batch-id').innerText = productData.batch_id || '';
            document.getElementById('product-name').innerText = productData.product_name || '';
            document.getElementById('batch-quantity').innerText = productData.batch_quantity || '';
            document.getElementById('Location_id').innerText = productData.assigned_location?.id || '';
            document.getElementById('zone_name').innerText = productData.assigned_location?.zone_name || '';
            document.getElementById('aisle').innerText = productData.assigned_location?.aisle || '';
            document.getElementById('rack').innerText = productData.assigned_location?.rack || '';

        }

        function populateLocationDropdown(assignedProduct) {
            let locationsSelect = document.getElementById('locations');
            locationsSelect.innerHTML = ''; // Clear previous options

            let nearestOption = document.createElement('option');
            nearestOption.value = assignedProduct.nearest.id || '';
            nearestOption.textContent = 'First Available Location: ' + assignedProduct.nearest.zone_name;
            locationsSelect.appendChild(nearestOption);

            let freestOption = document.createElement('option');
            freestOption.value = assignedProduct.freest.id || '';
            freestOption.textContent = 'Freest Location: ' + assignedProduct.freest.zone_name;
            locationsSelect.appendChild(freestOption);

            let preferredLocation = document.createElement('option');
            preferredLocation.value = assignedProduct.assigned_location?.id || '';
            preferredLocation.textContent = 'Same Product Type Location: ' + assignedProduct.assigned_location?.zone_name;
            locationsSelect.appendChild(preferredLocation);

            locationsSelect.addEventListener('change', updateLocationData);
        }

        function updateLocationData() {
            if (!assignedProductData) return;

            const selectedLocationId = document.getElementById('locations').value;

            let selectedLocation = null;

            if (selectedLocationId == assignedProductData.freest?.id) {
                selectedLocation = assignedProductData.freest;
            } else if (selectedLocationId == assignedProductData.nearest?.id) {
                selectedLocation = assignedProductData.nearest;
            } else if (selectedLocationId == assignedProductData.assigned_location?.id) {
                selectedLocation = assignedProductData.assigned_location;
            }

            if (!selectedLocation) {
                console.error("Selected location not found!");
                return;
            }

            // Update the UI
            document.getElementById('Location_id').innerText = selectedLocation.id || '';
            document.getElementById('zone_name').innerText = selectedLocation.zone_name || '';
            document.getElementById('aisle').innerText = selectedLocation.aisle || '';
            document.getElementById('rack').innerText = selectedLocation.rack || '';

            updateChart(selectedLocation.current_capacity, selectedLocation.capacity);
            sessionStorage.setItem('selectedLocationId', selectedLocation.id);
            document.getElementById('selectedLocationInput').value = selectedLocation.id;
        }

        document.getElementById('lookupLocationBtn').addEventListener('click', function () {
            let locationID = document.getElementById('locationID').value;
            console.log("Location ID:", locationID);  // Check if locationID is being captured correctly
            fetch('/AirVentra/lookupLocation?locationID=' + locationID)
                .then(response => response.json())
                .then(data => {
                    console.log(data);  // Debug the response data
                    if (data.success) {
                        document.getElementById('zone_name_Modal').textContent = data.data.zone_name;
                        document.getElementById('aisle_Modal').textContent = data.data.aisle;
                        document.getElementById('rack_Modal').textContent = data.data.rack;
                        document.getElementById('lookupError').classList.add('d-none');
                    } else {
                        document.getElementById('lookupError').classList.remove('d-none');
                    }
                })
                .catch(error => console.error('Error fetching location:', error));
        });
       
        initializeChart();
        fetchProductData();
        if (!window.fetchIntervalSet) {  // Prevent multiple intervals
            window.fetchIntervalSet = true;
            setInterval(fetchProductData, 10000);
        }
    });
</script>


@endsection
@extends('layouts.home.app')

@section('content')

<meta charset="UTF-8">

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
            <div class="bg-light p-4 border" style="height: 330px;">
                <h4 class="section-title">(Live Video Feed with Barcode Detection)</h4>
                <div class="border mt-3" style="height: 220px; background-color: white;">
                    <img src="http://127.0.0.1:5000/video_feed" width="100%" height="100%" alt="Live Video Feed">
                </div>
            </div>
        </div>

            <div class="col-md-6">
                <div class="bg-light p-4 border" style="height: 330px;">
                    <h4 class="section-title">Scanned Product</h4>
                    <p>Product ID: <strong>{{ session('assigned_product.product_id') ?? '' }}</strong></p>
                    <p>Product Name: <strong>{{ session('assigned_product.product_name') ?? '' }}</strong></p>
                    <p>Product Description: <strong>{{ session('assigned_product.product_description') ?? '' }}</strong></p>
                    <p>Product Quantity: <strong>{{ session('assigned_product.product_quantity') ?? '' }}</strong></p>
                    <form action="{{ route('sendLocationData') }}" method="GET">
                        <input type="hidden" name="redirect_to" value="storage-assignment">
                        <button id="fetchDataBtn"  type="submit" class="btn btn-primary">Fetch Product Data</button>
                    </form>

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
                        <span class="text-primary">{{ session('assigned_product.assigned_location') ?? ' ' }}</span>
                    </h4>
                    <h4 class="section-title align-items-center">Storage Utilization</h4> <!-- Moved Title to Same Line -->
                </div>

                <div class="d-flex justify-content-between align-items-start gap-3">
                    <!-- Left Side: Location Details -->
                    <div class="col-md-6 border p-5">
                        <p><strong>Zone Name: </strong> <span id="zone_name">
                            {{ session('assigned_product.zone_name') ?? ' ' }}
                        </span></p>
                        
                        <p><strong>Aisle Number:</strong> <span id="aisle">
                            {{ session('assigned_product.aisle') ?? ' ' }}
                        </span></p>
                        
                        <p><strong>Rack Number:</strong> <span id="rack">
                            {{ session('assigned_product.rack') ?? ' ' }}
                        </span></p>
                    </div>

                    <!-- Right Side: Chart -->
                    <div class="col-md-6 card">
                        <div class="card-body">
                            <canvas id="storageChart" width="400" height="190"></canvas>
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



<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var ctx = document.getElementById('storageChart').getContext('2d');

        // Fetching data from the session
        var usedCapacity = {{ session('assigned_product.current_capacity')}};
        var totalCapacity = {{ session('assigned_product.capacity')}};
        var remainingCapacity = totalCapacity - usedCapacity;

        var data = {
            labels: ["Used Capacity", "Remaining Capacity"],
            datasets: [{
                label: "Storage Utilization",
                data: [usedCapacity, remainingCapacity],
                backgroundColor: ["#001F3F", "#28A745"] // Navy Blue & Green
            }]
        };

        var options = {
            responsive: true,
            maintainAspectRatio: false
        };

        new Chart(ctx, {
            type: 'pie',
            data: data,
            options: options
        });
    });
</script>



 
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
                        <button type="button" class="btn btn-primary" id="lookupLocation">Look Up Location</button>
                    </div>

                    <!-- Display Data in Labels with Gray Background -->
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

<script>
    // Check if the page is being refreshed
    if (performance.navigation.type === 1) {
        fetch("{{ route('clearSession') }}") // Call the route to clear the session only on a refresh
            .then(response => location.reload()); // Ensure it reloads after clearing
    }
</script>

@endsection
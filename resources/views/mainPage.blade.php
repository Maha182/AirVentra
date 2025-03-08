@extends('layouts.home.app')
@section('content')

<div id="features" class="container-fluid bg-light py-4">
    <h4 class="display-5">Scan Inventory</h4>

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
        font-size: 30px;
        font-weight: bold;
        color: navy;
    }
    .title {
        font-size: 22px;
        font-weight: bold;
        color: navy;
    }
    .error-report-table th, .error-report-table td {
        text-align: center;
    }
    </style>
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
   
        <div class="col-md-12 bg-light p-2 border">
            <div class="row">
                <div class="col-md-6">
                    <div class="p-3 py-2">
                    <p class="title">Rack # <span id="rack-id">{{ $location ?? '' }}</span></p>

                        <p class="title">Rack Capacity: <strong id="rack-capacity">mm{{ $locationCapacity ?? '' }}</strong></p>
                        <p class="title">Status: <strong id="status" data-status="{{ session('status') }}">{{ $status ?? '' }}</strong></p>
                    </div>
                </div>
                <!-- Button Container -->
                <div class="col-md-6 d-flex  align-items-center gap-2 p-3">
                    <button id="checkRackLevelButton" class="btn btn-primary w-75 py-2">Check Rack Level</button>
                    <form action="{{ route('Reset') }}" method="POST" class="w-100 text-center">
                        @csrf
                        <button type="submit" class="btn btn-primary w-75 py-2">Next Rack</button>
                    </form>
                </div>
            </div>
        </div>
</div>


<!-- Current Location and Product Scan Section -->
<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <div class="bg-light p-4 border" style="height: auto;">
                <h4 class="section-title">Current Location (Live Video Feed with Barcode Detection)</h4>
                <div class="border mt-3" style="height: 330px; background-color: white;">
                    <img src="http://127.0.0.1:5000/video_feed" width="100%" height="100%" alt="Live Video Feed">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scanning Progress and Error Report -->
<div class="container my-5">
    <div class="row">
        @php
            $product = session('product', []);
        @endphp
        <div class="col-md-6">
            <div class="bg-light p-4 border" style="height: 300px;">
                <h4 class="section-title">Scanned Product</h4>
                <p>Product ID: <strong id="product-id">{{ $product['product_id'] ?? '' }}</strong></p>
                <p>Product Name: <strong id="product-name">{{ $product['product_name'] ?? '' }}</strong></p>
                <p>Assigned Rack Number: <strong id="product-rack">{{ $product['rack'] ?? '' }}</strong></p>
                <p>Product Zone: <strong id="product-zone">{{ $product['zone_name'] ?? '' }}</strong></p>
                <p>Product Quantity: <strong id="product-quantity">{{ $product['product_quantity'] ?? '' }}</strong></p>

                
            </div>
        </div>
        
        <!-- Error Report -->
        <div class="col-md-6">
            <div class="bg-light p-4 border" style="height: 300px;">
                <h4 class="section-title">Error Report</h4>
                <div style="max-height: 200px; overflow-y: auto;">
                
                <table class="table table-bordered error-report-table">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Current Location</th>
                            <th>Correct Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="error-report-body">
                        <!-- This will be populated dynamically by JavaScript -->
                    </tbody>

                </table>


                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let lastScannedBarcode = sessionStorage.getItem('lastScannedBarcode') || '';

        // Clear session storage on refresh
        sessionStorage.removeItem('lastScannedBarcode');
        document.getElementById('product-id').innerText = '';
        document.getElementById('product-name').innerText = '';
        document.getElementById('product-rack').innerText = '';
        document.getElementById('product-zone').innerText = '';
        document.getElementById('product-quantity').innerText = '';
        document.getElementById('rack-id').innerText = '';
        document.getElementById('current-location').innerText = '';
        document.getElementById('rack-capacity').innerText = '';

        const checkButton = document.getElementById('checkRackLevelButton');
        let progressBar = document.getElementById('progress-bar');
        progressBar.style.width = `0%`;
        progressBar.setAttribute('aria-valuenow', 0);
        progressBar.innerText = `0% Scanned`;
        
        function fetchBarcodeAndCheckPlacement() {
            fetch('/AirVentra/getBarcode')
                .then(response => response.json())
                .then(data => {
                    if (data.barcode && data.barcode !== lastScannedBarcode) {
                        lastScannedBarcode = data.barcode;
                        sessionStorage.setItem('lastScannedBarcode', lastScannedBarcode);

                        fetch(`/AirVentra/check-placement?barcode=${lastScannedBarcode}`)
                            .then(response => response.json())
                            .then(data => {
                                let product = data.product;
                                document.getElementById('product-id').innerText = product.product_id || '';
                                document.getElementById('product-name').innerText = product.product_name || '';
                                document.getElementById('product-rack').innerText = product.rack || '';
                                document.getElementById('product-zone').innerText = product.zone_name || '';
                                document.getElementById('product-quantity').innerText = product.product_quantity || '';
                                
                                if (checkButton) {
                                    checkButton.addEventListener('click', function (event) {
                                        document.getElementById('rack-id').innerText = data.location || '';
                                        document.getElementById('current-location').innerText = data.locationzone || '';
                                        document.getElementById('rack-capacity').innerText = data.locationCapacity || '';

                                        event.preventDefault(); // Prevents default form submission
                                        fetchStatusUpdate();
                                    });
                                } else {
                                    console.error("Button #checkRackLevelButton not found!");
                                }

                            });
                    }
                })
                .catch(error => console.error("Error fetching barcode or checking placement:", error));
        }

        function fetchStatusUpdate() {
            fetch('/AirVentra/update_inventory')
            .then(response => response.json())
            .then(data => {
                document.getElementById('status').innerText = data.status || '';
            })
            .catch(error => console.error("Error updating inventory status:", error));
        }



        function fetchErrorReports() {
        fetch('/AirVentra/getErrorReports')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('error-report-body');
                tableBody.innerHTML = '';  // Clear existing content

                if (data.errorReports.length > 0) {
                    // Iterate over the error reports and populate the table
                    data.errorReports.forEach(error => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${error.product_id}</td>
                            <td>${error.wrong_location}</td>
                            <td>${error.correct_location}</td>
                            <td>
                                ${error.status === 'Pending' 
                                    ? '<button class="btn btn-danger btn-sm">Pending</button>' 
                                    : '<button class="btn btn-success btn-sm">Corrected</button>'}
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else {
                    // If no error reports, display a "No errors found" message
                    tableBody.innerHTML = `<tr><td colspan="4">No errors found.</td></tr>`;
                }
            })
            .catch(error => console.error("Error fetching error reports:", error));
    }

        fetchErrorReports();
        setInterval(fetchErrorReports, 3000);

        setInterval(fetchBarcodeAndCheckPlacement, 3000);
    });

</script>



@endsection

@extends('layouts.home.app')
@section('content')

<div id="features" class="container-fluid bg-light py-5">
    <h4 class="display-4">Scan Inventory</h4>

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

<!-- Current Location and Product Scan Section -->
<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <div class="bg-light p-4 border" style="height: auto;">
                <h4 class="section-title">Current Location (Live Video Feed with Barcode Detection)</h4>
                <div class="border mt-3" style="height: 220px; background-color: white;">
                    <img src="http://127.0.0.1:5000/video_feed" width="100%" height="100%" alt="Live Video Feed">
                </div>
            </div>
        </div>
        @php
            $product = session('assigned_product', []);
        @endphp
        <div class="col-md-6">
            <div class="bg-light p-4 border" style="height: 330px;">
                <h4 class="section-title">Scanned Product</h4>
                <p>Product ID: <strong id="product-id">{{ $product['product_id'] ?? '' }}</strong></p>
                <p>Product Name: <strong id="product-name">{{ $product['product_name'] ?? '' }}</strong></p>
                <p>Rack Number: <strong id="product-rack">{{ $product['rack'] ?? '' }}</strong></p>
                <p>Product Zone: <strong id="product-zone">{{ $product['zone_name'] ?? '' }}</strong></p>
                <p>Product Quantity: <strong id="product-quantity">{{ $product['product_quantity'] ?? '' }}</strong></p>

                <div class="d-flex gap-2">
                    <form action="{{ route('updateInventory') }}" method="POST" class="flex-grow-1">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">Check Rack level</button>
                    </form>
                    <form action="{{ route('Reset') }}" method="POST" class="flex-grow-1">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">Next Rack</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scanning Progress and Error Report -->
<div class="container my-5">
    <div class="row">
        <!-- Scanning Progress -->
        <div class="col-md-6">
            <div class="bg-light p-4 border" style="height: 330px;">
                <h4 class="section-title">Rack # <span id="rack-id"> {{ $product['location'] ?? '' }}</span></h4>
                <div class="progress my-3">
                    <div class="progress-bar" id="progress-bar" role="progressbar" 
                        aria-valuemax="100">
                         0% Scanned <!-- Default text, will be updated by JavaScript -->
                    </div>
                </div>
                <div class="border p-3">
                    @php
                        $scanPercentage = (session('assigned_product.current_capacity') ?? 0) / (session('assigned_product.capacity') ?? 1) * 100;
                    @endphp
                    <p>Current Location: <strong id="current-location">{{ session('assigned_product.zone_name') ?? '' }}</strong></p>
                    <p>Rack Capacity: <strong id="rack-capacity">{{ session('assigned_product.capacity') ?? '' }}</strong></p>
                    <p id="status">Status: 
                        @if($scanPercentage < 100)
                            <span class="text-danger">Incomplete</span>
                        @else
                            <span class="text-success">Complete</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <!-- Error Report -->
        <div class="col-md-6">
            <div class="bg-light p-4 border" style="height: 330px;">
                <h4 class="section-title">Error Report</h4>
                <div style="max-height: 220px; overflow-y: auto;">
                
                <table class="table table-bordered error-report-table">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Current Location</th>
                            <th>Correct Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($errorReports->isEmpty()) <!-- Check if there are any error reports -->
                            <tr><td colspan="4">No errors found.</td></tr>
                        @else
                            @foreach($errorReports as $error)
                                <tr>
                                    <td>{{ $error->product_id }}</td>
                                    <td>{{ $error->wrong_location }}</td>
                                    <td>{{ $error->correct_location }}</td>
                                    <td>
                                        @if ($error->status == 'Pending')
                                            <button class="btn btn-danger btn-sm">Pending</button>
                                        @else
                                            <button class="btn btn-success btn-sm">Corrected</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
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
        let processedProducts = new Set(); // Track processed product IDs

        // Remove scanned product details from session storage on refresh
        sessionStorage.removeItem('lastScannedBarcode');

        // Clear product display fields
        document.getElementById('product-id').innerText = '';
        document.getElementById('product-name').innerText = '';
        document.getElementById('product-rack').innerText = '';
        document.getElementById('product-zone').innerText = '';
        document.getElementById('product-quantity').innerText = '';

        // Reset progress bar
        let progressBar = document.getElementById('progress-bar');
        progressBar.style.width = `0%`;
        progressBar.setAttribute('aria-valuenow', 0);
        progressBar.innerText = `0% Scanned`;

        // Clear Rack, Location, Capacity, and Status fields
        document.getElementById('rack-id').innerText = '';
        document.getElementById('current-location').innerText = '';
        document.getElementById('rack-capacity').innerText = '';
        document.getElementById('status').innerText = 'Status: Incomplete'; // Default text

        console.log("Last Scanned Barcode from sessionStorage:", lastScannedBarcode);

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

                            // First fetch the placement check data
                            fetch('/AirVentra/check-placement', {
                                method: 'GET',
                                headers: { 'Content-Type': 'application/json' }
                            })
                            .then(response => {
                                return response.json(); // Return the response data to process errors
                            })
                            .then(() => {
                                // Fetch the error reports after the placement check
                                return fetch('/AirVentra/mainPage');
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                }
                                return response.json(); // Parse the JSON response
                            })
                            .then(data => {
                                console.log("Received error reports:", data);

                                // Dynamically update the UI with the error reports
                                const tableBody = document.getElementById('error-report-body');
                                tableBody.innerHTML = ''; // Clear any previous errors

                                // Check if there are errors
                                if (data.length > 0) {
                                    data.forEach(error => {
                                        const row = document.createElement('tr');

                                        row.innerHTML = `
                                            <td>${error.product_id}</td>
                                            <td>${error.wrong_location}</td>
                                            <td>${error.correct_location}</td>
                                            <td>${error.status === 'Pending' ? '<button class="btn btn-danger btn-sm">Pending</button>' : '<button class="btn btn-success btn-sm">Corrected</button>'}</td>
                                        `;

                                        tableBody.appendChild(row);
                                    });
                                } else {
                                    tableBody.innerHTML = `<tr><td colspan="4">No errors found.</td></tr>`;
                                }
                            })
                            .catch(error => {
                                console.error("Error fetching placement check or error reports:", error);
                            });

                            // Additional logic for updating the product UI (e.g., product details, progress bar, etc.)
                            document.getElementById('product-id').innerText = assignedProduct.product_id || '';
                            document.getElementById('product-name').innerText = assignedProduct.product_name || '';
                            document.getElementById('product-rack').innerText = assignedProduct.rack || '';
                            document.getElementById('product-zone').innerText = assignedProduct.zone_name || '';
                            document.getElementById('product-quantity').innerText = assignedProduct.product_quantity || '';

                            let currentCapacity = assignedProduct.current_capacity || 0;
                            let totalCapacity = assignedProduct.capacity || 1;
                            let scanPercentage = (totalCapacity > 0) ? (currentCapacity / totalCapacity) * 100 : 0;

                            progressBar.style.width = `${scanPercentage}%`;
                            progressBar.setAttribute('aria-valuenow', scanPercentage);
                            progressBar.innerText = `${Math.round(scanPercentage)}% Scanned`;

                            // Update Rack and Location info
                            document.getElementById('rack-id').innerText = assignedProduct.location || '';
                            document.getElementById('current-location').innerText = assignedProduct.zone_name || '';
                            document.getElementById('rack-capacity').innerText = assignedProduct.capacity || '';
                            document.getElementById('status').innerText = scanPercentage < 100 ? 'Status: Incomplete' : 'Status: Complete';
                        }
                    }
                })
                .catch(error => {
                    console.error("Error fetching product data:", error);
                });
        }

        // Fetch product data every 5 seconds
        setInterval(fetchProductData, 3000);
    });
</script>



@endsection

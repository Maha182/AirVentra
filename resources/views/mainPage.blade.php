@extends('layouts.home.app')
@section('content')

<div id="features" class="container-fluid bg-light py-4">
    <h4 class="display-4" style="color: navy">Scan Inventory</h4>       

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
                    <p class="title">Rack Capacity: <strong id="rack-capacity">{{ $locationCapacity ?? '' }}</strong></p>
                    <p class="title">Status: <strong id="status" data-status="{{ session('status') }}">{{ $status ?? '' }}</strong></p>
                </div>
            </div>
            <!-- Button Container -->
            <div class="col-md-6 d-flex align-items-center gap-2 p-3">
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
        updateElementText('product-id', '');
        updateElementText('product-name', '');
        updateElementText('product-rack', '');
        updateElementText('product-zone', '');
        updateElementText('product-quantity', '');
        updateElementText('rack-id', '');
        updateElementText('rack-capacity', '');
        updateElementText('status', '');

        const checkButton = document.getElementById('checkRackLevelButton');
        let progressBar = document.getElementById('progress-bar');
        if (progressBar) {
            progressBar.style.width = `0%`;
            progressBar.setAttribute('aria-valuenow', 0);
            progressBar.innerText = `0% Scanned`;
        }
        
        function fetchBarcodeAndCheckPlacement() {
            fetch('http://127.0.0.1:5000/get_barcode')
                .then(response => response.json())
                .then(data => {
                    if (data.barcode && data.barcode !== lastScannedBarcode) {
                        lastScannedBarcode = data.barcode;
                        sessionStorage.setItem('lastScannedBarcode', lastScannedBarcode);

                        fetch(`/AirVentra/check-placement?barcode=${lastScannedBarcode}`)
                            .then(response => response.json())
                            .then(data => {
                                console.log("API Response:", data); // Log the API response

                                if (data && data.product) {
                                    let product = data.product;
                                    updateElementText('product-id', product.product_id || '');
                                    updateElementText('product-name', product.product_name || '');
                                    updateElementText('product-rack', product.rack || '');
                                    updateElementText('product-zone', product.zone_name || '');
                                    updateElementText('product-quantity', product.product_quantity || '');
                                } else {
                                    console.error("Product data is undefined or null.");
                                }

                                // ✅ Use the same response to update error reports
                                if (data.errorReports) {
                                    updateErrorReportsTable(data.errorReports);
                                } else {
                                    console.error("Error reports data is missing.");
                                }
                            })
                            .catch(error => console.error("Error fetching product data:", error));
                    }
                })
                .catch(error => console.error("Error fetching barcode or checking placement:", error));
        }

        // ✅ Function to update the error reports table
        function updateErrorReportsTable(errorReports) {
            const tableBody = document.getElementById('error-report-body');
            if (!tableBody) {
                console.error("Element #error-report-body not found.");
                return;
            }

            tableBody.innerHTML = ''; // Clear existing content

            if (errorReports.length > 0) {
                errorReports.forEach(error => {
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
                tableBody.innerHTML = `<tr><td colspan="4">No errors found.</td></tr>`;
            }
        }


        function fetchStatusUpdate() {
            fetch('/AirVentra/update_inventory')
                .then(response => response.json())
                .then(data => {
                    updateElementText('status', data.status || '');
                })
                .catch(error => console.error("Error updating inventory status:", error));
        }


        function updateElementText(elementId, text) {
            const element = document.getElementById(elementId);
            if (element) {
                element.innerText = text;
            } else {
                console.error(`Element with ID ${elementId} not found.`);
            }
        }

        setInterval(fetchBarcodeAndCheckPlacement, 3000);
    });
</script>
@endsection
@extends('layouts.home.app')
@section('content')

    <!-- <meta charset="UTF-8"> -->
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
<div id="features" class="container-fluid bg-light py-5">
            <h4 class="display-4">Scan Inventory </h4>

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
            <div class="bg-light p-4 border" style="height: 330px;">
                <h4 class="section-title">Current Location (Live Video Feed with Barcode Detection)</h4>
                <div class="border mt-3" style="height: 220px; background-color: white;">
                    <img src="http://127.0.0.1:5000/video_feed" width="100%" height="100%" alt="Live Video Feed">
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="bg-light p-4 border" style="height: 330px;">
                <h4 class="section-title">Scanned Product</h4>
                @if(session()->has('assigned_product')) 
                    @php
                        $product = session('assigned_product');
                    @endphp
                @endif
                    <p>Product ID: <strong>{{ $product['product_id'] ?? '' }}</strong></p>
                    <p>Product Name: <strong>{{ $product['product_name'] ?? '' }}</strong></p>
                    <p>Rack Number: <strong>{{ $product['rack'] ?? '' }}</strong></p>
                    <p>Product Zone: <strong>{{ $product['zone_name'] ?? '' }}</strong></p>
                    <p>Product Quantity: <strong>{{ $product['product_quantity'] ?? '' }}</strong></p>
                    <div class="d-flex gap-2"> 
                        <form action="{{ route('sendLocationData') }}" method="GET" class="flex-grow-1">
                            <input type="hidden" name="redirect_to" value="mainPage">
                            <button type="submit" class="btn btn-primary w-100">Fetch Product Data</button>
                        </form>
                        <form action="{{ route('checkPlacement') }}" method="GET" class="flex-grow-1">
                            <button type="submit" class="btn btn-primary w-100">Check Placement</button>
                        </form>
                    </div>
            </div>
        </div>
    </div>
</div>


<!-- Scanning Progress and Error Report -->
<div class="container">
    <div class="row justify-content-end mt-3 mb-5">
        <div class="col-md-6">
            <div class="bg-light p-4 border" style=" height: 300px;">
                <h4 class="section-title">Rack #ID Inventory Scan</h4>
                <div class="progress my-3">
                    <div class="progress-bar" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">60% Scanned</div>
                </div>
                <div class="border p-3">
                    <p>Current Location: <strong>Text</strong></p>
                    <p>Rack Capacity: <strong>Text</strong></p>
                    <p>Status: <span class="text-danger">Incomplete</span></p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="bg-light p-4 border" style=" height: 300px;">
                <h4 class="section-title">Error Report</h4>
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
                        <tr>
                            <td>60001</td>
                            <td>L0003</td>
                            <td>D0005</td>
                            <td><button class="btn btn-success btn-sm">Corrected</button></td>
                        </tr>
                        <tr>
                            <td>60002</td>
                            <td>L0005</td>
                            <td>D0001</td>
                            <td><button class="btn btn-success btn-sm">Corrected</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection

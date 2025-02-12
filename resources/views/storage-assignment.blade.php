@extends('layouts.home.app')

@section('content')
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
                        <button type="submit" class="btn btn-primary">Fetch Product Data</button>
                    </form>

                </div>
            </div>
            
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="bg-light p-4 border">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="section-title">Recommended Location #ID: <span class="text-primary">{{session('assigned_product.assigned_location') ?? ' ' }}</span> </h4>
                    <!-- Form to send location data to Flask -->
                        <!-- <form id="lookupForm" method="GET" action="{{ route('sendLocationData') }}">
                            @csrf
                            <button class="btn btn-primary" type="submit">Look Up Location</button>
                        </form> -->
                </div>
                
                <div class="border p-4">
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
            </div>

            <div class="d-flex justify-content-end mt-3 mb-5">
                <form method="POST" action="{{ route('assignProduct') }}">
                    @csrf
                    <button class="btn btn-primary" type="submit">Assign to Recommended</button>
                </form>
                <button class="btn btn-light border" data-bs-toggle="modal" data-bs-target="#manualAssignModal">Manually Assign</button>
            </div>
        </div>
    </div>
</div>


    <!-- Display Assigned Location (Added Section) -->
    @if(session('assigned_location'))
    <div class="container my-4">
    <div class="row">
        <div class="col-md-12">
            <div class="bg-light p-4 border">
                <h4 class="section-title">Assigned Location</h4>
                <div class="border p-3">
                    <p>Location ID: <strong>{{ session('assigned_location')->locationID }}</strong></p>
                    <p>Zone Name: <strong>{{ session('assigned_location')->zone_name }}</strong></p>
                    <p>Aisle Number: <strong>{{ session('assigned_location')->aisle }}</strong></p>
                    <p>Rack Number: <strong>{{ session('assigned_location')->rack }}</strong></p>
                </div>
            </div>
        </div>
    </div>
    </div>
    @endif

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

                <!-- Add jQuery for AJAX -->
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    $(document).ready(function() {
                        $("#lookupLocation").click(function() {
                        var locationID = $("#locationID").val();

                        // Hide the error message initially
                        $("#lookupError").addClass("d-none");

                        // Clear previous data if input is empty
                        if (locationID === '') {
                            $("#lookupError").removeClass("d-none").text("Please enter a Location ID");
                            $("#zone_name").text(" ");
                            $("#aisle").text(" ");
                            $("#rack").text(" ");
                            return;
                        }

                        $.ajax({
                                url: "{{ route('lookup.location') }}",
                                type: "GET",
                                data: { locationID: locationID },
                                success: function(response) {
                                    if (response.success) {
                                        $("#zone_name").text(response.data.zone_name);
                                        $("#aisle").text(response.data.aisle);
                                        $("#rack").text(response.data.rack);
                                    } else {
                                        $("#lookupError").removeClass("d-none").text("Location ID not found!");
                                        $("#zone_name").text(" ");
                                        $("#aisle").text(" ");
                                        $("#rack").text(" ");
                                    }
                                },
                                error: function() {
                                    $("#lookupError").removeClass("d-none").text("An error occurred. Please try again.");
                                    $("#zone_name").text(" ");
                                    $("#aisle").text(" ");
                                    $("#rack").text(" ");
                                }
                            });
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>

@endsection
@extends('layouts.home.app')

@section('content')
<div id="features" class="container-fluid bg-light py-5">
    <div class="container py-5">
        <h4 class="display-4">Storage Assignment</h4>

        <!-- Success Messages -->
        @if(session('success'))
            <div class="alert alert-success text-center" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="row g-4 justify-content-center mt-4">
            <!-- Scanning Section -->
            <div class="col-md-6">
                <div class="border p-4 bg-white" style="min-height: 300px; width: 100%; border: 2px solidrgb(4, 75, 150);">
                    <!-- Scanning Area -->
                </div>
            </div>

            <!-- Scanned Product Details -->
            <div class="col-md-6">
                <div class="p-4" style="background-color: #D6EAF8; border-radius: 5px;">
                    <h5 class="fw-bold">Scanned Product</h5>
                    <div class="p-3 bg-white" style="color: black;">
                        <p><strong>Product ID:</strong> <span>Text</span></p>
                        <p><strong>Product Name:</strong> <span>Text</span></p>
                        <p><strong>Product Description:</strong> <span>Text</span></p>
                        <p><strong>Product Quantity:</strong> <span>Text</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommended Location Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="p-4 bg-white">
                    <h5 class="fw-bold">Recommended Location #ID: <span class="text-primary">ID number</span></h5>
                    <p><strong>Zone Name:</strong> <span>Text</span></p>
                    <p><strong>Aisle Number:</strong> <span>Text</span></p>
                    <p><strong>Rack Number:</strong> <span>Text</span></p>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-end mt-3">
            <button class="btn btn-primary me-2">Assign to Recommended</button>
            <!-- Button to trigger modal -->
            <button class="btn btn-light border" data-bs-toggle="modal" data-bs-target="#manualAssignModal">Manually Assign</button>
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

                <form action="{{ route('assign.manual') }}" method="POST">
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
                        <label class="form-label bg-light p-2 d-block">Zone Name: <span id="zone_name" class="fw-bold text-dark">N/A</span></label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label bg-light p-2 d-block">Aisle Number: <span id="aisle" class="fw-bold text-dark">N/A</span></label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label bg-light p-2 d-block">Rack Number: <span id="rack" class="fw-bold text-dark">N/A</span></label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Assign Location</button>
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
                            $("#zone_name").text("N/A");
                            $("#aisle").text("N/A");
                            $("#rack").text("N/A");
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
                                    $("#zone_name").text("N/A");
                                    $("#aisle").text("N/A");
                                    $("#rack").text("N/A");
                                }
                            },
                            error: function() {
                                $("#lookupError").removeClass("d-none").text("An error occurred. Please try again.");
                                $("#zone_name").text("N/A");
                                $("#aisle").text("N/A");
                                $("#rack").text("N/A");
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
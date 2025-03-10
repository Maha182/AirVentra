@extends('layouts.home.app')
@section('content')

<div id="features" class="container-fluid bg-light py-4">
    <h4 class="display-5">Scan Shelf</h4>

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
    .title {
        font-size: 20px;
        font-weight: bold;
        color: navy;
    }
    .section-title {
        font-size: 30px;
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
        <div class="col-md-12">
            <div class="bg-light p-4 border" style="height: 460px;">
                <h2 class="section-title">Current Shelf</h2>
                <div class="border mt-3" style="height: 370px; background-color: white;">
                    <img src="http://127.0.0.1:5000/video_feed" width="100%" height="100%" alt="Live Video Feed">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scanning Progress and Error Report -->
<div class="container my-5">
    <div class="row">
        <!-- Scanning Progress -->
        <div class="col-md-12">
            <div class="bg-light p-4 border" style="height: auto;">
                <div class="p-3">
                    <p class="title">Rack # <span id="rack-id">{{ session('current_rack') ?? 'Not Scanned' }}</span></p>
                    @php
                        $scanPercentage = ($locationCurrentcapacity ?? 0) / ($locationCapacity ?? 1) * 100;
                    @endphp
                    <p class="title">Current Location: <strong id="current-location">{{ session('location_zone') ?? 'Unknown' }}</strong></p>
                    <p class="title">Rack Capacity: <strong id="rack-capacity">{{ $locationCapacity ?? '' }}</strong></p>
                    <p class="title">Current Capacity: <strong id="current-capacity">{{ $locationCurrentcapacity ?? 0 }}</strong></p>
                </div>
                <button id="scan-rack-btn" class="control-panel-btn">Scan Shelf</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Rack Scanning -->
<script>
    document.getElementById('scan-rack-btn').addEventListener('click', function() {
        fetch("{{ route('scan-rack') }}")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Fill scanned shelf details
                    document.getElementById('rack-id').innerText = data.rack_id;
                    document.getElementById('current-location').innerText = data.zone;
                    document.getElementById('rack-capacity').innerText = data.capacity;
                    document.getElementById('current-capacity').innerText = data.current_capacity;
                    
                    alert('Rack scanned successfully!');

                    // Auto-redirect to mainPage after 5 seconds
                    setTimeout(function() {
                        window.location.href = "{{ route('mainPage') }}";
                    }, 5000); // 5-second delay
                } else {
                    alert(data.error || 'Failed to scan rack.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while scanning the rack.');
            });
    });
</script>


@endsection

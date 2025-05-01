@extends('layouts.home.app')
@section('content')

<div id="features" class="container-fluid bg-light py-4">
    <h4 class="display-4" style="color: navy">Scan Shelf</h4>  

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

<!-- JavaScript for Rack Scanning and Redirection -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(() => {
            document.querySelector('img[alt="Live Video Feed"]').src = "http://127.0.0.1:5000/video_feed";
        }, 4000);

        function fetchRackData() {
            fetch("{{ url('scan-rack') }}")
                .then(response => response.json())
                .then(data => {
                    console.log(data);  // Log the data to check for any issues
                    if (data.success && data.rack_id) {
                        // Call Flask reset
                        fetch('http://127.0.0.1:5000/reset_barcodes', { method: 'POST' });

                        // ✅ Display success alert dynamically
                        const alertContainer = document.createElement('div');
                        alertContainer.innerHTML = `
                            <div id="rack-scan-alert" class="alert alert-success text-center mt-3" role="alert">
                                ✅ Rack scanned successfully. Redirecting to inventory...
                            </div>
                        `;
                        const featuresSection = document.getElementById('features');
                        if (featuresSection) {
                            featuresSection.appendChild(alertContainer);
                        }

                        // ⏳ Optional fade out before redirect
                        setTimeout(() => {
                            const alert = document.getElementById('rack-scan-alert');
                            if (alert) {
                                alert.style.transition = "opacity 1s";
                                alert.style.opacity = 0;
                            }
                        }, 2500);

                        // 🚀 Redirect after 3 seconds with message param
                        setTimeout(function () {
                            const redirectUrl = "{{ url('mainPage') }}" + "?rack_id=" + data.rack_id + "&message=success";
                            console.log("Redirecting to:", redirectUrl);
                            window.location.href = redirectUrl;
                        }, 3000);
                    }
                })
                .catch(error => console.error("Error fetching rack data:", error));
        }

        // 🔁 Check every 3 seconds for new rack scan
        setInterval(fetchRackData, 3000);
    });
</script>


@endsection

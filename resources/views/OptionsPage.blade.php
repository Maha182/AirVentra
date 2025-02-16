@extends('layouts.home.app')
@section('content')
       

     
        <div id="features" class="container-fluid feature bg-light py-5">
            <div class="container py-5">
            <div class="container-fluid blog py-5">
            <h4 class="display-4 "> Airventra Control Panel</h4>
            <div class="container py-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h3 style="color: navy;"> Select a feature to begin optimizing your operations: </h3>
                </div> 
                <div class="row g-4 justify-content-center">
                    <div class="col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="blog-item">
                            <a href="{{ route('storage-assignment') }}">
                                <div class="blog-img">
                                    <img src="img\homepageimg\st.png" class="img-fluid rounded-top w-100" alt="Smart Storage Assignment">
                                </div>
                            </a>
                            <div class="blog-content p-4">
                                <a href="{{ route('storage-assignment') }}" class="h4 d-inline-block mb-3">Assign Storage</a>
                                <p class="mb-3">Automatically assign storage zones and racks using AI-powered algorithms.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.4s">
                        <div class="blog-item">
                            <a href="{{ route('mainPage') }}">
                                <div class="blog-img">
                                    <img src="img\homepageimg\as.png" class="img-fluid rounded-top w-100" alt="Real-Time Inventory Tracking">
                                </div>
                            </a>
                            <div class="blog-content p-4">
                                <a href="{{ route('mainPage') }}" class="h4 d-inline-block mb-3">Scan Inventory</a>
                                <p class="mb-3">Verify product placement to minimize costly mistakes. Update inventory levels and detect over/understock issues.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.6s">
                        <div class="blog-item">
                            <a href="{{ route('dashboard') }}">
                                <div class="blog-img">
                                    <img src="img\homepageimg\dash.png" class="img-fluid rounded-top w-100" alt="Error Detection">
                                </div>
                            </a>
                            <div class="blog-content p-4">
                                <a href="{{ route('dashboard') }}" class="h4 d-inline-block mb-3">Dashboard</a>
                                <p class="mb-3">Monitor warehouse performance, track placement and inventory errors, and gain real-time insights with interactive reports</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
            </div>
        </div>
       

@endsection


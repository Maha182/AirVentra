<!-- Spinner Start -->
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar & Hero Start -->
        <div class="container-fluid nav-bar px-0 px-lg-4 py-lg-0">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light"> 
                <a href="#" class="navbar-brand p-0">
                    <img src="img\logo_b.png" alt="Logo" class="img-fluid" style="max-height: 100px;"> <!-- Adjust max-height as needed -->
                </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                        <span class="fa fa-bars"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <div class="navbar-nav mx-0 mx-lg-auto">
                            <a href="#home" class="nav-item nav-link active">Home</a>
                            <a href="#pricing" class="nav-item nav-link">Pricing</a>
                            <a href="#benefits" class="nav-item nav-link">Benefits</a>
                            <a href="#features" class="nav-item nav-link">Features</a>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link" data-bs-toggle="dropdown">
                                    <span class="dropdown-toggle">Pages</span>
                                </a>
                                <div class="dropdown-menu">
                                    <a href="feature.html" class="dropdown-item">Our Features</a>
                                    <a href="team.html" class="dropdown-item">Our team</a>
                                    <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                                    <a href="FAQ.html" class="dropdown-item">FAQs</a>
                                    <a href="404.html" class="dropdown-item">404 Page</a>
                                </div>
                            </div>
                            <a href="#contact" class="nav-item nav-link">Contact</a>
                            <div class="nav-btn px-3">
                                <a href="{{route('login')}}" class="btn btn-primary rounded-pill py-2 px-4 ms-3 flex-shrink-0">Login</a>
                            </div>
                        </div>
                    </div>


                </nav>
            </div>
        </div>
        <!-- Navbar & Hero End -->

        <!-- Modal Search Start -->
        <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex align-items-center bg-primary">
                        <div class="input-group w-75 mx-auto d-flex">
                            <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                            <span id="search-icon-1" class="btn bg-light border nput-group-text p-3"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Search End -->
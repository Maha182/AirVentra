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

                    <!-- Login/Logout Button -->
                    @guest
                    <div class="nav-btn px-3">
                        <a href="{{ route('login') }}" class="btn btn-primary rounded-pill py-2 px-4 ms-3 flex-shrink-0">Login</a>
                    </div>
                    @endguest

                    @auth
                    <div class="nav-btn px-3">
                        <!-- Logout Button -->
                        <a href="{{ route('logout') }}" class="btn btn-danger rounded-pill py-2 px-4 ms-3 flex-shrink-0"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                    </div>

                    <!-- Logout Form -->
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    @endauth

                </div>
            </div>
        </nav>
    </div>
</div>
<!-- Navbar & Hero End -->

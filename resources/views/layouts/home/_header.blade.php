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
            <div class="navbar-nav mx-0 mx-lg- d-flex" >
                    <a href="{{ route('home') }}" class="nav-item nav-link active">Home</a>
                    <a href="{{ route('home') }}#pricing" class="nav-item nav-link">Pricing</a>
                    <a href="{{ route('home') }}#benefits" class="nav-item nav-link">Benefits</a>
                    <a href="{{ route('home') }}#features" class="nav-item nav-link">Features</a>
                    <a href="{{ route('OptionsPage') }}" class="nav-item nav-link ms-3" style="white-space: nowrap;">Control Panel</a>
                    <a href="#" class="nav-link" data-bs-toggle="dropdown">
                        <span class="dropdown-toggle">Pages</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="{{ route('home') }}#features" class="dropdown-item">Our Features</a>
                        <a href="{{ route('home') }}#contact" class="dropdown-item">Our team</a>
                        <a href="{{ route('home') }}#pricing"  class="dropdown-item"> Pricing</a>
                        <a href="{{ route('home') }}#FAQ" class="dropdown-item">FAQs</a>
                    </div>
                </div>
               
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

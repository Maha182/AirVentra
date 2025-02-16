@extends('layouts.home.app')
@section('content')
        <!-- Carousel Start -->
        <div id="home" class="header-carousel owl-carousel">
            <div class="header-carousel-item bg-primary">
                <div class="carousel-caption">
                    <div class="container">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-7 animated fadeInLeft">
                                <div class="text-sm-center text-md-start">
                                    <h3 class="display-1 text-white mb-6">Revolutionizing Warehouse Management with Airventra</h3>
                                    <p class="mb-5 fs-5">Smart storage assignment. Drone-driven precision. Efficiency redefined.</p>
                                    <div class="d-flex justify-content-center justify-content-md-start flex-shrink-0 mb-4">
                                        <a class="btn btn-light rounded-pill py-3 px-4 px-md-5 me-2" href="{{ route('home') }}#features">Explore Features</a>
                                        <a class="btn btn-dark rounded-pill py-3 px-4 px-md-5 ms-2" href="{{ route('home') }}#contact">Contact us</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 animated fadeInRight">
                                <div class="calrousel-img" style="object-fit: cover;">
                                    <img src="img\homepageimg\drone1.png" class="img-fluid w-100" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-carousel-item bg-primary">
                <div class="carousel-caption">
                    <div class="container">
                        <div class="row gy-4 gy-lg-0 gx-0 gx-lg-5 align-items-center">
                            <div class="col-lg-5 animated fadeInLeft">
                                <div class="calrousel-img">
                                    <img src="img\homepageimg\drone2.png" class="img-fluid w-100" alt="">
                                </div>
                            </div>
                            <div class="col-lg-7 animated fadeInRight">
                                <div class="text-sm-center text-md-end">
                                <h2 class="display-1 text-white mb-4">Revolutionizing Warehouse Management with Airventra</h2>
                                <p class="mb-5 fs-5">Smart storage assignment. Drone-driven precision. Efficiency redefined.</p>
                                    <div class="d-flex justify-content-center justify-content-md-end flex-shrink-0 mb-4">
                                        <a class="btn btn-light rounded-pill py-3 px-4 px-md-5 me-2"href="{{ route('home') }}#features">Explore Features</a>
                                        <a class="btn btn-dark rounded-pill py-3 px-4 px-md-5 ms-2" href="{{ route('home') }}#contact">Contact us</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Carousel End -->

        <!-- Feature Start -->
        <div id="features" class="container-fluid feature bg-light py-5">
            <div class="container py-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h1 class="display-4 mb-4"> What is Airventra?</h1>
                    <p class="mb-0">Airventra is an innovative warehouse management solution designed to streamline operations through intelligent storage assignment and drone-powered inventory tracking. Our software combines cutting-edge AI algorithms with autonomous drone technology to reduce errors, improve efficiency, and make warehouse management smarter than ever.
                    </p>
                </div>
            <div class="container-fluid blog py-5">

            <div class="container py-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h1 class="display-4 mb-4"> Our Features  </h1>
                </div> 
                <div class="row g-4 justify-content-center">
                    <div class="col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="blog-item">
                            <div class="blog-img">
                                <img src="img\homepageimg\Smart Storage Assignment1.png" class="img-fluid rounded-top w-100" alt="">
                                
                            </div>
                            <div class="blog-content p-4">
                            <div class="blog-content p-4">
                                <a href="#" class="h4 d-inline-block mb-3">Smart Storage Assignment</a>
                                <p class="mb-3">Automatically assign storage zones and racks using AI-powered algorithms.</p>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.4s">
                        <div class="blog-item">
                            <div class="blog-img">
                                <img src="img\homepageimg\Real-Time Inventory Tracking1.png " class="img-fluid rounded-top w-100" alt="">
                            </div>
                            <div class="blog-content p-4">
                                <div class="blog-comment d-flex justify-content-between mb-3">
                                <div class="blog-content p-4">
                                    <a href="#" class="h4 d-inline-block mb-3"> Real-Time Inventory Tracking</a>
                                    <p class="mb-3">Keep your inventory accurate with autonomous drone scans.</p>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.6s">
                        <div class="blog-item">
                            <div class="blog-img">
                                <img src="img\homepageimg\Error Detection1.png" class="img-fluid rounded-top w-100" alt="">
                                
                            </div>
                            <div class="blog-content p-4">
                                <div class="blog-comment d-flex justify-content-between mb-3">
                                <div class="blog-content p-4">
                                    <a href="#" class="h4 d-inline-block mb-3">Error Detection</a>
                                    <p class="mb-3">Verify product placement to minimize costly mistakes.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
            </div>
        </div>
        <!-- Feature End -->
        
        <div id="benefits" class="container-fluid feature bg-light py-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h1 class="display-4 mb-4" > Why Airventra is a Game-Changer  </h1>
            </div> 


            <div class="container-fluid blog py-5">
    <div class="container py-5">
        <div class="row g-4 justify-content-center">
            <?php
            $cards = [
                ["img\homepageimg\Streamlined Operations1.png", "Streamlined Operations", "Simplify warehouse processes with intelligent storage assignment and automation."],
                ["img\homepageimg\Real-Time Accuracy1.png", "Real-Time Accuracy", "Keep your inventory updated instantly with drone-powered tracking."],
                ["img\homepageimg\Error Reduction1.png", "Error Reduction", "Detect misplaced products and prevent costly human errors."],
                ["img\homepageimg\o3.png", "Optimized Storage", "Maximize space usage by assigning products to the right zones and racks."],
                ["img\homepageimg\c2.png", "Cost Efficiency", "Reduce operational costs by improving accuracy and saving time on manual checks."],
                ["img\homepageimg\s1.png", "Sustainability", "Embrace smarter resource use and energy-efficient drones to reduce waste."]
            ];

            foreach ($cards as $index => $card) {
                $delay = 0.2 + ($index * 0.2);
            ?>
                <div class="col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="<?= $delay ?>s">
                    <div class="blog-item">
                        <div class="blog-img">
                            <img src="<?= $card[0] ?>" class="img-fluid rounded-top w-100" alt="">
                        </div>
                        <div class="blog-content p-4">
                            <a href="#" class="h4 d-inline-block mb-3"><?= $card[1] ?></a>
                            <p class="mb-3"><?= $card[2] ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

        </div>
        <!-- About End -->
    <style>
    .team-item {
        background: #004aad; /* Matching Blue Background */
        color: white; /* White text */
        font-weight: bold; 
        border-radius: 10px;
        padding: 20px;
        min-height: 300px; /* Uniform Height */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth Hover */
    }
    .team-item h4 {
        color: white; /* White text */
        font-weight: bold; /* Bold font */
    }
    /* Hover Effect */
    .team-item:hover {
        transform: translateY(-10px); /* Lift the card slightly */
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2); /* Add Shadow */
        background: #00338d; /* Slightly Darker Blue */
    }

    .team-title ul {
        padding-left: 20px;
    }

    .team-title ul li {
        list-style-type: disc;
    }
        </style>
        
        <div  id="pricing" class="container-fluid team pb-5">
        <div class="container pb-5">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
            <h1 class="display-4 mb-4">Subscription Options</h1>
        </div>
        <div class="row g-4">
            <?php
            $plans = [
                ["Basic Plan", "(Small Warehouses)", [
                    "Smart storage assignment.",
                    "Limited drone inventory tracking (2 drones supported).",
                    "Basic reporting and analytics."
                ]],
                ["Standard Plan", "(Medium-Sized Warehouses)", [
                    "Everything in the Basic Plan.",
                    "Support for more drones (5 drones).",
                    "Advanced reporting and analytics tools.",
                    "Error detection for product placement."
                ]],
                ["Premium Plan", "(Large Warehouses)", [
                    "Everything in the Standard Plan.",
                    "Unlimited drones supported.",
                    "Customized AI algorithms for unique warehouse setups.",
                    "Priority customer support and on-site assistance.",
                    "Advanced forecasting tools for inventory trends."
                ]]
            ];

            foreach ($plans as $index => $plan) {
                $delay = 0.2 + ($index * 0.2);
            ?>
                <div class="col-md-6 col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="<?= $delay ?>s">
                    <div class="team-item h-100">
                        <div >
                            <h4 ><?= $plan[0] ?></h4>
                            <p ><strong><?= $plan[1] ?></strong></p>
                            <ul >
                                <?php foreach ($plan[2] as $feature) { ?>
                                    <li><?= $feature ?></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
        </div>



        <!-- Service Start -->
       
        <!-- Service End -->

        <!-- FAQs Start -->
        <div class="container-fluid faq-section bg-light py-5">
            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-xl-6 wow fadeInLeft" data-wow-delay="0.2s">
                        <div class="h-100">
                            <div id="FAQ" class="mb-5">
                                <h4 class="text-primary">Some Important FAQ's</h4>
                                <h1 class="display-4 mb-0">Common Frequently Asked Questions</h1>
                            </div>
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button border-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Q: What happens during Freshers' Week?
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show active" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body rounded">
                                            A: Leverage agile frameworks to provide a robust synopsis for high level overviews. Iterative approaches to corporate strategy foster collaborative thinking to further the overall value proposition. Organically grow the holistic world view of disruptive innovation via workplace diversity and empowerment.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            Q: What is the transfer application process?
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            A: Leverage agile frameworks to provide a robust synopsis for high level overviews. Iterative approaches to corporate strategy foster collaborative thinking to further the overall value proposition. Organically grow the holistic world view of disruptive innovation via workplace diversity and empowerment.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            Q: Why should I attend community college?
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            A: Leverage agile frameworks to provide a robust synopsis for high level overviews. Iterative approaches to corporate strategy foster collaborative thinking to further the overall value proposition. Organically grow the holistic world view of disruptive innovation via workplace diversity and empowerment.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 wow fadeInRight" data-wow-delay="0.4s">
                        <img src="img\homepageimg\FAQ.png" class="img-fluid w-100" alt="">
                    </div>
                </div>
            </div>
        </div>
        <!-- FAQs End -->


        <!-- Team Start -->
        <div id="contact" class="container-fluid team pb-5">
            <div class="container pb-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                    <h1 class="display-4 mb-4">Our Team</h1>
                    <h4 class="text-primary">Meet the Team Behind Airventra </h4>

                </div>
                <div class="row g-3 justify-content-center"> <!-- Reduced gap -->
                    <?php
                    $teamMembers = [
                        [" ", "Noor Alawlaqi", "Developer"],
                        [" ", "Maha Almashharawi", "Developer"],
                        [" ", "Mashael Alsalamah", "Developer"],
                        ["", "Dr. Passent M. ElKafrawy", ""],
                        [" ", "Dr. Naila Marir", ""],
                    ];
                
                    foreach ($teamMembers as $index => $member) {
                        $delay = 0.2 + ($index * 0.2);
                    ?>
                        <div class="col-6 col-md-4 col-lg-3 col-xxl-2 wow fadeInUp" data-wow-delay="<?= $delay ?>s">
                            <div class="team-item text-center"> <!-- Centered text -->
                                <div class="team-img">
                                    <img src="<?= $member[0] ?>" class="img-fluid rounded-circle" alt="<?= $member[1] ?>">
                                </div>
                                <div class="team-title p-3">
                                    <h6 class="mb-1 text-white fw-bold"><?= $member[1] ?></h6> <!-- Smaller, white, bold -->
                                    <p class="mb-0 text-light"><?= $member[2] ?></p> <!-- Lighter text -->
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>


            </div>
        </div>

@endsection


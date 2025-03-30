<x-app-layout :assets="$assets ?? []">
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    <div class="row">
        <!-- Left Side Charts -->
        <div class="col-sm-12 col-lg-6">

            <!-- Task Completion Trend (Line Chart) -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Task Completion Trend</h4>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div id="high-basicline-chart"></div>
                </div>
            </div>

            <!-- Task Distribution by Employee (Bar Chart) -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Task Distribution by Employee</h4>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div id="high-columnndbar-chart"></div>
                </div>
            </div>

            <!-- Task Status Breakdown (Pie Chart) -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Task Status Breakdown</h4>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div id="high-pie-chart"></div>
                </div>
            </div>

            <!-- Live Task Updates (Gauge Chart) -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Live Task Updates</h4>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div id="high-gauges-chart"></div>
                </div>
            </div>

        </div>

        <!-- Right Side Charts -->
        <div class="col-sm-12 col-lg-6">

            <!-- Task Types Distribution (Area Chart) -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Task Types Over Time</h4>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div id="high-area-chart"></div>
                </div>
            </div>

            <!-- Task Completion Time per Employee (Scatter Plot) -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Task Completion Time per Employee</h4>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div id="high-scatterplot-chart"></div>
                </div>
            </div>

            <!-- Assigned vs Completed Tasks (Dual Axes Chart) -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Assigned vs Completed Tasks</h4>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div id="high-linendcolumn-chart"></div>
                </div>
            </div>

            <!-- Workload Distribution (3D Chart) -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Workload Distribution (3D View)</h4>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div id="high-3d-chart"></div>
                </div>
            </div>

            <!-- Delayed vs On-Time Tasks (Bar Chart) -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Delayed vs On-Time Tasks</h4>
                    </div>
                    <div id="filterOptions" class="dropdown">
                        <a href="#" class="text-gray dropdown-toggle" id="dropdownMenuButton22" data-bs-toggle="dropdown" aria-expanded="false">
                            By Date
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end custom-dropdown-menu-end" aria-labelledby="dropdownMenuButton22">
                            <li><a class="dropdown-item filter-option" href="#" data-value="date">By Date</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-value="month">By Month</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-value="employee">By Employee</a></li>
                        </ul>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div id="high-barwithnagative-chart"></div>
                </div>
            </div>

        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/rtl.js"></script>
    <script src="js/customizer.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.appear.js"></script>
    <script src="js/countdown.min.js"></script>
    <script src="js/waypoints.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/wow.min.js"></script>
    <script src="js/apexcharts.js"></script>
    <script src="js/slick.min.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/smooth-scrollbar.js"></script>
    <script src="js/lottie.js"></script>
    <script src="js/highcharts.js"></script>
    <script src="js/highcharts-3d.js"></script>
    <script src="js/highcharts-more.js"></script>
    <script src="js/chart-custom.js"></script>
    <script src="js/custom.js"></script>



</x-app-layout>

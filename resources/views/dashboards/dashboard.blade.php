<!-- Include jQuery before your other scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.29.0"></script>

<script src="{{ asset('js/charts/dashboard.js') }}"></script>

<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="row row-cols-1">
                 <div class="overflow-hidden d-slider1">
                    <?php
                    $data = [
                        ['id' => '01', 'progress' => 95, 'title' => 'Total Scans', 'amount' => ($totalScans >= 1000 ? number_format($totalScans) . 'K' : '0'), 'delay' => 700, 'color' => 'primary', 'svg' => 'M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z'],
                        ['id' => '02', 'progress' => 85, 'title' => 'Correct Placement', 'amount' => ($correctPlacements >= 1000 ? number_format($correctPlacements) . 'K' : '0'), 'delay' => 800, 'color' => 'info', 'svg' => 'M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z'],
                        ['id' => '03', 'progress' => 75, 'title' => 'Misplaced Items', 'amount' => ($misplacedItems >= 1000 ? number_format($misplacedItems) . 'K' : '0'), 'delay' => 900, 'color' => 'primary', 'svg' => 'M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z'],
                        ['id' => '04', 'progress' => 65, 'title' => 'Rack Capacity', 'amount' => $rackCapacity . '%', 'delay' => 1000, 'color' => 'info', 'svg' => 'M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z'],
                        ['id' => '05', 'progress' => 55, 'title' => 'Overstocked Racks', 'amount' => number_format($overstockCount), 'delay' => 1100, 'color' => 'primary', 'svg' => 'M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z'],
                        ['id' => '06', 'progress' => 45, 'title' => 'Understocked Racks', 'amount' => number_format($understockCount), 'delay' => 1200, 'color' => 'info', 'svg' => 'M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z'],
                        ['id' => '07', 'progress' => 35, 'title' => 'Total Products', 'amount' => ($normalCount >= 1000 ? number_format($normalCount) . 'K' : '0'), 'delay' => 1300, 'color' => 'primary', 'svg' => 'M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z']
                    ];
                    ?>

                    <ul class="p-0 m-0 mb-2 swiper-wrapper list-inline">
                        <?php foreach ($data as $item): ?>
                            <li class="col-12 col-md-6 col-lg-2 swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="<?= $item['delay']; ?>">
                                <div class="card-body">
                                    <div class="progress-widget">
                                        <div id="circle-progress-<?= $item['id']; ?>"
                                            class="text-center circle-progress-01 circle-progress circle-progress-<?= $item['color']; ?>"
                                            data-min-value="0" data-max-value="100" data-value="<?= $item['progress']; ?>" data-type="percent">
                                            <svg class="card-slie-arrow icon-24" width="24" viewBox="0 0 24 24">
                                                <path fill="currentColor" d="<?= $item['svg']; ?>" />
                                            </svg>
                                        </div>
                                        <div class="progress-detail">
                                            <p class="mb-2"><?= $item['title']; ?></p>
                                            <h4 class="counter text-center" style="min-height: 30px;"><?= $item['amount']; ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="swiper-button swiper-button-next"></div>
                    <div class="swiper-button swiper-button-prev"></div>
                </div>
        </div>
        <div class="col-md-12 col-lg-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" data-aos="fade-up" data-aos-delay="800">
                        <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                            <div class="header-title">
                                <h4 class="card-title">Misplaced products</h4>
                                <p class="mb-0">Inventory Overview</p>
                            </div>
                            <!-- <div class="d-flex align-items-center align-self-center">
                                <div class="d-flex align-items-center text-primary">
                                    <svg class="icon-12" xmlns="http://www.w3.org/2000/svg" width="12"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                            <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                    <div class="ms-2">
                                        <span class="text-gray">In Stock</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center ms-3 text-info">
                                    <svg class="icon-12" xmlns="http://www.w3.org/2000/svg" width="12"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                            <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                    <div class="ms-2">
                                        <span class="text-gray">Out of Stock</span>
                                    </div>
                                </div>
                            </div> -->
                            <div class="dropdown">
                                <a href="#" class="text-gray dropdown-toggle" id="dropdownMenuButton22"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    This Week
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end custom-dropdown-menu-end" aria-labelledby="dropdownMenuButton22">
                                    <li><a class="dropdown-item" href="#">This Week</a></li>
                                    <li><a class="dropdown-item" href="#">This Month</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="d-main" class="d-main"></div>
                        </div>
                    </div>
                </div>
                

                <div class="col-md-12">
                    <div class="row">
                        <!-- Placement Accuracy -->
                        <div class="col-md-6">
                            <div class="card" data-aos="fade-up" data-aos-delay="900">
                                <div class="flex-wrap card-header d-flex justify-content-between">
                                    <div class="header-title">
                                        <h4 class="card-title">Placement Accuracy</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div id="myChart" class="col-md-8 myChart"
                                            data-correct="{{ $correctCount }}"
                                            data-misplaced="{{ $misplacedCount }}">
                                        </div>
                                        <div class="d-grid gap col-md-4">
                                            <div class="d-flex align-items-start">
                                                <svg class="mt-2 icon-14" xmlns="http://www.w3.org/2000/svg" width="14"
                                                    viewBox="0 0 24 24" fill="#3a57e8">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8" fill="#3a57e8"></circle>
                                                    </g>
                                                </svg>
                                                <div class="ms-3">
                                                    <span class="text-gray">Correct</span>
                                                    <h6>{{ number_format($correctCount) }}</h6>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-start">
                                                <svg class="mt-2 icon-14" xmlns="http://www.w3.org/2000/svg" width="14"
                                                    viewBox="0 0 24 24" fill="#f44336">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8" fill="#f44336"></circle>
                                                    </g>
                                                </svg>
                                                <div class="ms-3">
                                                    <span class="text-gray">Misplaced</span>
                                                    <h6>{{ number_format($misplacedCount) }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rack Capacity Scans -->
                        <div class="col-md-6">
                            <div class="card" data-aos="fade-up" data-aos-delay="1000">
                                <div class="flex-wrap card-header d-flex justify-content-between">
                                    <div class="header-title">
                                        <h4 class="card-title">Rack Capacity Scans</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="d-activity" 
                                        class="d-activity"
                                        data-overstock="{{ $overstockCount }}" 
                                        data-understock="{{ $understockCount }}" 
                                        data-normal="{{ $normalCount }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>      


            </div>
        </div>
    </div>

                <!-- tabels -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h2 class="card-title">Placment Error Report:</h2>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="custom-datatable-entries">
                                    <table id="datatable" class="table table-striped" data-toggle="data-table">
                                        <thead>
                                            <tr>
                                            <th>Product ID</th>
                                            <th>Scan Date</th>
                                            <th>Wrong Location</th>
                                            <th>Correct Location</th>
                                            <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($locationChecks as $check)
                                            <tr>
                                                <td>{{ $check->product_id }}</td>
                                                <td>{{ $check->scan_date }}</td>
                                                <td>{{ $check->wrong_location }}</td>
                                                <td>{{ $check->correct_location }}</td>
                                                <td>{{ $check->status }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Product ID</th>
                                                <th>Scan Date</th>
                                                <th>Wrong Location</th>
                                                <th>Correct Location</th>
                                                <th>Status</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h2 class="card-title">Inventory Level Report:</h2>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="custom-datatable-entries">
                                    <table id="datatable2" class="table table-striped" data-toggle="data-table">
                                        <thead>
                                            <tr>
                                                <th>Location ID</th>
                                                <th>Scan Date</th>
                                                <th>Detected Capacity</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($locationCapacityChecks as $capacityCheck)
                                                <tr>
                                                    <td>{{ $capacityCheck->location_id }}</td>
                                                    <td>{{ $capacityCheck->scan_date }}</td>
                                                    <td>{{ $capacityCheck->detected_capacity }}</td>
                                                    <td>{{ $capacityCheck->status }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Location ID</th>
                                                <th>Scan Date</th>
                                                <th>Detected Capacity</th>
                                                <th>Status</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>

            </div>



                        
                    
</x-app-layout>



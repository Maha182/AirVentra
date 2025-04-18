<!-- Include jQuery before your other scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.29.0"></script>

<script src="{{ asset('js/charts/dashboard.js') }}"></script>

<x-app-layout :assets="$assets ?? []">

    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="row row-cols-1">
                <div class="overflow-hidden d-slider1">
                    <ul class="p-0 m-0 mb-2 swiper-wrapper list-inline">
                        @php
                            $data = [
                                ['id' => '01', 'title' => 'Total Scans', 'amount' => ($totalScans >= 1000 ? number_format($totalScans) : $formattedTotalScans), 'delay' => 700, 'color' => 'primary', 'icon' => 'total_scans_icon.png'],
                                ['id' => '03', 'title' => 'Misplaced Items', 'amount' => ($misplacedCount >= 1000 ? number_format($misplacedCount) : $formattedMisplacedItems), 'delay' => 900, 'color' => 'primary', 'icon' => 'misplaced_items_icon.png'],
                                ['id' => '05', 'title' => 'Overstocked Racks', 'amount' => number_format($inventoryStats->overfilled ), 'delay' => 1100, 'color' => 'primary', 'icon' => 'overstocked_racks_icon.png'],
                                ['id' => '06', 'title' => 'Understocked Racks', 'amount' => number_format($inventoryStats->underfilled ), 'delay' => 1200, 'color' => 'info', 'icon' => 'understocked_racks_icon.png'],
                                ['id' => '07', 'title' => 'Total Products', 'amount' => ($totalProduct >= 1000 ? number_format($totalProduct) : $totalProduct), 'delay' => 1300, 'color' => 'primary', 'icon' => 'total_products_icon.png']
                            ];
                        @endphp

                        @foreach ($data as $item)
                            <li class="col-12 col-md-6 col-lg-2 swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="{{ $item['delay'] }}">
                                <div class="card-body">
                                    <div class="progress-widget">
                                        <!-- Add the icon here -->
                                        <div class="icon-container text-center mb-2">
                                            <img src="{{ asset('images/' . $item['icon']) }}" alt="{{ $item['title'] }} Icon" style="width: 40px; height: 40px;">
                                        </div>
                                        <div class="progress-detail">
                                            <p class="mb-2">{{ $item['title'] }}</p>
                                            <h4 class="counter text-center" style="min-height: 30px;">{{ $item['amount'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="swiper-button swiper-button-next"></div>
                    <div class="swiper-button swiper-button-prev"></div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="card" data-aos="fade-up" data-aos-delay="1000">
                        <div class="flex-wrap card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Warehouse Capacity Utilization</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="capacityChart" class="capacityChart"
                                data-used="{{ $currentCapacity }}"
                                data-free="{{ $totalCapacity - $currentCapacity }}">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Zone Error Distribution -->
                <div class="col-md-6">
                        <div class="card" data-aos="fade-up" data-aos-delay="1200">
                            <div class="flex-wrap card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Zone Error Distribution</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="zoneErrorChart" class="zoneErrorChart"
                                    data-zone-errors='@json($zoneErrorData)'>
                                </div>
                            </div>
                        </div>
                </div>
                <!-- </div> -->
                <div class="col-md-12">
                        <div class="card" data-aos="fade-up" data-aos-delay="800">
                            <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                                <div class="header-title">
                                    <h4 class="card-title">Misplaced products</h4>
                                    <p class="mb-0">Inventory Overview</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="d-main" class="d-main" data-misplaced='@json(["months" => $months, "totals" => $totals])'></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card" data-aos="fade-up" data-aos-delay="1100">
                                <div class="flex-wrap card-header d-flex justify-content-between">
                                    <div class="header-title">
                                        <h4 class="card-title">Top Problematic Locations</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="problematicChart" class="problematicChart"
                                        data-problematic='@json($topProblematicLocations)'>
                                    </div>
                                </div>
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
                                            data-overstock="{{ $inventoryStats->overfilled }}"
                                            data-understock="{{ $inventoryStats->underfilled }}"
                                            data-normal="{{ $inventoryStats->normal }}">
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>      


            </div>
        </div>

        <div class="col-md-12 col-lg-12">
                <!-- tabels -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card" data-aos="fade-up" data-aos-delay="800">
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
                        <div class="card" data-aos="fade-up" data-aos-delay="800">
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
    </div>



                        
                    
</x-app-layout>


